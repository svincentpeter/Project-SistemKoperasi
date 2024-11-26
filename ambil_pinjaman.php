<?php
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM pinjaman WHERE id_pinjaman = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $pinjaman = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($pinjaman);
}
?>
