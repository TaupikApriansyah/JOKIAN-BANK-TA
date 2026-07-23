<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Akuntansi') | SIBERKAS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body">
<div class="app-screen app-screen--workspace">
    <div class="app-sidebar-overlay" data-sidebar-overlay></div>
    <aside class="app-sidebar" data-sidebar>
        <a href="{{ route('akuntan.dashboard') }}" class="app-sidebar__brand">
            <span class="app-sidebar__logo"><i class="bi bi-calculator-fill"></i></span>
            <span><b class="app-sidebar__title">SIBERKAS</b><small class="app-sidebar__caption">Akuntansi Administratif</small></span>
        </a>
        <nav class="app-sidebar__nav">
            <span class="app-sidebar__label">Keuangan layanan</span>
            <a href="{{ route('akuntan.dashboard') }}" class="app-sidebar__link {{ request()->routeIs('akuntan.dashboard') ? 'is-active' : '' }}"><i class="bi bi-grid-1x2-fill"></i>Dashboard</a>
            <a href="{{ route('akuntan.transaksi.index') }}" class="app-sidebar__link {{ request()->routeIs('akuntan.transaksi.*') ? 'is-active' : '' }}"><i class="bi bi-patch-check"></i>Verifikasi Transaksi</a>
            <a href="{{ route('akuntan.akun.index') }}" class="app-sidebar__link {{ request()->routeIs('akuntan.akun.*') ? 'is-active' : '' }}"><i class="bi bi-journal-bookmark"></i>Daftar Akun</a>
            <a href="{{ route('akuntan.jurnal.index') }}" class="app-sidebar__link {{ request()->routeIs('akuntan.jurnal.index') ? 'is-active' : '' }}"><i class="bi bi-journal-text"></i>Jurnal Umum</a>
            <a href="{{ route('akuntan.jurnal.ledger') }}" class="app-sidebar__link {{ request()->routeIs('akuntan.jurnal.ledger') ? 'is-active' : '' }}"><i class="bi bi-table"></i>Buku Besar</a>
            <a href="{{ route('akuntan.laporan.index') }}" class="app-sidebar__link {{ request()->routeIs('akuntan.laporan.*') ? 'is-active' : '' }}"><i class="bi bi-file-earmark-bar-graph"></i>Laporan Akuntansi</a>
            <a href="#" class="app-sidebar__link app-sidebar__link--logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right"></i>Logout</a>
        </nav>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
    </aside>

    <main class="app-main">
        <header class="app-topbar">
            <div class="app-topbar__left">
                <button type="button" class="app-icon-button" aria-label="Sembunyikan sidebar" data-sidebar-toggle><i class="bi bi-layout-sidebar-inset"></i></button>
                <div class="app-topbar__context"><b class="app-topbar__name">{{ auth()->user()->name ?? 'Petugas Akuntansi' }}</b><span class="app-topbar__role">Akuntansi Administratif</span></div>
            </div>
            <div class="app-topbar__right">
                @include('components.ui.accounting-notice')
                <span class="status-badge role-badge akuntan">AKUNTAN</span>
            </div>
        </header>
        @yield('content')
    </main>
</div>
@include('components.ui.loading-indicator')
@stack('scripts')
</body>
</html>
