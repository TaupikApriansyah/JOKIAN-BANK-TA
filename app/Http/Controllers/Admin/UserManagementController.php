<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View { return view('admin.users.index',['users'=>User::query()->latest()->paginate(15)]); }
    public function store(StoreUserRequest $request, AuditLogger $audit): RedirectResponse
    {
        $user=User::create($request->safe()->only(['employee_id','name','email','role','password']));
        $audit->log($request,'user_management','create',$user,null,$this->auditValues($user),'Admin membuat akun pengguna baru.');
        return back()->with('success','Pengguna baru berhasil dibuat.');
    }
    public function edit(User $user): View { return view('admin.users.edit',compact('user')); }
    public function update(Request $request, User $user, AuditLogger $audit): RedirectResponse
    {
        if($user->id===$request->user()->id && $request->input('role')!==$user->role->value) return back()->with('error','Admin tidak dapat mengubah rolenya sendiri.');
        $data=$request->validate(['employee_id'=>['required','string','max:50',Rule::unique('users','employee_id')->ignore($user->id)],'name'=>['required','string','max:150'],'email'=>['required','email','max:150',Rule::unique('users','email')->ignore($user->id)],'role'=>['required',Rule::in(['cs','admin'])],'password'=>['nullable','confirmed','min:8']]);
        $before=$this->auditValues($user); $update=collect($data)->except('password')->all(); if(filled($data['password']??null))$update['password']=$data['password']; $user->update($update);
        $audit->log($request,'user_management','update',$user,$before,$this->auditValues($user),'Admin memperbarui data akun pengguna.');
        return redirect()->route('admin.users.index')->with('success','Data pengguna berhasil diperbarui.');
    }
    public function updateStatus(UpdateUserStatusRequest $request, User $user, AuditLogger $audit): RedirectResponse
    {
        abort_if($user->id===$request->user()->id,422,'Admin tidak dapat menonaktifkan akunnya sendiri.');
        $before=$this->auditValues($user); $user->update(['is_active'=>$request->boolean('is_active')]);
        $audit->log($request,'user_management','update_status',$user,$before,$this->auditValues($user),'Admin mengubah status akun pengguna.');
        return back()->with('success','Status akun berhasil diperbarui.');
    }
    /** @return array<string,mixed> */ private function auditValues(User $user): array { return ['employee_id'=>$user->employee_id,'name'=>$user->name,'email'=>$user->email,'role'=>$user->role->value,'is_active'=>$user->is_active]; }
}
