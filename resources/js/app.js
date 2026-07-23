import './bootstrap';

const waveLeft = `<span class="uiverse-button__wave uiverse-button__wave--left" aria-hidden="true"><svg viewBox="0 0 487 487" preserveAspectRatio="none"><path fill="#fff" fill-opacity=".55" d="M0 .3c67 2.1 134.1 4.3 186.3 37 52.2 32.7 89.6 95.8 112.8 150.6 23.2 54.8 32.3 101.4 61.2 149.9 28.9 48.4 77.7 98.8 126.4 149.2H0V.3z"/></svg></span>`;
const waveRight = `<span class="uiverse-button__wave uiverse-button__wave--right" aria-hidden="true"><svg viewBox="0 0 487 487" preserveAspectRatio="none"><path fill="#fff" fill-opacity=".55" d="M487 486.7c-66.1-3.6-132.3-7.3-186.3-37s-95.9-85.3-126.2-137.2c-30.4-51.8-49.3-99.9-76.5-151.4C70.9 109.6 35.6 54.8.3 0H487v486.7z"/></svg></span>`;

function decorateButtons() {
    const selector = '.btn, .btn-add, .btn-back, .btn-cancel, .btn-detail, .btn-download, .btn-edit, .btn-reset, .btn-save, .btn-search, .btn-submit, .btn-update, .btn-upload, .btn-view, .btn-primary, .btn-secondary, .btn-info, .btn-warning, .btn-danger, .btn-delete, .btn-action';

    document.querySelectorAll(selector).forEach((button) => {
        if (button.dataset.uiverseReady || button.matches('[data-no-uiverse-button]')) return;

        const content = document.createElement('span');
        content.className = 'uiverse-button__content';
        while (button.firstChild) content.appendChild(button.firstChild);

        button.classList.add('uiverse-button');
        button.dataset.uiverseReady = '1';
        button.insertAdjacentHTML('beforeend', '<span class="uiverse-button__splash" aria-hidden="true"></span>');
        button.insertAdjacentHTML('beforeend', waveLeft);
        button.insertAdjacentHTML('beforeend', waveRight);
        button.insertAdjacentHTML('beforeend', '<span class="uiverse-button__sheen" aria-hidden="true"></span>');
        button.appendChild(content);
    });
}

function setModal(id, open) {
    const modal = document.getElementById(id);
    if (!modal) return;

    modal.classList.toggle('is-open', open);
    document.body.style.overflow = open ? 'hidden' : '';
    if (open) modal.querySelector('input, select, textarea, button')?.focus();
}

function setupModals() {
    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-modal-open]');
        if (opener) {
            event.preventDefault();
            setModal(opener.dataset.modalOpen, true);
        }

        if (event.target.closest('[data-modal-close]')) {
            const modal = event.target.closest('[data-modal]');
            if (modal) setModal(modal.id, false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        document.querySelectorAll('[data-modal].is-open').forEach((modal) => setModal(modal.id, false));
    });

    const modalWithError = document.body.dataset.openModal;
    if (modalWithError) setModal(modalWithError, true);
}

function setupSidebar() {
    const sidebar = document.querySelector('[data-sidebar]');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    if (!sidebar || !overlay) return;

    const desktop = () => window.matchMedia('(min-width: 901px)').matches;
    const setToggleIcon = () => {
        const collapsed = document.body.classList.contains('sidebar-collapsed');
        document.querySelectorAll('[data-sidebar-toggle] i').forEach((icon) => {
            icon.className = collapsed ? 'bi bi-layout-sidebar' : 'bi bi-layout-sidebar-inset';
        });
    };
    const closeMobile = () => {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-open');
    };

    sidebar.querySelectorAll('.app-sidebar__link').forEach((link) => {
        if (!link.dataset.navLabel) link.dataset.navLabel = link.textContent.trim();
    });

    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            if (desktop()) {
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('siberkas-sidebar', document.body.classList.contains('sidebar-collapsed') ? 'compact' : 'shown');
                setToggleIcon();
            } else {
                sidebar.classList.toggle('is-open');
                overlay.classList.toggle('is-open');
            }
        });
    });

    overlay.addEventListener('click', closeMobile);
    if (desktop() && ['hidden', 'compact'].includes(localStorage.getItem('siberkas-sidebar'))) {
        document.body.classList.add('sidebar-collapsed');
    }
    setToggleIcon();
    window.addEventListener('resize', () => {
        if (desktop()) closeMobile();
        setToggleIcon();
    });
}

