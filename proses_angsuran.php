<?php
include('db.php');

// Tambah Angsuran Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pinjaman'])) {
    $id_pinjaman = $_POST['id_pinjaman'];
    $tanggal_bayar = $_POST['tanggal_bayar'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $denda = $_POST['denda'];
    $id_anggota = $_POST['id_anggota']; // Pastikan id_anggota dikirimkan

    // Simpan ke database
    $query = "INSERT INTO angsuran (id_pinjaman, tanggal_bayar, jumlah_bayar, denda) 
              VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_pinjaman, $tanggal_bayar, $jumlah_bayar, $denda]);

    // Redirect kembali ke halaman detail angsuran dengan id_anggota
    header("Location: detail_angsuran.php?id_anggota=$id_anggota");
    exit();
}

// Hapus Angsuran
if (isset($_POST['delete_id'])) {
    $id_angsuran = $_POST['delete_id'];
    $query = "DELETE FROM angsuran WHERE id_angsuran = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute([$id_angsuran])) {
        echo "success"; // Respons berhasil
    } else {
        echo "error"; // Respons gagal
    }
    exit();
}
?>
