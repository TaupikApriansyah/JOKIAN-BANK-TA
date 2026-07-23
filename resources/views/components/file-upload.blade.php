@props(['name' => 'document', 'id' => 'file-upload', 'label' => 'Pilih dokumen'])
<div class="bank-upload-card" data-upload-card>
  <div class="upload-header">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M7 10V9a5 5 0 0 1 10 0v1"></path><path d="M6 10a3 3 0 0 0-1 5.83"></path><path d="M18 10a3 3 0 0 1 1 5.83"></path><path d="M12 12v8"></path><path d="m9 15 3-3 3 3"></path></svg>
    <p>Tarik file atau pilih dokumen</p><small>PDF, JPG, PNG, DOC, DOCX · Maks. 5 MB</small>
  </div>
  <label for="{{ $id }}" class="upload-footer"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path></svg><p data-file-name>{{ $label }}</p></label>
  <input id="{{ $id }}" type="file" name="{{ $name }}" data-file-input>
</div>
