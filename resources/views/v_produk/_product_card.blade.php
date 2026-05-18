<div class="col-md-4 col-sm-6 col-xs-6">
    <div class="product product-single">
        <div class="product-thumb">
            <div class="product-label">
                <span>Kategori</span>
                <span class="sale">{{ $row->kategori->nama_kategori }}</span>
            </div>

            <a href="{{ route('produk.detail', $row->id) }}">
                <button class="main-btn quick-view" type="button">
                    <i class="fa fa-search-plus"></i> Detail Produk
                </button>
            </a>
            @php
                $fotoProduk = $row->foto ? route('produk.foto', $row->foto) : asset('frontend/img/product01.jpg');
            @endphp
            <img src="{{ $fotoProduk }}" alt="{{ $row->nama_produk }}">
        </div>
        <div class="product-body">
            <h3 class="product-price">
                Rp. {{ number_format($row->harga, 0, ',', '.') }}
                <span class="product-old-price">{{ $row->kategori->nama_kategori }}</span>
            </h3>

            <h2 class="product-name">
                <a href="{{ route('produk.detail', $row->id) }}">{{ $row->nama_produk }}</a>
            </h2>
            <div class="product-btns">
                <a href="{{ route('produk.detail', $row->id) }}" title="Detail Produk">
                    <button class="main-btn icon-btn" type="button"><i class="fa fa-search-plus"></i></button>
                </a>
                <form action="{{ route('order.addToCart', $row->id) }}" method="post" style="display: inline-block;" title="Pesan Ke Aplikasi">
                    @csrf
                    <button type="submit" class="primary-btn add-to-cart">
                        <i class="fa fa-shopping-cart"></i> Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
