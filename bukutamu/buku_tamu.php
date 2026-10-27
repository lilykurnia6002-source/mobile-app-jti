<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu Sederhana</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 80%;
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: #1e293b;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        button {
            background-color: #22c55e;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
            font-weight: bold;
        }

        button:hover {
            background-color: #16a34a;
        }

        .error {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .message-box {
            background-color: #f1f5f9;
            border-left: 4px solid #2563eb;
            padding: 10px;
            margin-top: 10px;
            border-radius: 6px;
        }

        .timestamp {
            color: #475569;
            font-size: 13px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Buku Tamu Sederhana</h2>

    <?php
    $file = "buku_tamu.txt";
    $error = "";

    // Jika tombol diklik
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nama = trim($_POST["nama"]);
        $pesan = trim($_POST["pesan"]);

        if ($nama == "" || $pesan == "") {
            $error = "Nama dan pesan tidak boleh kosong!";
        } else {
            $waktu = date("Y-m-d H:i:s");
            $data = "Waktu: $waktu\nNama: $nama\nPesan: $pesan\n-----------------------------\n";
            file_put_contents($file, $data, FILE_APPEND);
        }
    }
    ?>

    <?php if ($error != ""): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>

    <!-- Form Input -->
    <form method="post">
        <label for="nama">Nama:</label>
        <input type="text" name="nama" placeholder="Masukkan nama Anda">

        <label for="pesan">Pesan:</label>
        <textarea name="pesan" placeholder="Tulis pesan Anda..."></textarea>

        <button type="submit">Kirim</button>
    </form>

    <h3 style="margin-top: 30px;">Pesan Tamu yang Sudah Masuk:</h3>

    <?php
    if (file_exists($file)) {
        $isi = file_get_contents($file);
        $data_tamu = array_filter(explode("-----------------------------", $isi));

        foreach ($data_tamu as $tamu) {
            echo "<div class='message-box'>";
            echo nl2br(htmlspecialchars($tamu));
            echo "</div>";
        }
    } else {
        echo "<p>Belum ada pesan tamu.</p>";
    }
    ?>
</div>
</body>
</html>
