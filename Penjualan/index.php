<?php
session_start();
include "./config/koneksi.php";

// Fetch barang data from database
$barangData = mysqli_query($koneksi, "SELECT * FROM barang");
$barangList = [];
while ($row = mysqli_fetch_assoc($barangData)) {
    $barangList[] = $row;
}

// Handle adding items to cart
if (isset($_POST['add'])) {
    $kodeBarang = $_POST['barang'];
    $qty = (int) $_POST['qty'];

    // Validate input
    if ($kodeBarang && $qty > 0) {
        foreach ($barangList as $barang) {
            if ($barang['kode_barang'] === $kodeBarang) {
                $namaBarang = $barang['nama_barang'];
                $hargaBarang = $barang['harga'];
                $total = $hargaBarang * $qty;

                $_SESSION['cart'][] = [
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $namaBarang,
                    'harga' => $hargaBarang,
                    'qty' => $qty,
                    'total' => $total,
                ];
                break;
            }
        }
    } else {
        $_SESSION['flash_message'] = "Input tidak valid.";
    }
}

// Handle removing items from cart
if (isset($_GET['remove'])) {
    $index = (int) $_GET['remove'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
    }

    $_SESSION['flash_message'] = "Barang berhasil dihapus dari keranjang.";

    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid-left">
    <b><p class="bg-danger text-center text-light py-3">PENJUALAN</p></b>
    <div class="pb-3 m-3">
        <form method="POST" action="">
            <div class="input-group mb-2" style="width: 339px">
                <select class="form-select col-5 p-1" name="barang" required>
                    <option selected disabled>Pilih Barang</option>
                    <?php foreach ($barangList as $barang): ?>
                        <option value="<?= htmlspecialchars($barang['kode_barang']) ?>">
                            <?= htmlspecialchars($barang['nama_barang']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <input type="number" name="qty" class="form-control mb-2" placeholder="QTY" min="1" style="width: 339px" required>
            </div>
            <div>
                <button type="submit" name="add" class="btn btn-danger col-5">ADD</button>
            </div>
        </form>
    </div>

    <hr>
    <p class="m-2">Daftar Barang Yang Dibeli</p>
    <div class="m-4">
        <table class="table table-sm">
            <thead class="table-dark">
            <tr>
                <th scope="col">Kode</th>
                <th scope="col">Nama</th>
                <th scope="col">Harga</th>
                <th scope="col">QTY</th>
                <th scope="col">Total</th>
                <th scope="col">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php $grandTotal = 0; ?>
            <?php if (!empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['kode_barang']) ?></td>
                        <td><?= htmlspecialchars($item['nama_barang']) ?></td>
                        <td><?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($item['qty']) ?></td>
                        <td><?= number_format($item['total'], 0, ',', '.') ?></td>
                        <td>
                            <a href="?remove=<?= $index ?>" class="btn btn-sm btn-danger">Hapus</a>
                        </td>
                    </tr>
                    <?php $grandTotal += $item['total']; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Keranjang kosong.</td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4"><strong>GRAND TOTAL</strong></td>
                <td colspan="2"><strong>Rp <?= number_format($grandTotal, 0, ',', '.') ?></strong></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
