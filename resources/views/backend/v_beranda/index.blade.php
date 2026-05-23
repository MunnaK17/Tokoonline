@extends('backend.v_layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body border-top">
                <h5 class="card-title">{{ $judul }}</h5>

                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">
                        Selamat Datang, {{ Auth::user()->nama }}
                    </h4>

                    Aplikasi Toko Online dengan hak akses yang anda miliki sebagai
                    <b>
                        @if (Auth::user()->role == 1)
                            Super Admin
                        @elseif (Auth::user()->role == 0)
                            Admin
                        @endif
                    </b>
                    ini adalah halaman utama dari aplikasi Web Programming. Studi Kasus
                    Toko Online.

                    <hr>
                    <p class="mb-0">Kuliah..? BSI Aja !!!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-cyan text-center">
                <h1 class="font-light text-white">{{ $totalPesanan }}</h1>
                <h6 class="text-white">Total Pesanan</h6>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-warning text-center">
                <h1 class="font-light text-white">{{ $pesananPending }}</h1>
                <h6 class="text-white">Pesanan Pending</h6>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-success text-center">
                <h1 class="font-light text-white">{{ $pesananDibayar }}</h1>
                <h6 class="text-white">Pesanan Dibayar</h6>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card card-hover">
            <div class="box bg-info text-center">
                <h3 class="font-light text-white mb-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                <h6 class="text-white">Total Pendapatan</h6>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Grafik Pesanan Bulanan</h4>
                <div class="flot-chart">
                    <div class="flot-chart-content" id="grafik-pesanan"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Status Pesanan</h4>
                <div class="flot-chart">
                    <div class="flot-chart-content" id="grafik-status-pesanan"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Grafik Pendapatan Bulanan</h4>
                <div class="flot-chart">
                    <div class="flot-chart-content" id="grafik-pendapatan"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Pesanan Terbaru</h4>
            </div>
            <div class="comment-widgets scrollable" style="max-height: 300px;">
                @forelse ($pesananTerbaru as $order)
                    <div class="d-flex flex-row comment-row m-t-0">
                        <div class="p-2">
                            <span class="btn btn-cyan btn-circle d-flex align-items-center justify-content-center text-white">
                                <i class="mdi mdi-cart"></i>
                            </span>
                        </div>
                        <div class="comment-text w-100">
                            <h6 class="font-medium mb-1">
                                {{ optional($order->customer?->user)->nama ?? 'Customer' }}
                            </h6>
                            <span class="m-b-15 d-block">
                                Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                            </span>
                            <div class="comment-footer">
                                <span class="text-muted float-end">
                                    {{ $order->created_at->format('d M Y') }}
                                </span>
                                <span class="badge bg-secondary">{{ $order->status }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-3 text-muted">
                        Belum ada data pesanan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="/admin-assets/assets/libs/flot/css/float-chart.css" rel="stylesheet" />
<style>
    .flot-chart {
        height: 320px;
    }

    .card-hover .box {
        min-height: 118px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
</style>
@endpush

@push('scripts')
<script src="/admin-assets/assets/libs/flot/jquery.flot.js"></script>
<script src="/admin-assets/assets/libs/flot/jquery.flot.resize.js"></script>
<script src="/admin-assets/assets/libs/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
<script>
    $(function () {
        var labels = @json($grafikPesanan['labels']);
        var jumlahPesanan = @json($grafikPesanan['pesanan']);
        var pendapatan = @json($grafikPesanan['pendapatan']);
        var statusPesanan = @json($grafikStatus);

        var ticksBulan = labels.map(function (label, index) {
            return [index, label];
        });

        var dataPesanan = jumlahPesanan.map(function (total, index) {
            return [index, total];
        });

        var dataPendapatan = pendapatan.map(function (total, index) {
            return [index, total];
        });

        var ticksStatus = statusPesanan.map(function (item, index) {
            return [index, item.label];
        });

        var dataStatus = statusPesanan.map(function (item, index) {
            return [index, item.total];
        });

        $.plot('#grafik-pesanan', [{
            label: 'Pesanan',
            data: dataPesanan,
            color: '#27a9e3',
            lines: { show: true, fill: true, fillColor: 'rgba(39, 169, 227, 0.18)' },
            points: { show: true, radius: 4 }
        }], {
            grid: { hoverable: true, borderWidth: 1, borderColor: '#e9ecef' },
            xaxis: { ticks: ticksBulan },
            yaxis: { min: 0, tickDecimals: 0 },
            tooltip: true,
            tooltipOpts: { content: '%s: %y pesanan' }
        });

        $.plot('#grafik-status-pesanan', [{
            label: 'Status',
            data: dataStatus,
            color: '#7460ee',
            bars: { show: true, barWidth: 0.45, align: 'center', fill: true }
        }], {
            grid: { hoverable: true, borderWidth: 1, borderColor: '#e9ecef' },
            xaxis: { ticks: ticksStatus },
            yaxis: { min: 0, tickDecimals: 0 },
            tooltip: true,
            tooltipOpts: { content: '%x: %y pesanan' }
        });

        $.plot('#grafik-pendapatan', [{
            label: 'Pendapatan',
            data: dataPendapatan,
            color: '#28b779',
            bars: { show: true, barWidth: 0.45, align: 'center', fill: true }
        }], {
            grid: { hoverable: true, borderWidth: 1, borderColor: '#e9ecef' },
            xaxis: { ticks: ticksBulan },
            yaxis: {
                min: 0,
                tickFormatter: function (value) {
                    return 'Rp ' + value.toLocaleString('id-ID');
                }
            },
            tooltip: true,
            tooltipOpts: {
                content: function (label, x, y) {
                    return label + ': Rp ' + y.toLocaleString('id-ID');
                }
            }
        });
    });
</script>
@endpush
