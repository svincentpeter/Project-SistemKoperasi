<?php
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT s.*, a.nama FROM simpanan s JOIN anggota a ON s.id_anggota = a.id_anggota WHERE id_simpanan = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $simpanan = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($simpanan); // Respons JSON yang dikirim ke AJAX
}
?>
