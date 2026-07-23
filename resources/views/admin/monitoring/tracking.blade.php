@extends('layouts.admin')
@section('title','Monitoring Tracking | SIBERKAS')
@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-clock-history"></i>
            Monitoring Tracking Status
        </div>
        <div class="page-subtitle">
            Riwayat perubahan status seluruh berkas
        </div>
    </div>

    {{-- 🔍 SEARCH FORM --}}
    <div class="search-card">
        <form action="{{ route('admin.monitoring.tracking') }}" method="GET" class="search-form">
            
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-search"></i> Cari
                </label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Nama nasabah, layanan, status..."
                       value="{{ request('search') }}">
            </div>

            <div class="form-group max-w-[200px]">
                <label class="form-label">
                    <i class="bi bi-funnel"></i> Status
                </label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
                    <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                </select>
            </div>

            <div class="form-group max-w-[180px]">
                <label class="form-label">
                    <i class="bi bi-calendar"></i> Dari Tanggal
                </label>
                <input type="date" 
                       name="tanggal_dari" 
                       class="form-control"
                       value="{{ request('tanggal_dari') }}">
            </div>

            <div class="form-group max-w-[180px]">
                <label class="form-label">
                    <i class="bi bi-calendar-check"></i> Sampai Tanggal
                </label>
                <input type="date" 
                       name="tanggal_sampai" 
                       class="form-control"
                       value="{{ request('tanggal_sampai') }}">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                    Cari
                </button>
                
                @if(request()->hasAny(['search','status','tanggal_dari','tanggal_sampai']))
                <a href="{{ route('admin.monitoring.tracking') }}" class="btn-reset">
                    <i class="bi bi-x-circle"></i>
                    Reset
                </a>
                @endif
            </div>

        </form>
    </div>

    <div class="card-monitoring">
        
        {{-- INFO HASIL PENCARIAN --}}
        @if(request()->hasAny(['search','status','tanggal_dari','tanggal_sampai']))
        <div class="result-info">
            <i class="bi bi-info-circle"></i>
            Menampilkan {{ $tracking->total() }} hasil tracking
            @if(request('search'))
                untuk pencarian "<strong>{{ request('search') }}</strong>"
            @endif
            @if(request('status'))
                dengan status <strong>{{ request('status') }}</strong>
            @endif
            @if(request('tanggal_dari'))
                dari tanggal <strong>{{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d M Y') }}</strong>
            @endif
            @if(request('tanggal_sampai'))
                sampai <strong>{{ \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d M Y') }}</strong>
            @endif
        </div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="60">NO</th>
                        <th>NASABAH</th>
                        <th>LAYANAN</th>
                        <th>STATUS</th>
                        <th>TANGGAL UPDATE</th>
                        <th>CS</th>
                        <th width="140">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tracking as $item)
                        <tr>
                            <td>
                                <span class="row-number">
                                    {{ $tracking->firstItem() + $loop->index }}
                                </span>
                            </td>

                            <td>
                                {{ $item->berkas->nasabah->nama_nasabah ?? '-' }}
                            </td>

                            <td>
                                {{ $item->berkas->jenis_layanan ?? '-' }}
                            </td>

                            <td>
                                @php
                                    $statusClass = match($item->status){
                                        'Diterima' => 'status-diterima',
                                        'Diproses' => 'status-diproses',
                                        'Selesai'  => 'status-selesai',
                                        'Ditolak'  => 'status-ditolak',
                                        'Menunggu' => 'status-menunggu',
                                        default    => 'status-diterima'
                                    };
                                    
                                    $statusIcon = match($item->status){
                                        'Diterima' => 'bi-check-circle',
                                        'Diproses' => 'bi-hourglass-split',
                                        'Selesai'  => 'bi-check-circle-fill',
                                        'Ditolak'  => 'bi-x-circle',
                                        'Menunggu' => 'bi-clock',
                                        default    => 'bi-circle'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="bi {{ $statusIcon }}"></i>
                                    {{ $item->status }}
                                </span>
                            </td>

                            <td>
                                <div class="font-semibold text-slate-900">
                                    {{ \Carbon\Carbon::parse($item->tanggal_update)->format('d M Y') }}
                                </div>
                                <div class="mt-0.5 text-xs text-slate-500">
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($item->tanggal_update)->format('H:i') }} WIB
                                </div>
                            </td>

                            <td>
                                {{ $item->user->name ?? '-' }}
                            </td>

                            <td>
                                <a href="{{ route('admin.monitoring.tracking.show',$item->id) }}" 
                                   class="btn-detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-5">
                                <i class="bi bi-inbox text-5xl text-slate-300"></i>
                                <div class="mt-3 font-semibold text-slate-500">
                                    @if(request()->hasAny(['search','status','tanggal_dari','tanggal_sampai']))
                                        Tidak ada data tracking yang sesuai dengan pencarian
                                    @else
                                        Tidak ada data tracking
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-3">
                {{ $tracking->links() }}
            </div>
        </div>
    </div>

</div>

@endsection