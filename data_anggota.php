<?php
session_start();
include('db.php');

// Proteksi halaman agar hanya bisa diakses setelah login
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Jika permintaan AJAX dari DataTables untuk server-side processing
if (isset($_POST['draw'])) {
    // Inisialisasi variabel untuk filter
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? $_POST['jenis_kelamin'] : '';
    $pekerjaan = isset($_POST['pekerjaan']) ? $_POST['pekerjaan'] : '';

    // Kolom yang tersedia di tabel
    $columns = array( 
        0 => 'id_anggota',
        1 => 'nama',
        2 => 'jenis_kelamin',
        3 => 'tempat_lahir',
        4 => 'tanggal_lahir',
        5 => 'alamat',
        6 => 'no_telp',
        7 => 'email',
        8 => 'nik',
        9 => 'pekerjaan',
        10 => 'tanggal_bergabung'
    );

    // Query dasar
    $query = "SELECT * FROM anggota WHERE 1=1";

    // Filter berdasarkan jenis kelamin
    if (!empty($jenis_kelamin)) {
        $query .= " AND jenis_kelamin = :jenis_kelamin";
    }
    // Filter berdasarkan pekerjaan
    if (!empty($pekerjaan)) {
        $query .= " AND pekerjaan = :pekerjaan";
    }

    // Hitung total data tanpa filter
    $stmt = $conn->prepare($query);
    if (!empty($jenis_kelamin)) {
        $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
    }
    if (!empty($pekerjaan)) {
        $stmt->bindParam(':pekerjaan', $pekerjaan);
    }
    $stmt->execute();
    $totalData = $stmt->rowCount();

    // Pagination, Sorting, dan Pencarian
    $limit = $_POST['length'];
    $start = $_POST['start'];
    $order = $columns[$_POST['order'][0]['column']];
    $dir = $_POST['order'][0]['dir'];

    $query .= " ORDER BY $order $dir LIMIT $start, $limit";
    $stmt = $conn->prepare($query);

    // Bind parameters untuk filter
    if (!empty($jenis_kelamin)) {
        $stmt->bindParam(':jenis_kelamin', $jenis_kelamin);
    }
    if (!empty($pekerjaan)) {
        $stmt->bindParam(':pekerjaan', $pekerjaan);
    }
    $stmt->execute();
    $anggota = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Persiapan data untuk DataTables
    $data = array();
    foreach ($anggota as $row) {
        $nestedData = array();
        $nestedData[] = $row["id_anggota"];
        $nestedData[] = $row["nama"];
        $nestedData[] = $row["jenis_kelamin"];
        $nestedData[] = $row["tempat_lahir"];
        $nestedData[] = $row["tanggal_lahir"];
        $nestedData[] = $row["alamat"];
        $nestedData[] = $row["no_telp"];
        $nestedData[] = $row["email"];
        $nestedData[] = $row["nik"];
        $nestedData[] = $row["pekerjaan"];
        $nestedData[] = $row["tanggal_bergabung"];
        $nestedData[] = '<button class="btn btn-warning btn-sm edit-btn mr-2" data-id="'.$row['id_anggota'].'" title="Edit">
                            <i class="fas fa-edit"></i> Edit
                         </button> 
                         <button class="btn btn-danger btn-sm hapus-btn" data-id="'.$row['id_anggota'].'" title="Hapus">
                            <i class="fas fa-trash"></i> Hapus
                         </button>';
        
        $data[] = $nestedData;
    }

    // Total record setelah filter
    $totalFiltered = $totalData;

    // Output untuk DataTables
    $json_data = array(
        "draw"            => intval($_POST['draw']),
        "recordsTotal"    => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"            => $data
    );

    echo json_encode($json_data);
    exit(); // Keluar dari script setelah memproses permintaan DataTables
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota</title>
    <!-- Bootstrap dan SB Admin 2 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Font Awesome untuk ikon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-size: 18px; /* Ubah ukuran font dasar */
        }

        .table th, .table td {
            font-size: 16px; /* Ukuran font untuk isi tabel */
        }

        .modal-title, .modal-body label, .modal-footer button {
            font-size: 18px; /* Ukuran font untuk modal */
        }

        h1.h3 {
            font-size: 24px; /* Ukuran font untuk judul halaman */
        }

        .btn {
            font-size: 16px; /* Ukuran font untuk tombol */
        }
    </style>
