@extends('layouts.admin')
@section('title','Monitoring Berkas | SIBERKAS')
@section('content')
<div class="container-fluid">

    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-folder2-open"></i>
            Monitoring Data Berkas
        </div>
        <div class="page-subtitle">
            Data berkas seluruh CS (Read Only)
        </div>
    </div>

    {{-- 🔍 SEARCH FORM --}}
    <div class="search-card">
        <form action="{{ route('admin.monitoring.berkas') }}" method="GET" class="search-form">
            
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-search"></i> Cari
                </label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Nama nasabah, jenis layanan, CS..."
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
                </select>
            </div>

            <div class="form-group max-w-[220px]">
                <label class="form-label">
                    <i class="bi bi-tag"></i> Jenis Layanan
                </label>
                <select name="jenis_layanan" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="Pembukaan Rekening" {{ request('jenis_layanan') == 'Pembukaan Rekening' ? 'selected' : '' }}>Pembukaan Rekening</option>
                    <option value="Penutupan Rekening" {{ request('jenis_layanan') == 'Penutupan Rekening' ? 'selected' : '' }}>Penutupan Rekening</option>
                    <option value="Perubahan Data" {{ request('jenis_layanan') == 'Perubahan Data' ? 'selected' : '' }}>Perubahan Data</option>
                    <option value="Pengajuan Kredit" {{ request('jenis_layanan') == 'Pengajuan Kredit' ? 'selected' : '' }}>Pengajuan Kredit</option>
                    <option value="Lainnya" {{ request('jenis_layanan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                    Cari
                </button>
                
                @if(request()->hasAny(['search','status','jenis_layanan']))
                <a href="{{ route('admin.monitoring.berkas') }}" class="btn-reset">
                    <i class="bi bi-x-circle"></i>
                    Reset
                </a>
                @endif
            </div>

        </form>
    </div>

    <div class="card-monitoring">
        
        {{-- INFO HASIL PENCARIAN --}}
        @if(request()->hasAny(['search','status','jenis_layanan']))
        <div class="result-info">
            <i class="bi bi-info-circle"></i>
            Menampilkan {{ $berkas->total() }} hasil
            @if(request('search'))
                untuk pencarian "<strong>{{ request('search') }}</strong>"
            @endif
            @if(request('status'))
                dengan status <strong>{{ request('status') }}</strong>
            @endif
            @if(request('jenis_layanan'))
                jenis layanan <strong>{{ request('jenis_layanan') }}</strong>
            @endif
        </div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="60">NO</th>
                        <th>NASABAH</th>
                        <th>JENIS LAYANAN</th>
                        <th>TANGGAL MASUK</th>
                        <th>STATUS</th>
                        <th>CS</th>
                        <th width="140">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($berkas as $item)
                        <tr>
                            <td>
                                <span class="row-number">
                                    {{ $berkas->firstItem() + $loop->index }}
                                </span>
                            </td>

                            <td>
                                {{ $item->nasabah->nama_nasabah ?? '-' }}
                            </td>

                            <td>
                                {{ $item->jenis_layanan }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M Y') }}
                            </td>

                            <td>
                                @php
                                    $statusClass = match($item->status_berkas){
                                        'Diterima' => 'status-diterima',
                                        'Diproses' => 'status-diproses',
                                        'Selesai'  => 'status-selesai',
                                        'Ditolak'  => 'status-ditolak',
                                        default    => 'status-diterima'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $item->status_berkas }}
                                </span>
                            </td>

                            <td>
                                {{ $item->user->name ?? '-' }}
                            </td>

                            <td>
                                <a href="{{ route('admin.monitoring.berkas.show',$item->id) }}"
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
                                    @if(request()->hasAny(['search','status','jenis_layanan']))
                                        Tidak ada data berkas yang sesuai dengan pencarian
                                    @else
                                        Tidak ada data berkas
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-3">
                {{ $berkas->links() }}
            </div>
        </div>
    </div>

</div>

@endsection