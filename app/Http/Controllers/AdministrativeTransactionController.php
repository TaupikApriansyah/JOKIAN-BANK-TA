<?php

namespace App\Http\Controllers;

use App\Enums\CaseStatus;
use App\Enums\TransactionStatus;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\AdministrativeTransaction;
use App\Models\ServiceCase;
use App\Services\AuditLogger;
use App\Services\ReferenceNumberService;
use App\Services\TransactionAccountResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdministrativeTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = AdministrativeTransaction::query()->with(['customer', 'serviceCase', 'createdBy', 'verifiedBy'])->latest();
        if (!$request->user()->isAdmin()) $query->where('created_by', $request->user()->id);
        if ($request->filled('status')) $query->where('status', $request->string('status')->value());
        if ($search = $request->string('q')->trim()->value()) {
            $query->where(fn ($builder) => $builder->where('transaction_number', 'like', "%{$search}%")->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%"))->orWhereHas('serviceCase', fn ($case) => $case->where('file_number', 'like', "%{$search}%")));
        }
        return view('transactions.index', ['transactions' => $query->paginate(15)->withQueryString()]);
    }

    public function show(Request $request, AdministrativeTransaction $transaction): View
    {
        abort_unless($request->user()->isAdmin() || $transaction->created_by === $request->user()->id, 403);
        $transaction->load(['customer', 'serviceCase.serviceType', 'createdBy', 'verifiedBy', 'journals', 'correctionRequests']);
        return view('transactions.show', compact('transaction'));
    }

    public function store(StoreTransactionRequest $request, ServiceCase $serviceCase, ReferenceNumberService $references, TransactionAccountResolver $accounts, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeMakerForCase($request, $serviceCase);
        abort_if(in_array($serviceCase->status, [CaseStatus::Selesai, CaseStatus::Ditolak], true), 422, 'Berkas tidak dapat ditambahkan transaksi.');
        $this->guardAgainstPendingDuplicate($serviceCase, $request->string('category')->value());
        $proofPath = $request->hasFile('proof') ? $request->file('proof')->store("private/transaction-proofs/{$serviceCase->file_number}", 'local') : null;
        $status = $request->input('action') === 'submit' ? TransactionStatus::MenungguVerifikasi : TransactionStatus::Draft;
        $mapped = $accounts->resolve($request->string('category')->value());

        $transaction = DB::transaction(function () use ($request, $serviceCase, $references, $mapped, $proofPath, $status) {
            $transaction = AdministrativeTransaction::create([
                'transaction_number' => 'PENDING-'.Str::uuid(),
                'service_case_id' => $serviceCase->id,
                'customer_id' => $serviceCase->customer_id,
                'created_by' => $request->user()->id,
                'category' => $request->string('category')->value(),
                'payment_method' => $request->string('payment_method')->value(),
                'amount' => $request->input('amount'),
                'debit_account' => $mapped['debit_account'],
                'credit_account' => $mapped['credit_account'],
                'description' => $request->input('description'),
                'proof_path' => $proofPath,
                'status' => $status,
                'submitted_at' => $status === TransactionStatus::MenungguVerifikasi ? now() : null,
            ]);

            $transaction->update(['transaction_number' => $references->transactionNumber($transaction)]);

            return $transaction;
        });

        $audit->log($request, 'transaction', $status===TransactionStatus::Draft?'create_draft':'create_submit', $transaction, null, $this->auditValues($transaction), 'Transaksi administrasi dibuat oleh Maker.');
        return redirect()->route('cases.show', $serviceCase)->with('success', $status===TransactionStatus::Draft?'Transaksi disimpan sebagai draft.':'Transaksi berhasil diajukan dan menunggu verifikasi Admin.');
    }

    public function edit(Request $request, AdministrativeTransaction $transaction): View
    {
        $this->authorizeEditableTransaction($request, $transaction);
        return view('transactions.edit', compact('transaction'));
    }

    public function update(UpdateTransactionRequest $request, AdministrativeTransaction $transaction, TransactionAccountResolver $accounts, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeEditableTransaction($request, $transaction);
        $before = $this->auditValues($transaction); $mapped = $accounts->resolve($request->string('category')->value()); $proofPath = $transaction->proof_path;
        if ($request->hasFile('proof')) $proofPath = $request->file('proof')->store("private/transaction-proofs/{$transaction->serviceCase->file_number}", 'local');
        $transaction->update(['category'=>$request->string('category')->value(),'payment_method'=>$request->string('payment_method')->value(),'amount'=>$request->input('amount'),'debit_account'=>$mapped['debit_account'],'credit_account'=>$mapped['credit_account'],'description'=>$request->input('description'),'proof_path'=>$proofPath,'status'=>TransactionStatus::Draft,'verified_by'=>null,'verified_at'=>null,'verification_note'=>null]);
        $audit->log($request,'transaction','update_draft',$transaction,$before,$this->auditValues($transaction),'Maker memperbarui draft transaksi.');
        return redirect()->route('transactions.show',$transaction)->with('success','Draft transaksi berhasil diperbarui.');
    }

    public function submit(Request $request, AdministrativeTransaction $transaction, AuditLogger $audit): RedirectResponse
    {
        $this->authorizeEditableTransaction($request,$transaction);
        $before=$this->auditValues($transaction); $this->guardAgainstPendingDuplicate($transaction->serviceCase,$transaction->category,$transaction->id);
        $transaction->update(['status'=>TransactionStatus::MenungguVerifikasi,'submitted_at'=>now(),'verified_by'=>null,'verified_at'=>null,'verification_note'=>null]);
        $audit->log($request,'transaction','submit',$transaction,$before,$this->auditValues($transaction),'Transaksi diajukan ulang untuk verifikasi.');
        return back()->with('success','Transaksi diajukan untuk verifikasi Admin.');
    }

    public function destroy(Request $request, AdministrativeTransaction $transaction, AuditLogger $audit): RedirectResponse
    {
        abort_unless($request->user()->isCustomerService() && $transaction->created_by === $request->user()->id, 403);
        abort_unless(in_array($transaction->status,[TransactionStatus::Draft,TransactionStatus::Dikembalikan],true),422,'Hanya draft atau transaksi yang dikembalikan yang dapat dibatalkan.');
        $before=$this->auditValues($transaction);
        $transaction->update(['status'=>TransactionStatus::Dibatalkan,'cancelled_at'=>now()]);
        $audit->log($request,'transaction','cancel',$transaction,$before,$this->auditValues($transaction),'Maker membatalkan draft transaksi. Data tetap disimpan untuk audit.');
        return redirect()->route('transactions.index')->with('success','Transaksi dibatalkan. Riwayat tetap tersimpan untuk audit.');
    }

    private function authorizeMakerForCase(Request $request, ServiceCase $serviceCase): void { abort_unless($request->user()->isCustomerService() && $serviceCase->assigned_to===$request->user()->id,403); }
    private function authorizeEditableTransaction(Request $request, AdministrativeTransaction $transaction): void { abort_unless($request->user()->isCustomerService() && $transaction->created_by===$request->user()->id,403); abort_unless(in_array($transaction->status,[TransactionStatus::Draft,TransactionStatus::Dikembalikan],true),422,'Hanya draft atau transaksi yang dikembalikan yang dapat diubah.'); }
    private function guardAgainstPendingDuplicate(ServiceCase $serviceCase,string $category,?int $ignoreId=null): void { $query=$serviceCase->transactions()->where('category',$category)->where('status',TransactionStatus::MenungguVerifikasi->value); if($ignoreId!==null)$query->whereKeyNot($ignoreId); abort_if($query->exists(),422,'Masih ada transaksi dengan kategori yang sama menunggu verifikasi.'); }
    /** @return array<string,mixed> */
    private function auditValues(AdministrativeTransaction $transaction): array { return ['transaction_number'=>$transaction->transaction_number,'category'=>$transaction->category,'payment_method'=>$transaction->payment_method,'amount'=>$transaction->amount,'status'=>$transaction->status->value]; }
}
