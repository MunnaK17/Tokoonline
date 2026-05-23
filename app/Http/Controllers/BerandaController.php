<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Order;
use Carbon\Carbon;

class BerandaController extends Controller
{
    // BACKEND
    public function index()
    {
        $mulaiBulan = Carbon::now()->startOfMonth()->subMonths(5);
        $statusSelesai = ['Paid', 'Kirim', 'Selesai'];

        $dataBulanan = Order::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as bulan,
                COUNT(*) as total_pesanan,
                COALESCE(SUM(total_harga), 0) as total_pendapatan
            ")
            ->where('created_at', '>=', $mulaiBulan)
            ->whereIn('status', $statusSelesai)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $bulanGrafik = collect(range(5, 0))->map(function ($mundur) use ($dataBulanan) {
            $tanggal = Carbon::now()->startOfMonth()->subMonths($mundur);
            $key = $tanggal->format('Y-m');
            $row = $dataBulanan->get($key);

            return [
                'label' => $tanggal->format('M Y'),
                'pesanan' => (int) ($row->total_pesanan ?? 0),
                'pendapatan' => (float) ($row->total_pendapatan ?? 0),
            ];
        });

        $statusPesanan = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $grafikPesanan = [
            'labels' => $bulanGrafik->pluck('label')->values(),
            'pesanan' => $bulanGrafik->pluck('pesanan')->values(),
            'pendapatan' => $bulanGrafik->pluck('pendapatan')->values(),
        ];

        $grafikStatus = collect(['pending', 'Paid', 'Kirim', 'Selesai'])->map(function ($status) use ($statusPesanan) {
            return [
                'label' => $status,
                'total' => (int) ($statusPesanan[$status] ?? 0),
            ];
        })->values();

        $pesananTerbaru = Order::with('customer.user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('backend.v_beranda.index', [
            'judul' => 'Halaman Beranda',
            'totalPesanan' => Order::count(),
            'pesananPending' => (int) ($statusPesanan['pending'] ?? 0),
            'pesananDibayar' => (int) ($statusPesanan['Paid'] ?? 0),
            'totalPendapatan' => Order::whereIn('status', $statusSelesai)->sum('total_harga'),
            'grafikPesanan' => $grafikPesanan,
            'grafikStatus' => $grafikStatus,
            'pesananTerbaru' => $pesananTerbaru,
        ]);
    }

    // FRONTEND
    public function frontend()
    {
        $produk = Produk::with('kategori')->where('status', 1)->orderBy('updated_at', 'desc')->paginate(6);

        return view('v_beranda.index', [
            'judul' => 'Produk Terbaru',
            'produk' => $produk,
        ]);
    }
}
