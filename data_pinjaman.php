<?php
session_start();
include('db.php');

// Proteksi halaman agar hanya bisa diakses setelah login
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Query untuk menampilkan data pinjaman dengan nama anggota
$query = "SELECT p.*, a.nama FROM pinjaman p JOIN anggota a ON p.id_anggota = a.id_anggota";
$stmt = $conn->prepare($query);
$stmt->execute();
$pinjaman = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch data pinjaman

// Query untuk menghitung statistik pinjaman lunas vs belum lunas
$query_stats = "SELECT COUNT(*) as total, 
                SUM(CASE WHEN status_pinjaman = 'lunas' THEN 1 ELSE 0 END) as lunas, 
                SUM(CASE WHEN status_pinjaman = 'belum lunas' THEN 1 ELSE 0 END) as belum_lunas 
                FROM pinjaman";
$stmt_stats = $conn->prepare($query_stats);
$stmt_stats->execute();
$stats = $stmt_stats->fetch(PDO::FETCH_ASSOC); // Statistik pinjaman lunas dan belum lunas

// Query untuk menghitung statistik berdasarkan jenis pinjaman
$query_jenis_stats = "SELECT jenis_pinjaman, COUNT(*) as total FROM pinjaman GROUP BY jenis_pinjaman";
$stmt_jenis_stats = $conn->prepare($query_jenis_stats);
$stmt_jenis_stats->execute();
$jenis_stats = $stmt_jenis_stats->fetchAll(PDO::FETCH_ASSOC); // Statistik pinjaman per jenis
$mingguan_count = 0;
$bulanan_count = 0;
foreach ($jenis_stats as $jenis) {
    if ($jenis['jenis_pinjaman'] == 'Mingguan') {
        $mingguan_count = $jenis['total'];
    } else if ($jenis['jenis_pinjaman'] == 'Bulanan') {
        $bulanan_count = $jenis['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pinjaman</title>
    <!-- Bootstrap dan SB Admin 2 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Font Awesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
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

        .card-body h5 {
            font-size: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div id="wrapper">
    <?php include('navbar.php'); ?> <!-- Include Sidebar dari navbar.php -->

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h1 class="h3 mb-0 text-gray-800">Data Pinjaman</h1>
            </nav>

            <div class="container-fluid">
                <!-- Tombol Tambah Pinjaman -->
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahPinjamanModal">Tambah Pinjaman</button>

                <!-- Tabel Data Pinjaman -->
                <table id="tabelPinjaman" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No Anggota</th>
                            <th>Nama Anggota</th>
                            <th>Jenis Pinjaman</th>
                            <th>Jumlah Pinjaman</th>
                            <th>Bunga (%)</th>
                            <th>Tenor</th>
                            <th>Nominal Angsuran (Rp)</th>
                            <th>Status Pinjaman</th>
                            <th>Tanggal Pencairan</th>
                            <th>Jatuh Tempo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tabelPinjamanBody">
                        <?php foreach ($pinjaman as $row): ?>
                        <tr id="row-<?php echo $row['id_pinjaman']; ?>">
                            <td><?php echo $row['id_anggota']; ?></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['jenis_pinjaman']; ?></td>
                            <td>Rp <?php echo number_format($row['jumlah_pinjaman'], 2, ',', '.'); ?></td>
                            <td><?php echo $row['bunga']; ?>%</td>
                            <td>
                                <?php 
                                    // Tampilkan tenor sesuai jenis pinjaman (Mingguan/Bulanan)
                                    if ($row['jenis_pinjaman'] == 'Mingguan') {
                                        echo $row['tenor'] . ' Minggu';
                                    } else {
                                        echo $row['tenor'] . ' Bulan';
                                    }
                                ?>
                            </td>
                            <td>Rp <?php echo number_format($row['angsuran'], 2, ',', '.'); ?></td>
                            <td><?php echo $row['status_pinjaman']; ?></td>
                            <td><?php echo $row['tanggal_pencairan']; ?></td>
                            <td><?php echo $row['jatuh_tempo']; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $row['id_pinjaman']; ?>" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm hapus-btn" data-id="<?php echo $row['id_pinjaman']; ?>" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Statistik Pinjaman -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Statistik Pinjaman Berdasarkan Status</h5>
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5>Statistik Pinjaman Berdasarkan Jenis</h5>
                                <canvas id="jenisPinjamanChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pinjaman -->
<div class="modal fade" id="tambahPinjamanModal" tabindex="-1" aria-labelledby="tambahPinjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formTambahPinjaman" method="POST" action="proses_pinjaman.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPinjamanModalLabel">Tambah Pinjaman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <select class="form-control" name="id_anggota" required>
                            <?php
                            $anggota_query = "SELECT id_anggota, nama FROM anggota";
                            $anggota_stmt = $conn->prepare($anggota_query);
                            $anggota_stmt->execute();
                            $anggota_list = $anggota_stmt->fetchAll();
                            foreach ($anggota_list as $anggota) {
                                echo "<option value='{$anggota['id_anggota']}'>{$anggota['nama']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Pinjaman</label>
                        <select class="form-control" name="jenis_pinjaman" required>
                            <option value="Mingguan">Mingguan</option>
                            <option value="Bulanan">Bulanan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Pinjaman</label>
                        <input type="number" class="form-control" name="jumlah_pinjaman" required>
                    </div>
                    <div class="form-group">
                        <label>Bunga (%)</label>
                        <input type="number" class="form-control" name="bunga" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Tenor (dalam minggu/bulan)</label>
                        <input type="number" class="form-control" name="tenor" required>
                    </div>
                    <div class="form-group">
                        <label>Nominal Angsuran (Rp)</label>
                        <input type="number" class="form-control" name="angsuran" required>
                    </div>
                    <div class="form-group">
                        <label>Biaya Administrasi (%)</label>
                        <input type="number" class="form-control" name="biaya_admin" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Pencairan</label>
                        <input type="date" class="form-control" name="tanggal_pencairan" required>
                    </div>
                    <div class="form-group">
                        <label>Jatuh Tempo</label>
                        <input type="date" class="form-control" name="jatuh_tempo" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Pinjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pinjaman -->
<div class="modal fade" id="editPinjamanModal" tabindex="-1" aria-labelledby="editPinjamanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditPinjaman" method="POST" action="proses_pinjaman.php">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPinjamanModalLabel">Edit Pinjaman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Jumlah Pinjaman</label>
                        <input type="number" class="form-control" name="edit_jumlah_pinjaman" id="edit_jumlah_pinjaman" required>
                    </div>
                    <div class="form-group">
                        <label>Bunga (%)</label>
                        <input type="number" class="form-control" name="edit_bunga" id="edit_bunga" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Tenor (dalam minggu/bulan)</label>
                        <input type="number" class="form-control" name="edit_tenor" id="edit_tenor" required>
                    </div>
                    <div class="form-group">
                        <label>Nominal Angsuran (Rp)</label>
                        <input type="number" class="form-control" name="edit_angsuran" id="edit_angsuran" required>
                    </div>
                    <div class="form-group">
                        <label>Status Pinjaman</label>
                        <select class="form-control" name="edit_status_pinjaman" id="edit_status_pinjaman" required>
                            <option value="belum lunas">Belum Lunas</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" name="edit_pinjaman">Update Pinjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery, Bootstrap JS, dan DataTables -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<!-- Chart.js untuk visualisasi data -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables dengan scrollX dan paging
        $('#tabelPinjaman').DataTable({
            "scrollX": true,
            "paging": true,
            "searching": true
        });

        // Validasi form Tambah Pinjaman
        $('#formTambahPinjaman').on('submit', function(e) {
            var jumlahPinjaman = $('input[name="jumlah_pinjaman"]').val();
            var bunga = $('input[name="bunga"]').val();
            if (jumlahPinjaman <= 0) {
                alert('Jumlah pinjaman harus lebih dari 0.');
                e.preventDefault(); // Mencegah pengiriman form
            }
            if (bunga < 0 || bunga > 100) {
                alert('Bunga harus antara 0% sampai 100%.');
                e.preventDefault(); // Mencegah pengiriman form
            }
        });

        // Statistik Pinjaman Berdasarkan Status
        var ctx = document.getElementById('statusChart').getContext('2d');
        var statusChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Lunas', 'Belum Lunas'],
                datasets: [{
                    label: 'Jumlah Pinjaman',
                    data: [<?php echo $stats['lunas']; ?>, <?php echo $stats['belum_lunas']; ?>],
                    backgroundColor: ['#28a745', '#dc3545'],
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Statistik Pinjaman Berdasarkan Jenis
        var ctxJenis = document.getElementById('jenisPinjamanChart').getContext('2d');
        var jenisPinjamanChart = new Chart(ctxJenis, {
            type: 'pie',
            data: {
                labels: ['Mingguan', 'Bulanan'],
                datasets: [{
                    label: 'Jenis Pinjaman',
                    data: [<?php echo $mingguan_count; ?>, <?php echo $bulanan_count; ?>],
                    backgroundColor: ['#4e73df', '#1cc88a'],
                }]
            }
        });

        // Hapus Pinjaman dengan AJAX
        $('.hapus-btn').on('click', function() {
            if (confirm('Yakin ingin menghapus pinjaman ini?')) {
                var id = $(this).data('id');
                $.ajax({
                    url: 'proses_pinjaman.php',
                    type: 'POST',
                    data: { hapus_id: id },
                    success: function(response) {
                        if (response === 'success') {
                            $('#row-' + id).remove();
                        } else {
                            alert('Gagal menghapus pinjaman.');
                        }
                    }
                });
            }
        });

        // Edit Pinjaman menggunakan AJAX
        $('.edit-btn').on('click', function() {
            var id = $(this).data('id');
            $.ajax({
                url: 'ambil_pinjaman.php',
                type: 'GET',
                data: { id: id },
                success: function(response) {
                    var pinjaman = JSON.parse(response);
                    $('#edit_id').val(pinjaman.id_pinjaman);
                    $('#edit_jumlah_pinjaman').val(pinjaman.jumlah_pinjaman);
                    $('#edit_bunga').val(pinjaman.bunga);
                    $('#edit_tenor').val(pinjaman.tenor);
                    $('#edit_angsuran').val(pinjaman.angsuran);
                    $('#edit_status_pinjaman').val(pinjaman.status_pinjaman);
                    $('#editPinjamanModal').modal('show');
                }
            });
        });
    });
</script>
</body>
</html>
