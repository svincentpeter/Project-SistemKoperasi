<?php
session_start();
include('db.php'); // Koneksi ke database

// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Query untuk menghitung total anggota
$query_anggota = "SELECT COUNT(*) AS total_anggota FROM anggota";
$stmt_anggota = $conn->prepare($query_anggota);
$stmt_anggota->execute();
$total_anggota = $stmt_anggota->fetchColumn();

// Query untuk menghitung total pinjaman
$query_pinjaman = "SELECT SUM(jumlah_pinjaman) AS total_pinjaman FROM pinjaman";
$stmt_pinjaman = $conn->prepare($query_pinjaman);
$stmt_pinjaman->execute();
$total_pinjaman = $stmt_pinjaman->fetchColumn();

// Query untuk menghitung total simpanan
$query_simpanan = "SELECT SUM(jumlah_simpanan) AS total_simpanan FROM simpanan";
$stmt_simpanan = $conn->prepare($query_simpanan);
$stmt_simpanan->execute();
$total_simpanan = $stmt_simpanan->fetchColumn();

// Query untuk menghitung pinjaman belum dibayar
$query_pinjaman_terlambat = "SELECT COUNT(*) AS overdue FROM pinjaman WHERE status_pinjaman = 'belum lunas'";
$stmt_overdue = $conn->prepare($query_pinjaman_terlambat);
$stmt_overdue->execute();
$jumlah_pinjaman_terlambat = $stmt_overdue->fetchColumn();

// Query untuk menghitung total angsuran yang sudah dibayar
$query_angsuran = "SELECT SUM(jumlah_bayar) AS total_angsuran FROM angsuran";
$stmt_angsuran = $conn->prepare($query_angsuran);
$stmt_angsuran->execute();
$total_angsuran = $stmt_angsuran->fetchColumn();

// Query untuk menghitung total simpanan berdasarkan jenis
$query_simpanan_jenis = "SELECT jenis_simpanan, SUM(jumlah_simpanan) AS total_per_jenis FROM simpanan GROUP BY jenis_simpanan";
$stmt_simpanan_jenis = $conn->prepare($query_simpanan_jenis);
$stmt_simpanan_jenis->execute();
$simpanan_per_jenis = $stmt_simpanan_jenis->fetchAll(PDO::FETCH_ASSOC);

// Query untuk menghitung total transaksi
$query_total_transaksi = "SELECT SUM(jumlah_transaksi) AS total_transaksi FROM transaksi";
$stmt_total_transaksi = $conn->prepare($query_total_transaksi);
$stmt_total_transaksi->execute();
$total_transaksi = $stmt_total_transaksi->fetchColumn();

