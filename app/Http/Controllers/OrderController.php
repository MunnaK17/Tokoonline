<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\Produk;
use App\Models\Order;
use App\Models\OrderItem;
use Midtrans\Snap;
use Midtrans\Config;

class OrderController extends Controller
{
    public function statusProses()
    {
        $order = Order::with('customer.user')
            ->whereIn('status', ['Paid', 'Kirim'])
            ->orderBy('id', 'desc')
            ->get();

        return view('backend.v_pesanan.proses', [
            'judul' => 'Pesanan',
            'subJudul' => 'Pesanan Proses',
            'index' => $order,
        ]);
    }

    public function statusSelesai()
    {
        $order = Order::with('customer.user')
            ->where('status', 'Selesai')
            ->orderBy('id', 'desc')
            ->get();

        return view('backend.v_pesanan.selesai', [
            'judul' => 'Pesanan',
            'subJudul' => 'Pesanan Selesai',
            'index' => $order,
        ]);
    }

    public function statusDetail($id)
    {
        $order = Order::with('customer.user', 'orderItems.produk.kategori')->findOrFail($id);

        return view('backend.v_pesanan.detail', [
            'judul' => 'Pesanan',
            'subJudul' => 'Detail Pesanan',
            'order' => $order,
        ]);
    }

    public function statusUpdate(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $rules = [
            'alamat' => 'required',
            'status' => 'required|in:Paid,Kirim,Selesai',
            'pos' => 'nullable|max:10',
            'noresi' => 'nullable|max:255',
        ];

        if ($request->input('status') === 'Kirim') {
            $rules['noresi'] = 'required|max:255';
        }

        $validatedData = $request->validate($rules);
        $order->update($validatedData);

        return redirect()->route('pesanan.proses')->with('success', 'Data berhasil diperbaharui');
    }

    public function invoiceBackend($id)
    {
        $order = Order::with('customer.user', 'orderItems.produk.kategori')->findOrFail($id);

        return view('backend.v_pesanan.invoice', [
            'judul' => 'Pesanan',
            'subJudul' => 'Invoice Pesanan',
            'order' => $order,
        ]);
    }

    public function addToCart($id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('beranda')->with('error', 'Data customer tidak ditemukan.');
        }

        $produk = Produk::findOrFail($id);

        $order = Order::firstOrCreate(
            ['customer_id' => $customer->id, 'status' => 'pending'],
            ['total_harga' => 0]
        );

        $orderItem = OrderItem::firstOrCreate(
            ['order_id' => $order->id, 'produk_id' => $produk->id],
            ['quantity' => 1, 'harga' => $produk->harga]
        );

        if (!$orderItem->wasRecentlyCreated) {
            $orderItem->quantity++;
            $orderItem->save();
        }

        $order->total_harga += $produk->harga;
        $order->save();

