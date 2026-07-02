(() => {
  const reduced = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const shell = document.querySelector('.app-shell');
  const desktopToggle = document.querySelector('[data-sidebar-desktop-toggle]');
  const mobileToggle = document.querySelector('[data-sidebar-mobile-toggle]');
  const closeMobile = document.querySelectorAll('[data-sidebar-close]');

  const setCollapsed = (value) => {
    if (!shell) return;
    shell.classList.toggle('sidebar-collapsed', value);
    localStorage.setItem('bank_sidebar_collapsed', value ? '1' : '0');
  };
  if (shell && window.innerWidth >= 768 && localStorage.getItem('bank_sidebar_collapsed') === '1') setCollapsed(true);
  desktopToggle?.addEventListener('click', () => setCollapsed(!shell.classList.contains('sidebar-collapsed')));
  mobileToggle?.addEventListener('click', () => shell?.classList.add('mobile-sidebar-open'));
  closeMobile.forEach((button) => button.addEventListener('click', () => shell?.classList.remove('mobile-sidebar-open')));
  window.addEventListener('resize', () => { if (window.innerWidth >= 768) shell?.classList.remove('mobile-sidebar-open'); });

  document.addEventListener('DOMContentLoaded', () => {
    window.lucide?.createIcons();
    if (!reduced) {
      document.querySelectorAll('.app-page > *, .app-card, .table-shell').forEach((item, index) => {
        item.style.animation = `pageReveal .42s ease ${Math.min(index, 8) * 35}ms both`;
      });
    }
    document.querySelectorAll('[data-upload-card]').forEach((card) => {
      const input = card.querySelector('[data-file-input]');
      const name = card.querySelector('[data-file-name]');
      if (!input || !name) return;
      const sync = () => { name.textContent = input.files?.[0]?.name || 'Belum ada file dipilih'; };
      input.addEventListener('change', sync);
      ['dragover','dragenter'].forEach((event) => card.addEventListener(event, (e) => { e.preventDefault(); card.classList.add('is-dragover'); }));
      ['dragleave','drop'].forEach((event) => card.addEventListener(event, (e) => { e.preventDefault(); card.classList.remove('is-dragover'); }));
      card.addEventListener('drop', (e) => { if (e.dataTransfer?.files?.length) { input.files = e.dataTransfer.files; sync(); } });
    });

    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/25 backdrop-blur-[1px]';
    overlay.innerHTML = '<div class="rounded-xl bg-white px-5 py-4 text-sm font-bold text-slate-700 shadow-xl">Memproses data dengan aman...</div>';
    document.body.appendChild(overlay);
    document.querySelectorAll('form[data-processing-overlay]').forEach((form) => form.addEventListener('submit', () => {
      if (form.checkValidity()) overlay.classList.replace('hidden', 'flex');
    }));
  });
})();
