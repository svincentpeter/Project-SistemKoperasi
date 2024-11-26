<?php
session_start();
include('db.php');

// Proteksi halaman agar hanya bisa diakses setelah login
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Query untuk menampilkan data simpanan
$query = "SELECT s.*, a.nama FROM simpanan s JOIN anggota a ON s.id_anggota = a.id_anggota";
$stmt = $conn->prepare($query);
$stmt->execute();
$simpanan = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch data simpanan dan nama anggota
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Simpanan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
    .jenis-simpanan-info {
        font-size: 14px;        /* Ukuran teks lebih besar */
        font-weight: bold;      /* Membuat teks lebih tebal */
        color: #5a5c69;         /* Warna teks agar lebih menonjol */
        padding: 10px 0;        /* Memberi jarak vertikal */
        display: block;         /* Membuat elemen blok agar rapi */
        background-color: #f8f9fc;  /* Latar belakang agar terlihat jelas */
        border-radius: 5px;     /* Membuat sudut sedikit melengkung */
    }

    .table th, .table td {
        font-size: 16px;        /* Ukuran font tabel */
        padding: 12px;          /* Memberi padding pada sel tabel */
    }

    h1.h3 {
        font-size: 28px;        /* Ukuran font untuk judul halaman */
        font-weight: 600;
    }

    .btn {
        font-size: 16px;        /* Ukuran font tombol */
        margin-right: 10px;     /* Menambah jarak antar tombol */
    }

    .modal-title, .modal-body label, .modal-footer button {
        font-size: 18px;        /* Ukuran font dalam modal */
    }

    .table .btn i {
        margin-right: 6px;      /* Memberi jarak antara ikon dan teks pada tombol */
    }

    .table td, .table th {
        vertical-align: middle; /* Membuat teks rata tengah secara vertikal di tabel */
    }
</style>

</style>
</head>
<body>
<div id="wrapper">
    <?php include('navbar.php'); ?> <!-- Include Sidebar dari navbar.php -->

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h1 class="h3 mb-0 text-gray-800">Data Simpanan</h1>
            </nav>

            <div class="container-fluid">
                <!-- Tombol Tambah Simpanan -->
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahSimpananModal">Tambah Simpanan</button>

                <!-- Input Search -->
                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan nama anggota atau jenis simpanan...">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="searchButton">Cari</button>
                    </div>
                </div>

                <!-- Tabel Data Simpanan -->
                <table class="table table-bordered table-striped" id="simpananTable">
                    <thead>
                        <tr>
                            <th>Nama Anggota</th>
                            <th>Jenis Simpanan</th>
                            <th>Jumlah Simpanan</th>
                            <th>Tanggal Simpanan</th>
                            <th>Sisa Saldo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($simpanan) > 0): ?>
                            <?php foreach ($simpanan as $row): ?>
                            <tr>
                                <td><?php echo $row['nama']; ?></td>
                                <td><?php echo $row['jenis_simpanan']; ?></td>
                                <td>Rp <?php echo number_format($row['jumlah_simpanan'], 2, ',', '.'); ?></td>
                                <td><?php echo $row['tanggal_simpanan']; ?></td>
                                <td>Rp <?php echo number_format($row['sisa_saldo'], 2, ',', '.'); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $row['id_simpanan']; ?>" data-toggle="modal" data-target="#editSimpananModal">Edit</button>
                                    <button class="btn btn-danger btn-sm hapus-btn" data-id="<?php echo $row['id_simpanan']; ?>">Hapus</button>
                                    <!-- Tombol Penarikan -->
                                    <button class="btn btn-success btn-sm tarik-btn" data-id="<?php echo $row['id_simpanan']; ?>" data-toggle="modal" data-target="#tarikSimpananModal">Tarik</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data simpanan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Simpanan -->
<div class="modal fade" id="tambahSimpananModal" tabindex="-1" aria-labelledby="tambahSimpananModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="proses_simpanan.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahSimpananModalLabel">Tambah Simpanan</h5>
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
                        <label>Jenis Simpanan</label>
                        <select class="form-control" name="jenis_simpanan" id="jenis_simpanan" required>
                            <option value="Bina Anggaran">Bina Anggaran</option>
                            <option value="Taruta">Taruta</option>
                            <option value="Depokita">Depokita</option>
                            <option value="Intan">Intan</option>
                        </select>
                        <!-- Tempat untuk menampilkan deskripsi jenis simpanan dengan gaya -->
                        <small id="jenisSimpananInfo" class="jenis-simpanan-info mt-2"></small>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Simpanan</label>
                        <input type="number" class="form-control" name="jumlah_simpanan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Simpanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Simpanan -->
<div class="modal fade" id="editSimpananModal" tabindex="-1" aria-labelledby="editSimpananModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formEditSimpanan" method="POST" action="proses_simpanan.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSimpananModalLabel">Edit Simpanan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <input type="text" class="form-control" id="edit_nama" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jenis Simpanan</label>
                        <input type="text" class="form-control" id="edit_jenis_simpanan" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Simpanan</label>
                        <input type="number" class="form-control" name="edit_jumlah_simpanan" id="edit_jumlah_simpanan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="edit_simpanan" class="btn btn-primary">Update Simpanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tarik Simpanan -->
