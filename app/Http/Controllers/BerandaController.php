<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class BerandaController extends Controller
{
    // BACKEND
    public function index()
    {
        return view('backend.v_beranda.index', [
            'judul' => 'Halaman Beranda',
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
