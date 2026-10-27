<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Post</title>
</head>
<body>
    <h2>Daftar Produk / Post</h2>
    <ul>
        @forelse($posts as $post)
            <li>{{ $post->name }} - Rp{{ number_format($post->price) }}</li>
        @empty
            <li>Tidak ada data.</li>
        @endforelse
    </ul>
</body>
</html>