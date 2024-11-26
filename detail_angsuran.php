<?php
session_start();
include('db.php');

// Ambil ID Anggota dari parameter URL
$id_anggota = $_GET['id_anggota'];

// Query untuk mengambil nama anggota
$query_anggota = "SELECT nama FROM anggota WHERE id_anggota = ?";
$stmt_anggota = $conn->prepare($query_anggota);
$stmt_anggota->execute([$id_anggota]);
$anggota = $stmt_anggota->fetch(PDO::FETCH_ASSOC);

// Query untuk menampilkan data angsuran berdasarkan anggota
$query = "SELECT a.id_angsuran, p.jenis_pinjaman, p.jumlah_pinjaman, a.tanggal_bayar, a.jumlah_bayar, a.denda
          FROM angsuran a
          JOIN pinjaman p ON a.id_pinjaman = p.id_pinjaman
          WHERE p.id_anggota = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id_anggota]);
$angsuran = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Query untuk menampilkan pinjaman yang belum lunas
$query_pinjaman = "SELECT p.id_pinjaman, p.jenis_pinjaman FROM pinjaman p 
                   WHERE p.id_anggota = ? AND p.status_pinjaman = 'belum lunas'";
$stmt_pinjaman = $conn->prepare($query_pinjaman);
$stmt_pinjaman->execute([$id_anggota]);
$pinjaman_list = $stmt_pinjaman->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Angsuran</title>
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
            font-size: 18px;
        }

        .table th, .table td {
            font-size: 16px;
            padding: 12px;
        }

        h1.h3 {
            font-size: 28px;
            font-weight: 600;
        }

        .btn {
            font-size: 16px;
            margin-right: 10px;
        }

        .modal-title, .modal-body label, .modal-footer button {
            font-size: 18px;
        }

        .table .btn i {
            margin-right: 6px;
        }

        .table td, .table th {
            vertical-align: middle;
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
                <h1 class="h3 mb-0 text-gray-800">Detail Angsuran - <?php echo $anggota['nama']; ?></h1>
            </nav>
            <div class="container-fluid">
                <!-- Tombol Tambah Angsuran -->
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahAngsuranModal">Tambah Angsuran</button>

                <!-- Tabel Data Angsuran -->
                <table id="angsuranTable" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>Jenis Pinjaman</th>
                            <th>Jumlah Pinjaman</th>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah Bayar</th>
                            <th>Denda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($angsuran as $row): ?>
                        <tr>
                            <td><?php echo $row['jenis_pinjaman']; ?></td>
                            <td>Rp <?php echo number_format($row['jumlah_pinjaman'], 2, ',', '.'); ?></td>
                            <td><?php echo $row['tanggal_bayar']; ?></td>
                            <td>Rp <?php echo number_format($row['jumlah_bayar'], 2, ',', '.'); ?></td>
                            <td>Rp <?php echo number_format($row['denda'], 2, ',', '.'); ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm hapus-btn" data-id="<?php echo $row['id_angsuran']; ?>">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Angsuran -->
<div class="modal fade" id="tambahAngsuranModal" tabindex="-1" aria-labelledby="tambahAngsuranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formTambahAngsuran" method="POST" action="proses_angsuran.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAngsuranModalLabel">Tambah Angsuran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Hidden input untuk mengirimkan id_anggota -->
                    <input type="hidden" name="id_anggota" value="<?php echo $id_anggota; ?>">
                    <div class="form-group">
                        <label>Pinjaman</label>
                        <select class="form-control" name="id_pinjaman" required>
                            <?php foreach ($pinjaman_list as $pinjaman): ?>
                                <option value="<?php echo $pinjaman['id_pinjaman']; ?>">
                                    <?php echo $pinjaman['jenis_pinjaman']; ?> - ID Pinjaman: <?php echo $pinjaman['id_pinjaman']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Bayar</label>
                        <input type="date" class="form-control" name="tanggal_bayar" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Bayar (Rp)</label>
                        <input type="number" class="form-control" name="jumlah_bayar" required>
                    </div>
                    <div class="form-group">
                        <label>Denda (Rp)</label>
                        <input type="number" class="form-control" name="denda" value="0.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Angsuran</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- jQuery, Bootstrap JS, dan DataTables -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
    $('#angsuranTable').DataTable({
        "scrollX": true
    });

    // Hapus angsuran menggunakan AJAX
    $('.hapus-btn').on('click', function() {
        if (confirm('Yakin ingin menghapus angsuran ini?')) {
            var id = $(this).data('id');
            $.ajax({
                url: 'proses_angsuran.php',
                type: 'POST',
                data: { delete_id: id },
                success: function(response) {
                    if (response == 'success') {
                        location.reload(); // Muat ulang halaman setelah sukses
                    } else {
                        alert('Gagal menghapus angsuran.');
                    }
                }
            });
        }
    });
});

</script>
</body>
</html>