<div class="modal fade" id="tarikSimpananModal" tabindex="-1" aria-labelledby="tarikSimpananModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="proses_simpanan.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="tarikSimpananModalLabel">Penarikan Simpanan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="tarik_id" id="tarik_id">
                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <input type="text" class="form-control" id="tarik_nama" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jenis Simpanan</label>
                        <input type="text" class="form-control" id="tarik_jenis_simpanan" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Simpanan</label>
                        <input type="number" class="form-control" id="tarik_jumlah_simpanan" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Penarikan</label>
                        <input type="number" class="form-control" name="jumlah_tarik" id="jumlah_tarik" required>
                    </div>
                    <div class="form-group">
                        <label>Penalti</label>
                        <input type="number" class="form-control" name="penalti" id="penalti" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tarik_simpanan" class="btn btn-primary">Tarik Simpanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Ambil data untuk edit simpanan
        $('.edit-btn').on('click', function() {
            var id = $(this).data('id'); // Ambil ID simpanan
            $.ajax({
                url: 'ambil_simpanan.php', // Kirim permintaan ke ambil_simpanan.php
                type: 'GET',
                data: { id: id },
                success: function(response) {
                    var data = JSON.parse(response); // Parse respons dari server (data simpanan)
                    
                    // Isi input pada modal edit dengan data yang diterima
                    $('#edit_id').val(data.id_simpanan);
                    $('#edit_nama').val(data.nama); // Nama anggota
                    $('#edit_jenis_simpanan').val(data.jenis_simpanan); // Jenis simpanan
                    $('#edit_jumlah_simpanan').val(data.jumlah_simpanan); // Jumlah simpanan

                    // Tampilkan modal setelah data terisi
                    $('#editSimpananModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data: " + error);
                }
            });
        });

        // Ambil data untuk penarikan
        $('.tarik-btn').on('click', function() {
            var id = $(this).data('id');
            $.ajax({
                url: 'ambil_simpanan.php',
                type: 'GET',
                data: { id: id },
                success: function(response) {
                    var data = JSON.parse(response);
                    $('#tarik_id').val(data.id_simpanan);
                    var jenis_simpanan = data.jenis_simpanan;
                    // Jika simpanan adalah Depokita, hitung penalti
                    if (jenis_simpanan === "Depokita") {
                        $('#penalti').val((data.jumlah_simpanan * 0.5).toFixed(2)); // Penalti 50% dari jasa simpanan
                    } else {
                        $('#penalti').val(0);
                    }
                }
            });
        });

        // Hapus data
        $('.hapus-btn').on('click', function() {
            if (confirm('Yakin ingin menghapus?')) {
                var id = $(this).data('id');
                $.ajax({
                    url: 'proses_simpanan.php',
                    type: 'POST',
                    data: { hapus_id: id },
                    success: function(response) {
                        location.reload(); // Muat ulang halaman setelah sukses
                    }
                });
            }
        });

        // Fungsi pencarian tabel
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#simpananTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        var jenisSimpananSelect = document.getElementById('jenis_simpanan');
        var jenisSimpananInfo = document.getElementById('jenisSimpananInfo');

        var deskripsiSimpanan = {
            "Bina Anggaran": "Simpanan dengan jangka waktu min. 6 bulan, setoran min. Rp100,000 per bulan, jasa simpanan 2%.",
            "Taruta": "Simpanan dan penarikan sewaktu-waktu, setoran min. Rp100,000, jasa simpanan 1%.",
            "Depokita": "Simpanan dengan jangka waktu 1, 3, 6, atau 12 bulan, setoran min. Rp100,000, jasa simpanan 4%, penalti 50% jika diambil sebelum jatuh tempo.",
            "Intan": "Simpanan untuk hari tua, setoran min. Rp500,000, jasa simpanan 5.5% per tahun, bisa diambil setelah usia 55 tahun."
        };

        // Fungsi untuk mengubah deskripsi saat jenis simpanan berubah
        jenisSimpananSelect.addEventListener('change', function() {
            var selectedOption = jenisSimpananSelect.value;
            jenisSimpananInfo.textContent = deskripsiSimpanan[selectedOption] || "";
        });

        // Panggil fungsi secara otomatis saat halaman dimuat
        jenisSimpananSelect.dispatchEvent(new Event('change'));
    });

    // Ambil data untuk penarikan
    $('.tarik-btn').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'ambil_simpanan.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                var data = JSON.parse(response);

                // Isi data ke dalam modal
                $('#tarik_id').val(data.id_simpanan);
                $('#tarik_nama').val(data.nama);
                $('#tarik_jenis_simpanan').val(data.jenis_simpanan);
                $('#tarik_jumlah_simpanan').val(data.sisa_saldo);

                // Hitung penalti jika jenis simpanan adalah Depokita
                var penalti = 0;
                if (data.jenis_simpanan === "Depokita") {
                    penalti = (data.sisa_saldo * 0.5).toFixed(2);
                }
                $('#penalti').val(penalti);

                // Tampilkan modal setelah data terisi
                $('#tarikSimpananModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data: " + error);
            }
        });
    });

</script>

</body>
</html>
