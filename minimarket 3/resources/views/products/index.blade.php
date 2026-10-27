<!DOCTYPE html>
<html>
<head>
    <title>Daftar Produk</title>
</head>
<body>
    <h1>Daftar Produk POS</h1>

    @if($products->isEmpty())
        <p>Belum ada data produk di database.</p>
    @else
        <ul>
            @foreach($products as $product)
                <li>
                    <strong>{{ $product->name }}</strong> - Rp{{ number_format($product->price) }} 
                    (Stok: {{ $product->stock }})
                </li>
            @endforeach
        </ul>
    @endif
</body>
</html>