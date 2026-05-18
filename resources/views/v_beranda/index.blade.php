@extends('v_layouts.app')

@section('content')
<div id="store">
    <div class="store-title">
        <h3 class="title">{{ $judul ?? 'Produk Terbaru' }}</h3>
    </div>

    <div class="row">
        @forelse ($produk as $row)
            @include('v_produk._product_card', ['row' => $row])
        @empty
            <div class="col-md-12">
                <p>Belum ada produk yang dipublikasikan.</p>
            </div>
        @endforelse
    </div>

    <div class="text-center">
        {{ $produk->links() }}
    </div>
</div>
@endsection
