<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RajaOngkirController;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return redirect()->route('beranda');
});



Route::get('backend/login', [LoginController::class, 'loginBackend'])
    ->name('backend.login');

Route::get('login', [LoginController::class, 'loginBackend'])
    ->name('frontend.login');

Route::post('backend/login', [LoginController::class, 'authenticateBackend'])
    ->name('backend.login.submit');

Route::post('login', [LoginController::class, 'authenticateBackend'])
    ->name('frontend.login.submit');

Route::post('backend/logout', [LoginController::class, 'logoutBackend'])
    ->name('backend.logout');

Route::get('/backend/beranda', [BerandaController::class, 'index']) // Pastikan ini 'index'
    ->middleware(['auth', 'is.admin'])
    ->name('backend.beranda');

Route::get('/admin', function () {
    return redirect()->route('backend.beranda');
})->middleware(['auth', 'is.admin']);

// Route resource untuk User Control
Route::resource('backend/user', UserController::class, ['as' => 'backend'])->middleware(['auth', 'is.admin']);

Route::resource('backend/kategori', KategoriController::class, ['as' => 'backend'])->middleware(['auth', 'is.admin']);

Route::resource('backend/produk', ProdukController::class, ['as' => 'backend'])->middleware(['auth', 'is.admin']);

// Route untuk menambahkan foto
Route::post('foto-produk/store', [ProdukController::class, 'storeFoto'])->name('backend.foto_produk.store')->middleware(['auth', 'is.admin']);

// Route untuk menghapus foto
Route::delete('foto-produk/{id}', [ProdukController::class, 'destroyFoto'])->name('backend.foto_produk.destroy')->middleware(['auth', 'is.admin']);


Route::get('backend/laporan/formuser', [UserController::class, 'formUser'])->name('backend.laporan.formuser')->middleware(['auth', 'is.admin']);

Route::post('backend/laporan/cetakuser', [UserController::class, 'cetakUser'])->name('backend.laporan.cetakuser')->middleware(['auth', 'is.admin']);  

Route::get('backend/laporan/formproduk', [ProdukController::class, 'formProduk'])->name('backend.laporan.formproduk')->middleware(['auth', 'is.admin']);
Route::post('backend/laporan/cetakproduk', [ProdukController::class, 'cetakProduk'])->name('backend.laporan.cetakproduk')->middleware(['auth', 'is.admin']);

Route::get('backend/customer', [CustomerController::class, 'index'])
    ->name('backend.customer.index')
    ->middleware(['auth', 'is.admin']);

// Frontend customer
Route::get('/beranda', [BerandaController::class, 'frontend'])->name('beranda');
Route::get('/produk/foto/{filename}', [ProdukController::class, 'foto'])->name('produk.foto');
Route::get('/produk/detail/{id}', [ProdukController::class, 'detail'])->name('produk.detail');
Route::get('/produk/kategori/{id}', [ProdukController::class, 'produkKategori'])->name('produk.kategori');
Route::get('/produk/all', [ProdukController::class, 'produkAll'])->name('produk.all');

Route::get('/auth/redirect', [CustomerController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/google/callback', [CustomerController::class, 'callback'])->name('auth.callback');
Route::post('/customer/logout', [CustomerController::class, 'logout'])->name('customer.logout');

Route::middleware('is.customer')->group(function () {
    Route::get('/customer/akun/{id}', [CustomerController::class, 'akun'])->name('customer.akun');
    Route::put('/customer/update/{id}', [CustomerController::class, 'updateAkun'])->name('customer.update');

    Route::post('add-to-cart/{id}', [OrderController::class, 'addToCart'])->name('order.addToCart');
    Route::get('cart', [OrderController::class, 'viewCart'])->name('order.cart');
    Route::post('cart/update/{id}', [OrderController::class, 'updateCart'])->name('order.updateCart');
    Route::post('remove/{id}', [OrderController::class, 'removeFromCart'])->name('order.remove');
    Route::post('select-shipping', [OrderController::class, 'selectShipping'])->name('order.select-shipping');
    Route::post('update-ongkir', [OrderController::class, 'updateOngkir'])->name('order.update-ongkir');
    Route::get('select-payment', [OrderController::class, 'selectPayment'])->name('order.selectpayment');
    Route::post('/midtrans-callback', [OrderController::class, 'callback'])->name('order.midtrans-callback');
    Route::get('/order/complete', [OrderController::class, 'complete'])->name('order.complete');
    Route::get('history', [OrderController::class, 'orderHistory'])->name('order.history');
    Route::get('order/invoice/{id}', [OrderController::class, 'invoiceFrontend'])->name('order.invoice');
});

Route::get('/cek-ongkir', function () {
    return view('ongkir');
});
Route::get('/list-ongkir', [RajaOngkirController::class, 'getProvinces']);
Route::get('/provinces', [RajaOngkirController::class, 'getProvinces']);
Route::get('/cities', [RajaOngkirController::class, 'getCities']);
Route::post('/cost', [RajaOngkirController::class, 'getCost']);
