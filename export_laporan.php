<?php
session_start();
include('db.php');

// Pastikan pengguna login
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Query untuk mengambil data laporan keseluruhan
$query = "SELECT anggota.id_anggota, anggota.nama, anggota.alamat, anggota.no_telp,
          pinjaman.jenis_pinjaman, pinjaman.jumlah_pinjaman, pinjaman.status_pinjaman, pinjaman.tanggal_pencairan, pinjaman.jatuh_tempo,
          angsuran.tanggal_bayar, angsuran.jumlah_bayar, angsuran.denda,
          simpanan.jenis_simpanan, simpanan.jumlah_simpanan, simpanan.sisa_saldo
          FROM anggota
          LEFT JOIN pinjaman ON anggota.id_anggota = pinjaman.id_anggota
          LEFT JOIN angsuran ON pinjaman.id_pinjaman = angsuran.id_pinjaman
          LEFT JOIN simpanan ON anggota.id_anggota = simpanan.id_anggota
          ORDER BY anggota.id_anggota, pinjaman.id_pinjaman, angsuran.id_angsuran, simpanan.id_simpanan";

$stmt = $conn->prepare($query);
$stmt->execute();
$dataLaporan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Atur header untuk file Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keseluruhan.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Cetak header tabel Excel
echo "ID Anggota\tNama\tAlamat\tNo. Telepon\tJenis Pinjaman\tJumlah Pinjaman\tStatus Pinjaman\tTanggal Pencairan\tJatuh Tempo\tTanggal Angsuran\tJumlah Bayar\tDenda\tJenis Simpanan\tJumlah Simpanan\tSisa Saldo\n";

// Cetak data laporan
foreach ($dataLaporan as $row) {
    echo $row['id_anggota'] . "\t";
    echo $row['nama'] . "\t";
    echo $row['alamat'] . "\t";
    echo $row['no_telp'] . "\t";
    echo ($row['jenis_pinjaman'] ?? '-') . "\t";
    echo (isset($row['jumlah_pinjaman']) ? $row['jumlah_pinjaman'] : '-') . "\t";
    echo ucfirst($row['status_pinjaman'] ?? '-') . "\t";
    echo ($row['tanggal_pencairan'] ?? '-') . "\t";
    echo ($row['jatuh_tempo'] ?? '-') . "\t";
    echo ($row['tanggal_bayar'] ?? '-') . "\t";
    echo (isset($row['jumlah_bayar']) ? $row['jumlah_bayar'] : '-') . "\t";
    echo (isset($row['denda']) ? $row['denda'] : '-') . "\t";
    echo ($row['jenis_simpanan'] ?? '-') . "\t";
    echo (isset($row['jumlah_simpanan']) ? $row['jumlah_simpanan'] : '-') . "\t";
    echo (isset($row['sisa_saldo']) ? $row['sisa_saldo'] : '-') . "\n";
}
?>
