<?php

namespace App\Http\Controllers;

use App\Models\CaseDocument;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArchiveController extends Controller
{
    public function index(Request $request): View
    {
        $query = CaseDocument::query()->with(['serviceCase.customer', 'uploadedBy'])->latest();

        if (!$request->user()->isAdmin()) {
            $query->whereHas('serviceCase', fn ($caseQuery) => $caseQuery->where('assigned_to', $request->user()->id));
        }

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(fn ($builder) => $builder
                ->where('document_type', 'like', "%{$search}%")
                ->orWhereHas('serviceCase', fn ($caseQuery) => $caseQuery->where('file_number', 'like', "%{$search}%"))
                ->orWhereHas('serviceCase.customer', fn ($customerQuery) => $customerQuery->where('name', 'like', "%{$search}%")));
        }

        return view('archives.index', ['documents' => $query->paginate(15)->withQueryString()]);
    }
}