// Query untuk menghitung total transaksi per jenis
$query_transaksi_per_jenis = "SELECT tipe_transaksi, SUM(jumlah_transaksi) AS total_per_jenis FROM transaksi GROUP BY tipe_transaksi";
$stmt_transaksi_per_jenis = $conn->prepare($query_transaksi_per_jenis);
$stmt_transaksi_per_jenis->execute();
$transaksi_per_jenis = $stmt_transaksi_per_jenis->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mendapatkan data pinjaman berdasarkan bulan
$query_pinjaman_bulanan = "SELECT DATE_FORMAT(tanggal_pinjaman, '%Y-%m') AS bulan, SUM(jumlah_pinjaman) AS total_per_bulan FROM pinjaman GROUP BY bulan ORDER BY bulan";
$stmt_pinjaman_bulanan = $conn->prepare($query_pinjaman_bulanan);
$stmt_pinjaman_bulanan->execute();
$pinjaman_bulanan = $stmt_pinjaman_bulanan->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Koperasi</title>
    <!-- Bootstrap and SB Admin 2 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar-brand img {
            height: 50px;
            width: auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background: linear-gradient(45deg, #0d47a1, #42a5f5); /* Warna gradien menyelaraskan dengan sidebar */
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header i {
            font-size: 1.5rem;
        }
        .card-body h4 {
            font-size: 1.75rem;
            font-weight: bold;
        }
        .dropdown-hover {
            position: relative;
            display: inline-block;
        }
        .dropdown-hover .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 10px;
            z-index: 1;
            border-radius: 5px;
        }
        .dropdown-hover:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <?php include('navbar.php'); ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h1 class="h3 mb-0 text-gray-800">Dashboard Koperasi</h1>
                    
                    <!-- Ikon Notifikasi -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Jika ada notifikasi, tampilkan badge -->
                                <?php if ($jumlah_pinjaman_terlambat > 0): ?>
                                    <span class="badge badge-danger badge-counter"><?php echo $jumlah_pinjaman_terlambat; ?></span>
                                <?php endif; ?>
                            </a>
                            <!-- Dropdown untuk notifikasi -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">Notifikasi</h6>
                                <?php if ($jumlah_pinjaman_terlambat > 0): ?>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <div class="mr-3">
                                            <div class="icon-circle bg-warning">
                                                <i class="fas fa-exclamation-triangle text-white"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold">Terdapat <?php echo $jumlah_pinjaman_terlambat; ?> pinjaman yang belum dibayar.</span>
                                        </div>
                                    </a>
                                <?php else: ?>
                                    <a class="dropdown-item text-center small text-gray-500">Tidak ada notifikasi</a>
                                <?php endif; ?>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <!-- Statistik Utama -->
                    <div class="row">
                        <!-- Total Anggota dengan Detail -->
                        <div class="col-lg-4 col-md-6 mb-4 dropdown-hover">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-users fa-2x"></i> Total Anggota</h6>
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <h4 class="display-4 text-dark"><?php echo $total_anggota; ?></h4>
                                    <i class="fas fa-users fa-3x text-primary"></i>
                                </div>
                                <div class="dropdown-content">
                                    <h6>Detail Anggota:</h6>
                                    <ul class="list-unstyled">
                                        <li>Jumlah Anggota Aktif: <?php echo $total_anggota; ?></li>
                                        <!-- Anda bisa menambahkan detail lainnya di sini -->
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Total Pinjaman dengan Detail -->
                        <div class="col-lg-4 col-md-6 mb-4 dropdown-hover">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-dollar-sign fa-2x"></i> Total Pinjaman</h6>
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <h4 class="display-4 text-dark">Rp <?php echo number_format($total_pinjaman, 2, ',', '.'); ?></h4>
                                    <i class="fas fa-dollar-sign fa-3x text-danger"></i>
                                </div>
                                <div class="dropdown-content">
                                    <h6>Detail Pinjaman:</h6>
                                    <ul class="list-unstyled">
                                        <li>Jumlah Pinjaman Belum Lunas: <?php echo $jumlah_pinjaman_terlambat; ?></li>
                                        <!-- Anda bisa menambahkan detail lainnya di sini -->
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Total Simpanan dengan Detail per Jenis -->
                        <div class="col-lg-4 col-md-6 mb-4 dropdown-hover">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-coins fa-2x"></i> Total Simpanan</h6>
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <h4 class="display-4 text-dark">Rp <?php echo number_format($total_simpanan, 2, ',', '.'); ?></h4>
                                    <i class="fas fa-coins fa-3x text-success"></i>
                                </div>
                                <div class="dropdown-content">
                                    <h6>Detail Simpanan per Jenis:</h6>
                                    <ul class="list-unstyled">
                                        <?php foreach ($simpanan_per_jenis as $simpanan): ?>
                                            <li><?php echo $simpanan['jenis_simpanan']; ?>: Rp <?php echo number_format($simpanan['total_per_jenis'], 2, ',', '.'); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Total Angsuran yang Sudah Dibayar dengan Detail -->
                        <div class="col-lg-4 col-md-6 mb-4 dropdown-hover">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-hand-holding-usd fa-2x"></i> Total Angsuran Dibayar</h6>
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <h4 class="display-4 text-dark">Rp <?php echo number_format($total_angsuran, 2, ',', '.'); ?></h4>
                                    <i class="fas fa-hand-holding-usd fa-3x text-info"></i>
                                </div>
                                <div class="dropdown-content">
                                    <h6>Detail Angsuran:</h6>
                                    <ul class="list-unstyled">
                                        <li>Total Angsuran Dibayar: Rp <?php echo number_format($total_angsuran, 2, ',', '.'); ?></li>
                                        <!-- Anda bisa menambahkan detail lainnya di sini -->
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Total Transaksi dengan Detail -->
                        <div class="col-lg-4 col-md-6 mb-4 dropdown-hover">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-exchange-alt fa-2x"></i> Total Transaksi</h6>
                                </div>
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <h4 class="display-4 text-dark">Rp <?php echo number_format($total_transaksi, 2, ',', '.'); ?></h4>
                                    <i class="fas fa-exchange-alt fa-3x text-warning"></i>
                                </div>
                                <div class="dropdown-content">
                                    <h6>Detail Transaksi per Jenis:</h6>
                                    <ul class="list-unstyled">
                                        <?php foreach ($transaksi_per_jenis as $transaksi): ?>
                                            <li><?php echo $transaksi['tipe_transaksi']; ?>: Rp <?php echo number_format($transaksi['total_per_jenis'], 2, ',', '.'); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadDashboardData() {
    $.ajax({
        url: 'load_dashboard_data.php',
        method: 'GET',
        success: function (response) {
            $('#dashboard-content').html(response);
        }
    });
}

document.addEventListener('DOMContentLoaded', loadDashboardData);

    </script>
</body>
</html>
