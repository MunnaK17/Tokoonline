@extends('backend.v_layouts.app')

@section('content')
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
    <div class="card mb-3">
        <div class="card-header">
            <h3>{{ $subJudul }}</h3>
        </div>
        <div class="card-body">
            <div class="invoice-title text-center mb-3">
                <h2>Detail Pesanan #{{ $order->id }}</h2>
                <strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}
            </div>

            <form action="{{ route('pesanan.update', $order->id) }}" method="post">
                @method('put')
                @csrf

                <hr>
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-6">
                        <h5>Pelanggan</h5>
                        <address>
                            Nama: {{ optional($order->customer?->user)->nama ?? '-' }}<br>
                            Email: {{ optional($order->customer?->user)->email ?? '-' }}<br>
                            Hp: {{ optional($order->customer?->user)->hp ?? '-' }}<br>
                        </address>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-6 text-end">
                        <h5>Ongkos Kirim</h5>
                        <address>
                            Kurir: {{ $order->kurir ?? '-' }}<br>
                            Layanan: {{ $order->layanan_ongkir ?? '-' }}<br>
                            Estimasi: {{ $order->estimasi_ongkir ?? '-' }} Hari<br>
                            Berat: {{ $order->total_berat ?? 0 }} Gram<br>
                        </address>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <h5>Produk</h5>
                        <table class="table table-bordered table-hover display">
                            <thead>
                                <tr>
                                    <th colspan="2">Produk</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalHarga = 0;
                                    $totalBerat = 0;
                                @endphp
                                @foreach($order->orderItems as $item)
                                @php
                                    $totalHarga += $item->harga * $item->quantity;
                                    $totalBerat += $item->produk->berat * $item->quantity;
                                    $fotoProduk = $item->produk->foto ? asset('storage/img-produk/thumb_sm_' . $item->produk->foto) : asset('image/img-default.jpg');
                                @endphp
                                <tr>
                                    <td align="center">
                                        <img src="{{ $fotoProduk }}" alt="{{ $item->produk->nama_produk }}" width="80">
                                    </td>
                                    <td class="details">
                                        <a>{{ $item->produk->nama_produk }} #{{ $item->produk->kategori->nama_kategori }}</a>
                                        <ul>
                                            <li><span>Berat: {{ $item->produk->berat }} Gram</span></li>
                                            <li><span>Stok: {{ $item->produk->stok }}</span></li>
                                        </ul>
                                    </td>
                                    <td class="price text-center">Rp. {{ number_format($item->harga, 0, ',', '.') }}</td>
                                    <td class="qty text-center">{{ $item->quantity }}</td>
                                    <td class="total text-center">Rp. {{ number_format($item->harga * $item->quantity, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="empty" colspan="3"></th>
                                    <td>Subtotal</td>
                                    <td colspan="2">Rp. {{ number_format($totalHarga, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="empty" colspan="3"></th>
                                    <td>Ongkos Kirim</td>
                                    <td colspan="2">Rp. {{ number_format($order->biaya_ongkir, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="empty" colspan="3"></th>
                                    <th>TOTAL BAYAR</th>
                                    <th colspan="2" class="total">Rp. {{ number_format($totalHarga + (float) $order->biaya_ongkir, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group mb-3">
                            <label>No. Resi</label>
                            <input type="text" name="noresi" value="{{ old('noresi', $order->noresi) }}" class="form-control @error('noresi') is-invalid @enderror" placeholder="Masukkan Nomor Resi">
                            @error('noresi')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="" {{ old('status', $order->status) == '' ? 'selected' : '' }}>- Pilih Status Pesanan -</option>
                                <option value="Paid" {{ old('status', $order->status) == 'Paid' ? 'selected' : '' }}>Proses</option>
                                <option value="Kirim" {{ old('status', $order->status) == 'Kirim' ? 'selected' : '' }}>Kirim</option>
                                <option value="Selesai" {{ old('status', $order->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            @error('status')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" id="ckeditor">{{ old('alamat', $order->alamat) }}</textarea>
                            @error('alamat')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label>Kode Pos</label>
                            <input type="text" name="pos" value="{{ old('pos', $order->pos) }}" class="form-control @error('pos') is-invalid @enderror" placeholder="Masukkan Kode Pos">
                            @error('pos')
                            <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <br>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('pesanan.proses') }}">
                    <button type="button" class="btn btn-secondary">Kembali</button>
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
