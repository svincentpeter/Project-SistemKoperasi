<?php
include('db.php');

// Tambah Pinjaman Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['jumlah_pinjaman'])) {
    $id_anggota = $_POST['id_anggota'];
    $jenis_pinjaman = $_POST['jenis_pinjaman'];
    $jumlah_pinjaman = $_POST['jumlah_pinjaman'];
    $tanggal_pencairan = date('Y-m-d');
    
    // Perhitungan jatuh tempo berdasarkan jenis pinjaman
    $tenor = $_POST['tenor'];
    if ($jenis_pinjaman == "Mingguan") {
        $jatuh_tempo = date('Y-m-d', strtotime("+{$tenor} week"));
    } else {
        $jatuh_tempo = date('Y-m-d', strtotime("+{$tenor} month"));
    }

    $bunga = $_POST['bunga'];
    $angsuran = $_POST['angsuran'];
    $biaya_admin = $_POST['biaya_admin'];

    // Simpan ke database
    $query = "INSERT INTO pinjaman (id_anggota, jenis_pinjaman, jumlah_pinjaman, tanggal_pencairan, jatuh_tempo, status_pinjaman, tenor, bunga, biaya_admin, angsuran) 
              VALUES (?, ?, ?, ?, ?, 'belum lunas', ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_anggota, $jenis_pinjaman, $jumlah_pinjaman, $tanggal_pencairan, $jatuh_tempo, $tenor, $bunga, $biaya_admin, $angsuran]);

    header('Location: data_pinjaman.php');
    exit();
}

// Hapus Pinjaman
if (isset($_POST['hapus_id'])) {
    $id_pinjaman = $_POST['hapus_id'];
    $query = "DELETE FROM pinjaman WHERE id_pinjaman = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute([$id_pinjaman])) {
        echo "success"; // Respons berhasil
    } else {
        echo "error"; // Respons gagal
    }
    exit();
}

// Update/Edit Pinjaman
if (isset($_POST['edit_pinjaman'])) {
    $id_pinjaman = $_POST['edit_id'];
    $jumlah_pinjaman = $_POST['edit_jumlah_pinjaman'];
    $bunga = $_POST['edit_bunga'];
    $tenor = $_POST['edit_tenor'];
    $angsuran = $_POST['edit_angsuran'];
    $status_pinjaman = $_POST['edit_status_pinjaman'];

    $query = "UPDATE pinjaman SET jumlah_pinjaman = ?, bunga = ?, tenor = ?, angsuran = ?, status_pinjaman = ? WHERE id_pinjaman = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$jumlah_pinjaman, $bunga, $tenor, $angsuran, $status_pinjaman, $id_pinjaman]);

    header('Location: data_pinjaman.php');
    exit();
}
?>
