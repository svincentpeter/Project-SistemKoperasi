<?php
session_start();
include('db.php'); // Include koneksi database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // SQL untuk memeriksa kredensial pengguna
    $query = "SELECT * FROM pengguna WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$username, $password]);

    $result = $stmt->fetch(); // Mengambil hasil

    if ($result) {
        $_SESSION['user'] = $username;
        header('Location: dashboard.php');
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Koperasi</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome untuk icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(45deg, #4e73df, #1cc88a); /* Gradient background */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Shadow lebih tebal */
            overflow: hidden;
        }

        .card-header {
            background-color: white; /* Warna background untuk logo */
            text-align: center;
            padding: 20px;
            border-bottom: none;
        }

        .card-header img {
            width: 80px; /* Ukuran logo */
            height: auto;
            margin-bottom: 10px;
        }

        .card-header h2 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #4e73df; /* Warna teks header */
        }

        .card-body {
            padding: 30px;
        }

        .form-group label {
            font-weight: bold;
            color: #4e73df;
        }

        .form-control {
            border-radius: 10px;
            box-shadow: none;
            border: 2px solid #d1d3e2;
            transition: border-color 0.3s ease-in-out;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 10px rgba(78, 115, 223, 0.2); /* Glow effect saat fokus */
        }

        .btn-primary {
            background-color: #4e73df;
            border-radius: 20px;
            font-size: 1.2rem;
            padding: 10px 20px;
            transition: background-color 0.3s ease-in-out;
            border: none;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
        }

        .btn i {
            margin-right: 8px;
        }

        .form-icon {
            font-size: 1.5rem;
            color: #4e73df;
            margin-right: 10px;
        }

        .alert {
            border-radius: 10px;
            margin-top: 10px;
        }

        /* Animasi ringan */
        .login-container {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="card">
        <div class="card-header">
    <!-- Logo di Header -->
    <img src="Logo.png" alt="Logo Koperasi">
    <!-- Teks di bawah logo -->
    <div style="text-align: center; margin-top: 10px;">
        <span class="fs-4" style="font-family: 'Poppins', sans-serif; font-size: 1.7rem; font-weight: bold; line-height: 1.2;">
            <span style="color: #ff4081;">KSP</span> 
            <span style="color: #0d47a1;">MULIA PRASAMA DANARTA</span>
        </span>
    </div>
</div>

            <div class="card-body">
                <!-- Pesan error jika login gagal -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger text-center">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user form-icon"></i> Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock form-icon"></i> Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS dan Font Awesome JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