function setupNotice() {
    const closeAll = () => document.querySelectorAll('[data-notice].is-open').forEach((notice) => notice.classList.remove('is-open'));

    document.addEventListener('click', (event) => {
        const closeButton = event.target.closest('[data-notice-close]');
        if (closeButton) {
            closeButton.closest('[data-notice]')?.classList.remove('is-open');
            return;
        }

        const button = event.target.closest('[data-notice-toggle]');
        if (button) {
            const notice = button.closest('[data-notice]');
            const willOpen = !notice?.classList.contains('is-open');
            closeAll();
            if (willOpen) notice?.classList.add('is-open');
            return;
        }

        if (!event.target.closest('[data-notice]')) closeAll();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAll();
    });
}

function setupLoader() {
    const loader = document.querySelector('[data-global-loader]');
    if (!loader) return;

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.noLoader === 'true') return;
        if (!form.checkValidity()) return;
        window.setTimeout(() => loader.classList.add('is-visible'), 90);
    });

    window.addEventListener('pageshow', () => loader.classList.remove('is-visible'));
}

function setupFileUploads() {
    document.querySelectorAll('[data-file-upload]').forEach((widget) => {
        const input = widget.querySelector('[data-upload-input]');
        const dropzone = widget.querySelector('[data-upload-dropzone]');
        const filename = widget.querySelector('[data-upload-filename]');
        const clear = widget.querySelector('[data-upload-clear]');
        const feedback = widget.querySelector('[data-upload-feedback]');
        const emptyText = widget.dataset.emptyText || 'Belum ada file dipilih';
        if (!input || !dropzone || !filename) return;

        const paint = (file) => {
            filename.textContent = file ? `${file.name} (${Math.ceil(file.size / 1024)} KB)` : emptyText;
            if (clear) clear.disabled = !file;
        };
        const selectFile = (file) => {
            if (!file) return;
            const allowed = input.accept.split(',').map((item) => item.trim().toLowerCase());
            const maxSize = Number(widget.dataset.maxSize || 0);
            const matches = !input.accept || allowed.some((item) => item.startsWith('.') ? file.name.toLowerCase().endsWith(item) : file.type === item);
            if (!matches) {
                if (feedback) { feedback.textContent = 'Format file belum didukung.'; feedback.classList.remove('hidden'); }
                return;
            }
            if (maxSize && file.size > maxSize) {
                if (feedback) { feedback.textContent = `Ukuran file maksimal ${Math.round(maxSize / 1024 / 1024)} MB.`; feedback.classList.remove('hidden'); }
                return;
            }
            const transfer = new DataTransfer();
            transfer.items.add(file);
            input.files = transfer.files;
            if (feedback) feedback.classList.add('hidden');
            paint(file);
        };

        input.addEventListener('change', () => paint(input.files?.[0]));
        dropzone.addEventListener('click', () => input.click());
        dropzone.addEventListener('keydown', (event) => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); input.click(); } });
        dropzone.addEventListener('dragover', (event) => { event.preventDefault(); dropzone.classList.add('is-dragging'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('is-dragging'));
        dropzone.addEventListener('drop', (event) => {
            event.preventDefault();
            dropzone.classList.remove('is-dragging');
            selectFile(event.dataTransfer?.files?.[0]);
        });
        clear?.addEventListener('click', () => { input.value = ''; paint(null); });
    });
}
document.addEventListener('DOMContentLoaded', () => {
    decorateButtons();
    setupModals();
    setupSidebar();
    setupNotice();
    setupLoader();
    setupFileUploads();
});
