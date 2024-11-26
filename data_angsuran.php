<?php
session_start();
include('db.php');

// Proteksi halaman agar hanya bisa diakses setelah login
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Query untuk menampilkan anggota dan total pinjaman, total angsuran, serta status pinjaman
$query = "SELECT ag.id_anggota, ag.nama, COUNT(p.id_pinjaman) as jumlah_pinjaman, 
          SUM(p.jumlah_pinjaman) as total_pinjaman, 
          (SELECT SUM(a.jumlah_bayar) FROM angsuran a JOIN pinjaman p2 ON a.id_pinjaman = p2.id_pinjaman WHERE p2.id_anggota = ag.id_anggota) as total_angsuran,
          CASE WHEN SUM(p.jumlah_pinjaman) <= (SELECT SUM(a.jumlah_bayar) FROM angsuran a JOIN pinjaman p2 ON a.id_pinjaman = p2.id_pinjaman WHERE p2.id_anggota = ag.id_anggota) 
          THEN 'Lunas' ELSE 'Belum Lunas' END as status_pinjaman
          FROM anggota ag
          LEFT JOIN pinjaman p ON ag.id_anggota = p.id_anggota
          GROUP BY ag.id_anggota";
$stmt = $conn->prepare($query);
$stmt->execute();
$anggota_pinjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Angsuran</title>
    <!-- Bootstrap dan SB Admin 2 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Font Awesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS untuk memperbesar font dan memperbaiki layout -->
    <style>
        body {
            font-size: 18px; /* Ukuran font dasar seluruh halaman */
        }

        .table th, .table td {
            font-size: 16px; /* Ukuran font tabel */
            padding: 12px; /* Memberi padding agar tabel lebih rapi */
        }

        h1.h3 {
            font-size: 28px; /* Ukuran font untuk judul halaman */
            font-weight: 600;
        }

        .btn {
            font-size: 16px; /* Ukuran font tombol */
            margin-right: 10px; /* Menambah jarak antar tombol */
        }

        .modal-title, .modal-body label, .modal-footer button {
            font-size: 18px; /* Ukuran font dalam modal */
        }

        .table .btn i {
            margin-right: 6px; /* Memberi jarak antara ikon dan teks pada tombol */
        }

        .table td, .table th {
            vertical-align: middle; /* Membuat teks rata tengah secara vertikal di tabel */
        }

        /* Card untuk statistik */
        .card-body h5 {
            font-size: 20px; /* Ukuran font untuk judul statistik */
            font-weight: 600;
        }
    </style>
</head>

<body>
<div id="wrapper">
    <?php include('navbar.php'); ?> <!-- Include Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h1 class="h3 mb-0 text-gray-800">Data Angsuran</h1>
            </nav>
            <div class="container-fluid">
                <!-- Tabel Data Anggota dan Total Pinjaman -->
                <table id="anggotaTable" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID Anggota</th>
                            <th>Nama Anggota</th>
                            <th>Jumlah Pinjaman</th>
                            <th>Total Pinjaman</th>
                            <th>Total Angsuran</th>
                            <th>Status Pinjaman</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($anggota_pinjaman as $row): ?>
                        <tr>
                            <td><?php echo $row['id_anggota']; ?></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['jumlah_pinjaman']; ?></td>
                            <td>Rp <?php echo number_format($row['total_pinjaman'], 2, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['total_angsuran'], 2, ',', '.'); ?></td>
                            <td><?php echo $row['status_pinjaman']; ?></td>
                            <td>
                                <a href="detail_angsuran.php?id_anggota=<?php echo $row['id_anggota']; ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery, Bootstrap JS, dan DataTables -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#anggotaTable').DataTable({
            "scrollX": true
        });
    });
</script>
</body>
</html>