        return redirect()->route('order.cart')->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function viewCart()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return view('v_order.cart', ['order' => null]);
        }

        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();
        if (!$order) {
            return view('v_order.cart', ['order' => null]);
        }

        // Load relasi orderItems
        $order->load('orderItems.produk');

        return view('v_order.cart', compact('order'));
    }

    public function updateCart(Request $request, $id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('order.cart')->with('error', 'Data customer tidak ditemukan.');
        }

        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();
        if ($order) {
            $orderItem = $order->orderItems()->where('id', $id)->first();
            if ($orderItem) {
                $quantity = $request->input('quantity');
                if ($quantity > $orderItem->produk->stok) {
                    return redirect()->route('order.cart')->with('error', 'Jumlah produk melebihi stok yang tersedia');
                }
                $order->total_harga -= $orderItem->harga * $orderItem->quantity;
                $orderItem->quantity = $quantity;
                $orderItem->save();
                $order->total_harga += $orderItem->harga * $orderItem->quantity;
                $order->save();
            }
        }
        return redirect()->route('order.cart')->with('success', 'Jumlah produk berhasil diperbarui');
    }

    public function removeFromCart(Request $request, $id)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('order.cart')->with('error', 'Data customer tidak ditemukan.');
        }

        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();

        if ($order) {
            $orderItem = OrderItem::where('order_id', $order->id)->where('produk_id', $id)->first();

            if ($orderItem) {
                $order->total_harga -= $orderItem->harga * $orderItem->quantity;
                $orderItem->delete();

                if ($order->total_harga <= 0) {
                    $order->delete();
                } else {
                    $order->save();
                }
            }
        }
        return redirect()->route('order.cart')->with('success', 'Produk berhasil dihapus dari keranjang');
    }

    public function selectShipping(Request $request)
    {
        // Mendapatkan customer berdasarkan user yang login
        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('order.cart')->with('error', 'Data customer tidak ditemukan.');
        }

        // Pastikan order dengan status 'pending' ada untuk customer ini
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();

        // Cek apakah order ada
        if (!$order) {
            return redirect()->route('order.cart')->with('error', 'Keranjang belanja kosong.');
        }

        // Pastikan orderItems sudah dimuat menggunakan eager loading
        $order->load('orderItems.produk');

        // Lanjutkan ke view jika order ada
        return view('v_order.select_shipping', compact('order'));
    }

    public function updateOngkir(Request $request)
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return back()->with('error', 'Data customer tidak ditemukan.');
        }
        $order = Order::where('customer_id', $customer->id)->where('status', 'pending')->first();

        $origin = $request->input('city_origin'); // kode kota asal
        $originName = $request->input('city_origin_name'); // nama kota asal

        if ($order) {
            // Simpan data ongkir ke dalam order
            $order->kurir = $request->input('kurir');
            $order->layanan_ongkir = $request->input('layanan_ongkir');
            $order->biaya_ongkir = $request->input('biaya_ongkir');
            $order->estimasi_ongkir = $request->input('estimasi_ongkir');
            $order->total_berat = $request->input('total_berat');
            $order->alamat = $request->input('alamat') . ', <br>' . $request->input('city_name') . ', <br>' . $request->input('province_name');
            $order->pos = $request->input('pos');
            $order->save();
            // Simpan ke session flash agar bisa diakses di halaman tujuan
            return redirect()->route('order.selectpayment')
                ->with('origin', $origin)
                ->with('originName', $originName);
        }

        return back()->with('error', 'Gagal menyimpan data ongkir');
    }

    public function selectPayment()
    {
        $customer = Auth::user();
        $order = Order::where('customer_id', $customer->customer->id)->where('status', 'pending')->first();

        $origin = session('origin');        // Kode kota asal
        $originName = session('originName'); // Nama kota asal


        if (!$order) {
            return redirect()->route('order.cart')->with('error', 'Keranjang belanja kosong.');
        }

        // Muat relasi orderItems dan produk terkait
        $order->load('orderItems.produk');

        // Hitung total harga produk
        $totalHarga = 0;
        foreach ($order->orderItems as $item) {
            $totalHarga += $item->harga * $item->quantity;
        }

        // Tambahkan biaya ongkir ke total harga
        $grossAmount = (int) ($totalHarga + (float) $order->biaya_ongkir);

        // Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');

        // Generate unique order_id
        $orderId = 'ORDER-' . $order->id . '-' . time();

        $itemDetails = $order->orderItems->map(function ($item) {
            return [
                'id' => (string) $item->produk_id,
                'price' => (int) $item->harga,
                'quantity' => (int) $item->quantity,
                'name' => substr($item->produk->nama_produk, 0, 50),
            ];
        })->values()->all();

        if ((float) $order->biaya_ongkir > 0) {
            $itemDetails[] = [
                'id' => 'ONGKIR',
                'price' => (int) $order->biaya_ongkir,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $customer->nama,
                'email' => $customer->email,
                'phone' => $customer->hp,
            ],
            'callbacks' => [
                'finish' => route('order.complete'),
            ],
        ];

        $snapToken = null;
        if (config('midtrans.server_key')) {
            try {
                $snapToken = Snap::getSnapToken($params);
            } catch (\Throwable $e) {
                Log::error('Midtrans Snap token failed', [
                    'order_id' => $order->id,
                    'message' => $e->getMessage(),
                ]);

                return redirect()->route('order.cart')
                    ->with('error', 'Gagal membuat token pembayaran Midtrans. Silakan coba lagi.');
            }
        }

        return view('v_order.select_payment', [
            'order' => $order,
            'origin' => $origin,
            'originName' => $originName,
            'snapToken' => $snapToken,
        ]);
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');

        if (!$serverKey) {
            return response()->json(['message' => 'Midtrans server key belum dikonfigurasi'], 500);
        }

        $signatureKey = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        if (!hash_equals($signatureKey, (string) $request->signature_key)) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $localOrderId = $this->localOrderIdFromMidtransOrderId((string) $request->order_id);
        $order = Order::find($localOrderId);

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus = $request->fraud_status;

        $this->updateOrderStatusFromMidtrans($order, $transactionStatus, $fraudStatus);

        return response()->json(['message' => 'Callback berhasil diproses']);
    }

    public function complete(Request $request)
    {
        $customer = Auth::user();

        if ($request->filled('order_id') && config('midtrans.server_key')) {
            $midtransOrderId = (string) $request->query('order_id');
            $localOrderId = $this->localOrderIdFromMidtransOrderId($midtransOrderId);

            $order = Order::where('id', $localOrderId)
                ->where('customer_id', $customer->customer->id)
                ->first();

            if ($order) {
                $response = Http::withBasicAuth(config('midtrans.server_key'), '')
                    ->acceptJson()
                    ->get($this->midtransApiBaseUrl() . '/v2/' . urlencode($midtransOrderId) . '/status');

                if ($response->successful()) {
                    $status = $response->json();
                    $signatureKey = hash('sha512', $status['order_id'] . $status['status_code'] . $status['gross_amount'] . config('midtrans.server_key'));

                    if (hash_equals($signatureKey, (string) ($status['signature_key'] ?? ''))) {
                        $this->updateOrderStatusFromMidtrans(
                            $order,
                            $status['transaction_status'] ?? null,
                            $status['fraud_status'] ?? null
                        );
                    }
                }
            }

            return redirect()->route('order.history')->with('success', 'Checkout berhasil');
        }

        $order = Order::where('customer_id', $customer->customer->id)
            ->where('status', 'pending')
            ->first();

        if ($order && !config('midtrans.server_key')) {
            $order->status = 'Paid';
            $order->save();
        }

        return redirect()->route('order.history')->with('success', 'Checkout berhasil');
    }


    // public function complete() // Untuk kondisi sudah memiliki domain
    // {
    //     // Logika untuk halaman setelah pembayaran berhasil
    //     return redirect()->route('order.history')->with('success', 'Checkout berhasil');
    // }

    public function orderHistory()
    {
        $customer = Customer::where('user_id', Auth::id())->first();
        if (!$customer) {
            return redirect()->route('beranda')->with('error', 'Data customer tidak ditemukan.');
        }

        // $orders = Order::where('customer_id', $customer->id)->where('status', 'completed')->get();
        $statuses = ['Paid', 'Kirim', 'Selesai'];
        $orders = Order::where('customer_id', $customer->id)
            ->whereIn('status', $statuses)
            ->orderBy('id', 'desc')
            ->get();
        return view('v_order.history', compact('orders'));
    }

    public function invoiceFrontend($id)
    {
        $order = Order::findOrFail($id);
        return view('v_order.invoice', [
            'judul' => 'Pesanan',
            'subJudul' => 'Pesanan Proses',
            'order' => $order,
        ]);
    }

    private function localOrderIdFromMidtransOrderId(string $midtransOrderId): int
    {
        if (preg_match('/^ORDER-(\d+)-/', $midtransOrderId, $matches)) {
            return (int) $matches[1];
        }

        return (int) $midtransOrderId;
    }

    private function updateOrderStatusFromMidtrans(Order $order, ?string $transactionStatus, ?string $fraudStatus = null): void
    {
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $order->update(['status' => 'Paid']);
            }
        } elseif ($transactionStatus === 'settlement') {
            $order->update(['status' => 'Paid']);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
            $order->update(['status' => 'pending']);
        }
    }

    private function midtransApiBaseUrl(): string
    {
        return config('midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }
}
