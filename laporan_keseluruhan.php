<?php
session_start();
include('db.php');

// Memastikan pengguna telah login
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Query untuk mengambil data lengkap setiap anggota, termasuk pinjaman, angsuran, dan simpanan
$query = "SELECT anggota.id_anggota, anggota.nama, anggota.alamat, anggota.no_telp,
          pinjaman.jenis_pinjaman, pinjaman.jumlah_pinjaman, pinjaman.status_pinjaman, pinjaman.tanggal_pencairan, pinjaman.jatuh_tempo,
          angsuran.tanggal_bayar, angsuran.jumlah_bayar, angsuran.denda,
          simpanan.jenis_simpanan, simpanan.jumlah_simpanan, simpanan.sisa_saldo
          FROM anggota
          LEFT JOIN pinjaman ON anggota.id_anggota = pinjaman.id_anggota
          LEFT JOIN angsuran ON pinjaman.id_pinjaman = angsuran.id_pinjaman
          LEFT JOIN simpanan ON anggota.id_anggota = simpanan.id_anggota
          ORDER BY anggota.id_anggota, pinjaman.id_pinjaman, angsuran.id_angsuran, simpanan.id_simpanan";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $dataLaporan = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keseluruhan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        header {
            background: #0d47a1;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        footer {
            background: #0d47a1;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .card-header {
            background: linear-gradient(45deg, #0d47a1, #42a5f5);
            color: white;
        }
        .btn-export {
            margin-top: 10px;
        }
        .sidebar {
            background: #0d47a1;
            color: white;
            min-height: 100vh;
            padding: 20px;
            position: fixed;
            width: 250px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 10px;
        }
        .sidebar a:hover {
            background: #1e88e5;
            border-radius: 5px;
        }
        .content-wrapper {
            margin-left: 260px;
            padding: 20px;
        }

        .status-lunas {
    color: #28a745 !important;
    font-weight: bold;
}

.status-belum-lunas {
    color: #dc3545 !important;
    font-weight: bold;
}

    </style>
</head>
<body>
<div id="wrapper">
    <?php include('navbar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h1 class="h3 mb-0 text-gray-800">Laporan Keseluruhan Anggota</h1>
            </nav>

            <div class="container-fluid">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-clipboard-list"></i> Data Laporan Keseluruhan</h6>
                    </div>
                    <div class="card-body">
                    <div class="row mb-4">
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table id="tabelLaporan" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Anggota</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>No. Telepon</th>
                                        <th>Jenis Pinjaman</th>
                                        <th>Jumlah Pinjaman</th>
                                        <th>Status Pinjaman</th>
                                        <th>Tanggal Pencairan</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Tanggal Angsuran</th>
                                        <th>Jumlah Bayar</th>
                                        <th>Denda</th>
                                        <th>Jenis Simpanan</th>
                                        <th>Jumlah Simpanan</th>
                                        <th>Sisa Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dataLaporan)): ?>
                                        <tr>
                                            <td colspan="15" class="text-center">Tidak ada data laporan keseluruhan.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($dataLaporan as $row): ?>
                                            <tr>
                                                <td><span class="badge badge-info">#<?php echo $row['id_anggota']; ?></span></td>
                                                <td><?php echo $row['nama']; ?></td>
                                                <td><?php echo $row['alamat']; ?></td>
                                                <td><?php echo $row['no_telp']; ?></td>
                                                <td><?php echo $row['jenis_pinjaman'] ?? '-'; ?></td>
                                                <td><?php echo isset($row['jumlah_pinjaman']) ? 'Rp ' . number_format($row['jumlah_pinjaman'], 2, ',', '.') : '-'; ?></td>
                                                <td class="<?php echo ($row['status_pinjaman'] == 'lunas') ? 'status-lunas' : 'status-belum-lunas'; ?>">
                                                    <?php echo ucfirst($row['status_pinjaman'] ?? '-'); ?>
                                                </td>
                                                <td><?php echo $row['tanggal_pencairan'] ?? '-'; ?></td>
                                                <td><?php echo $row['jatuh_tempo'] ?? '-'; ?></td>
                                                <td><?php echo $row['tanggal_bayar'] ?? '-'; ?></td>
                                                <td><?php echo isset($row['jumlah_bayar']) ? 'Rp ' . number_format($row['jumlah_bayar'], 2, ',', '.') : '-'; ?></td>
                                                <td><?php echo isset($row['denda']) ? 'Rp ' . number_format($row['denda'], 2, ',', '.') : '-'; ?></td>
                                                <td><?php echo $row['jenis_simpanan'] ?? '-'; ?></td>
                                                <td><?php echo isset($row['jumlah_simpanan']) ? 'Rp ' . number_format($row['jumlah_simpanan'], 2, ',', '.') : '-'; ?></td>
                                                <td><?php echo isset($row['sisa_saldo']) ? 'Rp ' . number_format($row['sisa_saldo'], 2, ',', '.') : '-'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-right btn-export">
                    <a href="export_laporan.php" class="btn btn-success"><i class="fas fa-file-excel"></i> Export ke Excel</a>
                </div>
                </div>
                
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelLaporan').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "scrollX": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.11.3/i18n/id.json"
            }
        });
    });
</script>
</body>
</html>
