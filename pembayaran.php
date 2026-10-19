<?php
// Inisialisasi variabel awal
$errors = [];
$hasil = null;

// Memeriksa apakah form telah dikirimkan menggunakan method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hitung'])) {
    // Ambil data dari form
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $harga_satuan = filter_input(INPUT_POST, 'harga_satuan', FILTER_VALIDATE_FLOAT);
    $jumlah = filter_input(INPUT_POST, 'jumlah', FILTER_VALIDATE_INT);

    // Validasi input
    if (empty($nama_barang)) {
        $errors[] = "Nama barang tidak boleh kosong.";
    }
    
    if ($harga_satuan === false || $harga_satuan < 0) {
        $errors[] = "Harga satuan tidak valid atau tidak boleh bernilai negatif.";
    }

    if ($jumlah === false || $jumlah < 1) {
        $errors[] = "Jumlah pembelian minimal harus 1.";
    }

    // Jika tidak ada error, hitung total dan diskon
    if (empty($errors)) {
        $total_kotor = $harga_satuan * $jumlah;
        $persen_diskon = 0;

        // Penentuan diskon berdasarkan total harga
        if ($total_kotor >= 500000) {
            $persen_diskon = 20;
        } elseif ($total_kotor >= 250000) {
            $persen_diskon = 10;
        } else {
            $persen_diskon = 0;
        }

        $nominal_diskon = ($total_kotor * $persen_diskon) / 100;
        $total_bayar = $total_kotor - $nominal_diskon;

        $hasil = [
            'nama_barang' => $nama_barang,
            'harga_satuan' => $harga_satuan,
            'jumlah' => $jumlah,
            'total_kotor' => $total_kotor,
            'persen_diskon' => $persen_diskon,
            'nominal_diskon' => $nominal_diskon,
            'total_bayar' => $total_bayar
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Hitung Pembayaran</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        body {
            background-color: #f5f5f7;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .container {
            background: #ffffff;
            border: 1px solid #e1e1e8;
            border-radius: 8px;
            width: 100%;
            max-width: 420px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.25rem;
            color: #111;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
        }
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }
        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: #2563eb;
        }
        button {
            width: 100%;
            padding: 11px;
            background-color: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background-color: #1d4ed8;
        }
        .error-box {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 16px;
        }
        .error-box ul {
            margin: 0;
            padding-left: 18px;
        }
        .result-box {
            margin-top: 24px;
            border-top: 2px dashed #e5e7eb;
            padding-top: 16px;
        }
        .result-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #4b5563;
        }
        .result-row.total {
            font-weight: bold;
            font-size: 1rem;
            color: #111827;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Kasir Pembayaran</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="pembayaran.php">
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" value="<?= htmlspecialchars($_POST['nama_barang'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="harga_satuan">Harga Satuan (Rp)</label>
            <input type="number" id="harga_satuan" name="harga_satuan" min="0" step="any" value="<?= htmlspecialchars($_POST['harga_satuan'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="jumlah">Jumlah Pembelian</label>
            <input type="number" id="jumlah" name="jumlah" min="1" value="<?= htmlspecialchars($_POST['jumlah'] ?? '1') ?>" required>
        </div>

        <button type="submit" name="hitung">Hitung Total</button>
    </form>

    <?php if ($hasil !== null): ?>
        <div class="result-box">
            <div class="result-row">
                <span>Nama Barang:</span>
                <span><?= htmlspecialchars($hasil['nama_barang']) ?></span>
            </div>
            <div class="result-row">
                <span>Harga Satuan:</span>
                <span>Rp <?= number_format($hasil['harga_satuan'], 0, ',', '.') ?></span>
            </div>
            <div class="result-row">
                <span>Jumlah:</span>
                <span><?= number_format($hasil['jumlah'], 0, ',', '.') ?></span>
            </div>
            <div class="result-row">
                <span>Diskon (<?= $hasil['persen_diskon'] ?>%):</span>
                <span>- Rp <?= number_format($hasil['nominal_diskon'], 0, ',', '.') ?></span>
            </div>
            <div class="result-row total">
                <span>Total Bayar:</span>
                <span>Rp <?= number_format($hasil['total_bayar'], 0, ',', '.') ?></span>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>