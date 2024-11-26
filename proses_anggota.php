<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nama'])) {
        // Tambah anggota baru
        $nama = $_POST['nama'];
        $jenis_kelamin = $_POST['jenis_kelamin'];
        $tempat_lahir = $_POST['tempat_lahir'];
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $alamat = $_POST['alamat'];
        $no_telp = $_POST['no_telp'];
        $email = $_POST['email'];
        $nik = $_POST['nik'];
        $pekerjaan = $_POST['pekerjaan'];
        $tanggal_bergabung = date('Y-m-d');

        // Simpan data anggota ke database
        $query = "INSERT INTO anggota (nama, jenis_kelamin, tempat_lahir, tanggal_lahir, alamat, no_telp, email, nik, pekerjaan, tanggal_bergabung) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nama, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $alamat, $no_telp, $email, $nik, $pekerjaan, $tanggal_bergabung]);

        header('Location: data_anggota.php');
        exit();
    }

    if (isset($_POST['edit_id'])) {
        // Edit anggota
        $id = $_POST['edit_id'];
        $nama = $_POST['edit_nama'];
        $jenis_kelamin = $_POST['edit_jenis_kelamin'];
        $tempat_lahir = $_POST['edit_tempat_lahir'];
        $tanggal_lahir = $_POST['edit_tanggal_lahir'];
        $alamat = $_POST['edit_alamat'];
        $no_telp = $_POST['edit_no_telp'];
        $email = $_POST['edit_email'];
        $nik = $_POST['edit_nik'];
        $pekerjaan = $_POST['edit_pekerjaan'];

        $query = "UPDATE anggota SET nama = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, alamat = ?, no_telp = ?, email = ?, nik = ?, pekerjaan = ? WHERE id_anggota = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$nama, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $alamat, $no_telp, $email, $nik, $pekerjaan, $id]);

        header('Location: data_anggota.php');
        exit();
    }

    if (isset($_POST['delete_id'])) {
        // Hapus anggota
        $id = $_POST['delete_id'];
        $query = "DELETE FROM anggota WHERE id_anggota = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id]);

        echo "success";
    }
}
?>
