<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Bank X — Sistem Administrasi Layanan Nasabah' }}</title>
  <script src="https://cdn.tailwindcss.com"></script><script src="https://cdn.jsdelivr.net/npm/chart.js"></script><script src="https://unpkg.com/lucide@latest"></script>
  <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','ui-sans-serif','system-ui','sans-serif']},colors:{brand:{50:'#eff7fc',100:'#d9ebf7',200:'#bdd9eb',500:'#2e79b5',600:'#1f5f94',700:'#174b76',800:'#123e66',900:'#102f4d'},bank:{900:'#103b63',800:'#1f5f94',700:'#174b76',teal:'#1f5f94'}}}}};</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/bank-ui.css') }}"><link rel="stylesheet" href="{{ asset('css/motion.css') }}">
  <style>@keyframes pageReveal{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}@media print{.no-print{display:none!important}body{background:#fff!important}}</style>
</head>
<body class="font-sans text-slate-800">
<div class="app-shell">
  <div class="sidebar-overlay" data-sidebar-close></div>
  <aside class="app-sidebar no-print" aria-label="Navigasi utama">
    <div>
      <div class="sidebar-brand"><div class="sidebar-brand-mark"><i data-lucide="landmark" class="h-5 w-5"></i></div><div class="sidebar-title-wrap"><p class="sidebar-title">Bank X</p><p class="sidebar-subtitle">Internal Operations</p></div><button type="button" class="ml-auto text-[#a9cae2] md:hidden" data-sidebar-close aria-label="Tutup menu"><i data-lucide="x" class="h-5 w-5"></i></button></div>
      <nav class="sidebar-nav">
        <p class="nav-section">Menu Utama</p>
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active':'' }}"><i data-lucide="layout-dashboard" class="h-5 w-5"></i><span class="sidebar-text">Dashboard</span></a>
        <a href="{{ route('customers.index') }}" class="sidebar-link {{ request()->routeIs('customers.*') ? 'sidebar-link-active':'' }}"><i data-lucide="users-round" class="h-5 w-5"></i><span class="sidebar-text">Daftar Nasabah</span></a>
        <a href="{{ route('cases.index') }}" class="sidebar-link {{ request()->routeIs('cases.*') ? 'sidebar-link-active':'' }}"><i data-lucide="folder-open" class="h-5 w-5"></i><span class="sidebar-text">Layanan Nasabah</span></a>
        <a href="{{ route('transactions.index') }}" class="sidebar-link {{ request()->routeIs('transactions.*') ? 'sidebar-link-active':'' }}"><i data-lucide="arrow-right-left" class="h-5 w-5"></i><span class="sidebar-text">Transaksi</span></a>
        <a href="{{ route('sla.index') }}" class="sidebar-link {{ request()->routeIs('sla.*') ? 'sidebar-link-active':'' }}"><i data-lucide="clock-3" class="h-5 w-5"></i><span class="sidebar-text">Monitoring SLA</span></a>
        <a href="{{ route('archives.index') }}" class="sidebar-link {{ request()->routeIs('archives.*') ? 'sidebar-link-active':'' }}"><i data-lucide="archive" class="h-5 w-5"></i><span class="sidebar-text">Arsip Digital</span></a>
        @if(auth()->user()->isAdmin())
          <p class="nav-section">Checker & Laporan</p>
          <a href="{{ route('admin.transactions.index') }}" class="sidebar-link {{ request()->routeIs('admin.transactions.*') ? 'sidebar-link-active':'' }}"><i data-lucide="badge-check" class="h-5 w-5"></i><span class="sidebar-text">Verifikasi Transaksi</span></a>
          <a href="{{ route('admin.corrections.index') }}" class="sidebar-link {{ request()->routeIs('admin.corrections.*') ? 'sidebar-link-active':'' }}"><i data-lucide="refresh-cw" class="h-5 w-5"></i><span class="sidebar-text">Koreksi Transaksi</span></a>
          <a href="{{ route('admin.reconciliations.index') }}" class="sidebar-link {{ request()->routeIs('admin.reconciliations.*') ? 'sidebar-link-active':'' }}"><i data-lucide="wallet-cards" class="h-5 w-5"></i><span class="sidebar-text">Rekonsiliasi Kas</span></a>
          <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'sidebar-link-active':'' }}"><i data-lucide="file-chart-column" class="h-5 w-5"></i><span class="sidebar-text">Laporan & Export</span></a>
          <a href="{{ route('admin.audit.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit.*') ? 'sidebar-link-active':'' }}"><i data-lucide="shield-check" class="h-5 w-5"></i><span class="sidebar-text">Audit Trail</span></a>
          <a href="{{ route('admin.service-types.index') }}" class="sidebar-link {{ request()->routeIs('admin.service-types.*') ? 'sidebar-link-active':'' }}"><i data-lucide="list-checks" class="h-5 w-5"></i><span class="sidebar-text">Master Layanan</span></a>
          <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'sidebar-link-active':'' }}"><i data-lucide="settings-2" class="h-5 w-5"></i><span class="sidebar-text">Manajemen User</span></a>
        @endif
      </nav>
    </div>
    <div class="sidebar-footer"><div class="sidebar-user"><span class="sidebar-avatar">{{ str(auth()->user()->name)->substr(0,1)->upper() }}</span><div class="sidebar-user-copy min-w-0"><p class="sidebar-user-name">{{ auth()->user()->name }}</p><p class="sidebar-user-role">{{ auth()->user()->role->label() }}</p></div></div><form method="POST" action="{{ route('logout') }}" data-processing-overlay>@csrf<button class="sidebar-link w-full text-left hover:!bg-red-500/15 hover:!text-white"><i data-lucide="log-out" class="h-5 w-5"></i><span class="sidebar-text">Keluar</span></button></form></div>
  </aside>
  <main class="app-main">
    <header class="app-header no-print">
      <div class="flex min-w-0 items-center gap-3"><button type="button" class="header-action hidden md:inline-flex" data-sidebar-desktop-toggle aria-label="Sembunyikan sidebar"><i data-lucide="panel-left-close" class="h-5 w-5"></i></button><button type="button" class="header-action md:hidden" data-sidebar-mobile-toggle aria-label="Buka menu"><i data-lucide="menu" class="h-5 w-5"></i></button><div class="min-w-0"><h1 class="header-title truncate">{{ $pageTitle ?? 'Dashboard' }}</h1><p class="header-subtitle">Sistem Informasi Administrasi Layanan Nasabah</p></div></div>
      <div class="flex items-center gap-2"><span class="hidden rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-500 lg:inline-flex"><i data-lucide="calendar-days" class="mr-2 h-3.5 w-3.5"></i>{{ now()->translatedFormat('d M Y') }}</span><a href="{{ route('notifications.index') }}" class="header-action relative"><i data-lucide="bell" class="h-5 w-5"></i>@if($unreadNotificationCount>0)<span class="absolute right-1 top-1 h-2 w-2 rounded-full border-2 border-white bg-red-500"></span>@endif</a><div class="hidden items-center gap-2 border-l border-slate-200 pl-3 sm:flex"><span class="sidebar-avatar !bg-[#103b63] !text-white">{{ str(auth()->user()->name)->substr(0,1)->upper() }}</span><div class="max-w-40"><p class="truncate text-sm font-bold text-slate-800">{{ auth()->user()->name }}</p><p class="truncate text-[11px] text-slate-400">{{ auth()->user()->email }}</p></div></div></div>
    </header>
    <div class="page-wrap">@include('components.flash')<div class="app-page">@yield('content')</div></div>
  </main>
</div>
<script src="{{ asset('js/motion.js') }}"></script>@stack('scripts')
</body></html>
