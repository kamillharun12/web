<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "toko_penjualan");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $pelanggan_id = $_POST['pelanggan_id'];
    $produk_id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];

    // Ambil harga produk
    $produk = $conn->query("SELECT * FROM produk WHERE ProdukID = $produk_id")->fetch_assoc();
    $harga = $produk['Harga'];
    $subtotal = $harga * $jumlah;

    // Simpan ke penjualan
    $conn->query("INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID)
                  VALUES (NOW(), $subtotal, $pelanggan_id)");
    $penjualan_id = $conn->insert_id;

    // Simpan ke detailpenjualan
    $conn->query("INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal)
                  VALUES ($penjualan_id, $produk_id, $jumlah, $subtotal)");

    echo "Transaksi berhasil!";
}
?>

<h2>Transaksi Baru</h2>
<form method="post">
    <label>Pelanggan:</label>
    <select name="pelanggan_id">
        <?php
        $pelanggan = $conn->query("SELECT * FROM pelanggan");
        while ($row = $pelanggan->fetch_assoc()) {
            echo "<option value='{$row['PelangganID']}'>{$row['NamaPelanggan']}</option>";
        }
        ?>
    </select><br>

    <label>Produk:</label>
    <select name="produk_id">
        <?php
        $produk = $conn->query("SELECT * FROM produk");
        while ($row = $produk->fetch_assoc()) {
            echo "<option value='{$row['ProdukID']}'>{$row['NamaProduk']} - Rp{$row['Harga']}</option>";
        }
        ?>
    </select><br>

    <label>Jumlah:</label>
    <input type="number" name="jumlah" min="1" required><br>

    <button type="submit">Simpan Transaksi</button>
</form>
