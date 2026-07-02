<div class="grid gap-5 md:grid-cols-2">
<label class="text-sm font-semibold">Nama Nasabah / Perusahaan<input class="form-input mt-1.5" name="name" value="{{ old('name',$customer?->name) }}" required></label>
<label class="text-sm font-semibold">Nomor Telepon<input class="form-input mt-1.5" name="phone" value="{{ old('phone',$customer?->phone) }}"></label>
<label class="text-sm font-semibold">NIK<input class="form-input mt-1.5" name="nik" inputmode="numeric" placeholder="Diubah bila perlu" value="{{ old('nik') }}"><span class="mt-1 block text-xs font-normal text-slate-400">Nilai lama tidak ditampilkan demi keamanan.</span></label>
<label class="text-sm font-semibold">Nomor Rekening<input class="form-input mt-1.5" name="account_number" inputmode="numeric" placeholder="Diubah bila perlu" value="{{ old('account_number') }}"><span class="mt-1 block text-xs font-normal text-slate-400">Nilai lama tidak ditampilkan demi keamanan.</span></label>
<label class="text-sm font-semibold md:col-span-2">Email<input class="form-input mt-1.5" name="email" type="email" value="{{ old('email',$customer?->email) }}"></label>
</div>