</head>
<body>
<div id="wrapper">
    <!-- Include Sidebar dari navbar.php -->
    <?php include('navbar.php'); ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="body-wrapper d-flex flex-column">
        <div id="content">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <h1 class="h3 mb-0 text-gray-800">Data Anggota</h1>
            </nav>
            <div class="container-fluid">
                <!-- Tombol Tambah Anggota -->
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#tambahAnggotaModal">Tambah Anggota</button>
                
                <!-- Filter Dropdowns -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filterJenisKelamin">Filter Jenis Kelamin:</label>
                        <select id="filterJenisKelamin" class="form-control">
                            <option value="">Semua</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filterPekerjaan">Filter Pekerjaan:</label>
                        <select id="filterPekerjaan" class="form-control">
                            <option value="">Semua</option>
                            <option value="Pegawai Negeri Sipil">Pegawai Negeri Sipil</option>
                            <option value="Wiraswasta">Wiraswasta</option>
                            <option value="Karyawan Swasta">Karyawan Swasta</option>
                        </select>
                    </div>
                </div>

                <!-- Tabel Data Anggota -->
                <table id="anggotaTable" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID Anggota</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Lahir</th>
                            <th>Tanggal Lahir</th>
                            <th>Alamat</th>
                            <th>No. Telepon</th>
                            <th>Email</th>
                            <th>NIK</th>
                            <th>Pekerjaan</th>
                            <th>Tanggal Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Anggota -->
<div class="modal fade" id="tambahAnggotaModal" tabindex="-1" aria-labelledby="tambahAnggotaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formTambahAnggota" method="POST" enctype="multipart/form-data" action="proses_anggota.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAnggotaModalLabel">Tambah Anggota</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select class="form-control" name="jenis_kelamin" id="jenis_kelamin" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" class="form-control" name="alamat" id="alamat" required>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" class="form-control" name="no_telp" id="no_telp" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" class="form-control" name="nik" id="nik" required>
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan</label>
                        <input type="text" class="form-control" name="pekerjaan" id="pekerjaan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Anggota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Anggota -->
<div class="modal fade" id="editAnggotaModal" tabindex="-1" aria-labelledby="editAnggotaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="proses_anggota.php" id="formEditAnggota">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAnggotaModalLabel">Edit Anggota</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="edit_nama" id="edit_nama" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select class="form-control" name="edit_jenis_kelamin" id="edit_jenis_kelamin" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" class="form-control" name="edit_tempat_lahir" id="edit_tempat_lahir" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" class="form-control" name="edit_tanggal_lahir" id="edit_tanggal_lahir" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" name="edit_alamat" id="edit_alamat" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" class="form-control" name="edit_no_telp" id="edit_no_telp" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="edit_email" id="edit_email" required>
                    </div>
                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" class="form-control" name="edit_nik" id="edit_nik" required>
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan</label>
                        <input type="text" class="form-control" name="edit_pekerjaan" id="edit_pekerjaan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Anggota</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- jQuery, Bootstrap JS dan DataTables -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
    // Inisialisasi DataTables dengan server-side processing
    var table = $('#anggotaTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "data_anggota.php",
            "type": "POST",
            "data": function (d) {
                d.jenis_kelamin = $('#filterJenisKelamin').val();
                d.pekerjaan = $('#filterPekerjaan').val();
            }
        },
        "scrollX": true,
    });

    // Trigger reload DataTables ketika filter berubah
    $('#filterJenisKelamin, #filterPekerjaan').on('change', function() {
        table.ajax.reload();
    });

    // Edit data anggota
    $('#anggotaTable').on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'ambil_anggota.php',
            type: 'GET',
            data: { id: id },
            success: function(response) {
                var data = JSON.parse(response);
                $('#edit_id').val(data.id_anggota);
                $('#edit_nama').val(data.nama);
                $('#edit_jenis_kelamin').val(data.jenis_kelamin);
                $('#edit_tempat_lahir').val(data.tempat_lahir);
                $('#edit_tanggal_lahir').val(data.tanggal_lahir);
                $('#edit_alamat').val(data.alamat);
                $('#edit_no_telp').val(data.no_telp);
                $('#edit_email').val(data.email);
                $('#edit_nik').val(data.nik);
                $('#edit_pekerjaan').val(data.pekerjaan);
                $('#editAnggotaModal').modal('show'); // Tampilkan modal edit
            }
        });
    });

    // Hapus data anggota
    $('#anggotaTable').on('click', '.hapus-btn', function() {
        var id = $(this).data('id');
        if (confirm('Yakin ingin menghapus anggota ini?')) {
            $.ajax({
                url: 'proses_anggota.php',
                type: 'POST',
                data: { delete_id: id },
                success: function(response) {
                    table.ajax.reload(); // Muat ulang tabel setelah hapus
                }
            });
        }
    });
});

</script>
</body>
</html>
