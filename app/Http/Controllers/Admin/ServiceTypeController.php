<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceTypeController extends Controller
{
    public function index(): View { return view('service-types.index',['serviceTypes'=>ServiceType::query()->withCount('serviceCases')->orderBy('name')->paginate(15)]); }
    public function create(): View { return view('service-types.create'); }
    public function store(Request $request, AuditLogger $audit): RedirectResponse
    { $data=$this->validateData($request); $type=ServiceType::create($data); $audit->log($request,'service_type','create',$type,null,$this->auditValues($type),'Admin menambahkan jenis layanan.'); return redirect()->route('admin.service-types.index')->with('success','Jenis layanan berhasil ditambahkan.'); }
    public function edit(ServiceType $serviceType): View { return view('service-types.edit',compact('serviceType')); }
    public function update(Request $request, ServiceType $serviceType, AuditLogger $audit): RedirectResponse
    { $before=$this->auditValues($serviceType); $serviceType->update($this->validateData($request,$serviceType)); $audit->log($request,'service_type','update',$serviceType,$before,$this->auditValues($serviceType),'Admin memperbarui jenis layanan.'); return redirect()->route('admin.service-types.index')->with('success','Jenis layanan berhasil diperbarui.'); }
    public function destroy(Request $request, ServiceType $serviceType, AuditLogger $audit): RedirectResponse
    { $before=$this->auditValues($serviceType); $serviceType->update(['is_active'=>false]); $audit->log($request,'service_type','deactivate',$serviceType,$before,$this->auditValues($serviceType),'Admin menonaktifkan jenis layanan. Histori berkas tetap dipertahankan.'); return back()->with('success','Jenis layanan dinonaktifkan. Riwayat berkas tidak dihapus.'); }
    public function updateStatus(Request $request, ServiceType $serviceType, AuditLogger $audit): RedirectResponse
    { $before=$this->auditValues($serviceType); $serviceType->update(['is_active'=>$request->boolean('is_active')]); $audit->log($request,'service_type','update_status',$serviceType,$before,$this->auditValues($serviceType),'Admin mengubah status jenis layanan.'); return back()->with('success','Status jenis layanan diperbarui.'); }
    private function validateData(Request $request, ?ServiceType $serviceType=null): array
    { $data=$request->validate(['name'=>['required','string','max:150',Rule::unique('service_types','name')->ignore($serviceType?->id)],'sla_hours'=>['required','integer','min:1','max:720'],'required_documents_text'=>['nullable','string','max:4000']]); return ['name'=>$data['name'],'sla_hours'=>$data['sla_hours'],'required_documents'=>collect(preg_split('/\r\n|\r|\n/',$data['required_documents_text']??''))->map(fn($item)=>trim($item))->filter()->values()->all()]; }
    /** @return array<string,mixed> */ private function auditValues(ServiceType $serviceType): array { return ['name'=>$serviceType->name,'sla_hours'=>$serviceType->sla_hours,'required_documents'=>$serviceType->required_documents,'is_active'=>$serviceType->is_active]; }
}
