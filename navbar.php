<?php
$current_page = basename($_SERVER['PHP_SELF']); // Ambil nama file yang sedang diakses
?>

<!-- Tambahkan Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<div class="d-flex flex-column flex-shrink-0 p-3 bg-light-custom" style="width: 280px; height: 100vh;">
    <a href="dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        <div class="sidebar-brand-icon" style="display: flex; align-items: center;">
            <img src="Logo.png" alt="Logo Yayasan" style="height: 60px; margin-right: 15px;">
            <span class="fs-4" style="font-family: 'Poppins', sans-serif; font-size: 1.7rem; font-weight: bold; line-height: 1.2;">
                <span style="color: #ff4081;">KSP</span> <span style="color: #0d47a1;">MULIA PRASAMA DANARTA</span>
            </span>
        </div>
    </a>
    <hr>

    <ul class="nav nav-pills flex-column mb-auto" id="sidebarMenu">
        <li class="nav-item mb-3"> <!-- Menambahkan margin bawah -->
            <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : 'link-dark'; ?>" aria-current="page" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                <i class="fas fa-fw fa-tachometer-alt me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item mb-3"> <!-- Menambahkan margin bawah -->
            <a href="data_anggota.php" class="nav-link <?php echo ($current_page == 'data_anggota.php') ? 'active' : 'link-dark'; ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Anggota">
                <i class="fas fa-fw fa-users me-2"></i>Anggota
            </a>
        </li>
        <li class="nav-item mb-3"> <!-- Menambahkan margin bawah -->
            <a href="data_pinjaman.php" class="nav-link <?php echo ($current_page == 'data_pinjaman.php') ? 'active' : 'link-dark'; ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Pinjaman">
                <i class="fas fa-fw fa-dollar-sign me-2"></i>Pinjaman
            </a>
        </li>
        <li class="nav-item mb-3"> <!-- Menambahkan margin bawah -->
            <a href="data_angsuran.php" class="nav-link <?php echo ($current_page == 'data_angsuran.php') ? 'active' : 'link-dark'; ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Angsuran">
                <i class="fas fa-fw fa-file-invoice-dollar me-2"></i>Angsuran
            </a>
        </li>
        <li class="nav-item mb-3"> <!-- Menambahkan margin bawah -->
            <a href="data_simpanan.php" class="nav-link <?php echo ($current_page == 'data_simpanan.php') ? 'active' : 'link-dark'; ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Simpanan">
                <i class="fas fa-fw fa-file-invoice-dollar me-2"></i>Simpanan
            </a>
        </li>   

        <li class="nav-item mb-3">
            <a href="laporan_keseluruhan.php" class="nav-link <?php echo ($current_page == 'laporan_keseluruhan.php') ? 'active' : 'link-dark'; ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Keseluruhan">
                <i class="fas fa-fw fa-clipboard me-2"></i>Laporan Keseluruhan
            </a>
        </li>
        <li class="nav-item mb-3"> <!-- Menambahkan margin bawah -->
            <a href="logout.php" class="nav-link link-dark" data-bs-toggle="tooltip" data-bs-placement="right" title="Logout">
                <i class="fas fa-fw fa-sign-out-alt me-2"></i>Logout
            </a>
        </li>
    </ul>
    <hr>
    
    <!-- Loading spinner -->
    <div id="loading-spinner" style="display:none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
// Tooltip initialization for small screens
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Show loading spinner on link click
document.querySelectorAll('.nav-link').forEach(item => {
    item.addEventListener('click', function () {
        document.getElementById('loading-spinner').style.display = 'block';
    });
});
</script>

<style>
    .bg-light-custom {
        background: white; /* Latar belakang gradien */
    }

    .nav-link {
        border-radius: 8px;
        transition: all 0.3s ease;
        padding: 12px 20px; /* Spacing yang lebih nyaman */
    }

    .nav-link:hover {
        background-color: #0064ff;
        color: #ffffff;
        transform: scale(1.05); /* Animasi memperbesar sedikit saat hover */
    }

    .nav-link.active {
        background: linear-gradient(45deg, #0d47a1, #42a5f5); /* Warna aktif gradien */
        color: #ffffff;
        font-weight: bold;
    }

    /* Responsive sidebar for smaller screens */
    @media (max-width: 768px) {
        .d-flex.flex-column {
            width: 80px;
            overflow: hidden;
        }
        .sidebar-brand-icon img {
            height: 40px;
        }
        .fs-4 {
            display: none;
        }
        .nav-link {
            padding: 10px;
            text-align: center;
        }
        .nav-link span {
            display: none;
        }
        .nav-link i {
            font-size: 1.5rem;
        }
    }
</style>
