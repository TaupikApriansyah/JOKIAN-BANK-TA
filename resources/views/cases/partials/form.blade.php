@php($selectedCustomerId = old('customer_id', $serviceCase?->customer_id ?? ($selectedCustomerId ?? null)))
<div class="grid gap-5 md:grid-cols-2">
  <label class="text-sm font-semibold">Nasabah
    <select name="customer_id" class="form-input mt-1.5 @error('customer_id') border-red-400 ring-1 ring-red-200 @enderror" required>
      <option value="" disabled @selected(blank($selectedCustomerId))>Pilih nasabah</option>
      @foreach($customers as $customer)
        <option value="{{ $customer->id }}" @selected((int) $selectedCustomerId === $customer->id)>{{ $customer->name }} · {{ $customer->customer_number }}</option>
      @endforeach
    </select>
    @error('customer_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
  </label>
  <label class="text-sm font-semibold">Jenis Layanan
    <select name="service_type_id" class="form-input mt-1.5 @error('service_type_id') border-red-400 ring-1 ring-red-200 @enderror" required>
      <option value="" disabled @selected(blank(old('service_type_id', $serviceCase?->service_type_id)))>Pilih jenis layanan</option>
      @foreach($serviceTypes as $type)
        <option value="{{ $type->id }}" @selected((int) old('service_type_id', $serviceCase?->service_type_id) === $type->id)>{{ $type->name }} · SLA {{ $type->sla_hours }} jam</option>
      @endforeach
    </select>
    @error('service_type_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
  </label>
  <label class="text-sm font-semibold md:col-span-2">Tanggal & Jam Berkas Masuk
    <input name="received_at" type="datetime-local" class="form-input mt-1.5 @error('received_at') border-red-400 ring-1 ring-red-200 @enderror" value="{{ old('received_at',$serviceCase?->received_at?->format('Y-m-d\\TH:i') ?? now()->format('Y-m-d\\TH:i')) }}" required>
    @error('received_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
  </label>
  <label class="text-sm font-semibold md:col-span-2">Catatan Layanan
    <textarea name="notes" rows="4" class="form-input mt-1.5 @error('notes') border-red-400 ring-1 ring-red-200 @enderror" placeholder="Catatan awal kebutuhan layanan nasabah (opsional)">{{ old('notes',$serviceCase?->notes) }}</textarea>
    @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
  </label>
</div>
