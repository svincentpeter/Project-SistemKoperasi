<?php
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM anggota WHERE id_anggota = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($member);
}
?>
