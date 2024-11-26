<?php
include('db.php');

// Tambah Simpanan Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['edit_simpanan']) && isset($_POST['jumlah_simpanan'])) {
    $id_anggota = $_POST['id_anggota'];
    $jenis_simpanan = $_POST['jenis_simpanan'];
    $jumlah_simpanan = $_POST['jumlah_simpanan'];
    $tanggal_simpanan = date('Y-m-d');

    // Simpan ke database
    $query = "INSERT INTO simpanan (id_anggota, jenis_simpanan, jumlah_simpanan, tanggal_simpanan, sisa_saldo) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_anggota, $jenis_simpanan, $jumlah_simpanan, $tanggal_simpanan, $jumlah_simpanan]);

    // Redirect setelah berhasil tambah
    header('Location: data_simpanan.php');
    exit();
}

// Hapus Simpanan
if (isset($_POST['hapus_id'])) {
    $id_simpanan = $_POST['hapus_id'];
    $query = "DELETE FROM simpanan WHERE id_simpanan = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id_simpanan]);

    // Redirect setelah berhasil hapus
    header('Location: data_simpanan.php');
    exit();
}

// Update/Edit Simpanan
if (isset($_POST['edit_simpanan'])) {
    $id_simpanan = $_POST['edit_id'];
    $jumlah_simpanan = $_POST['edit_jumlah_simpanan'];

    $query = "UPDATE simpanan SET jumlah_simpanan = ? WHERE id_simpanan = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$jumlah_simpanan, $id_simpanan]);

    // Redirect setelah berhasil edit
    header('Location: data_simpanan.php');
    exit();
}

// Penarikan Simpanan
if (isset($_POST['tarik_id'])) {
    $id_simpanan = $_POST['tarik_id'];
    $jumlah_tarik = $_POST['jumlah_tarik'];
    $penalti = $_POST['penalti'];

    // Cek saldo saat ini
    $query_saldo = "SELECT sisa_saldo FROM simpanan WHERE id_simpanan = ?";
    $stmt_saldo = $conn->prepare($query_saldo);
    $stmt_saldo->execute([$id_simpanan]);
    $current_saldo = $stmt_saldo->fetchColumn();

    if ($jumlah_tarik + $penalti > $current_saldo) {
        // Jika penarikan melebihi saldo
        header('Location: data_simpanan.php?error=Saldo tidak mencukupi');
        exit();
    }

    // Hitung saldo baru setelah penarikan
    $query = "UPDATE simpanan SET sisa_saldo = sisa_saldo - ? WHERE id_simpanan = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$jumlah_tarik + $penalti, $id_simpanan]);

    // Redirect setelah berhasil tarik
    header('Location: data_simpanan.php?success=Penarikan berhasil');
    exit();
}

?>
