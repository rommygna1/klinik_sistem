<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resepsionis') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT p.*, u1.username AS pasien, u2.username AS dokter 
                              FROM pendaftaran p 
                              JOIN users u1 ON p.pasien_id = u1.id 
                              JOIN users u2 ON p.dokter_id = u2.id 
                              WHERE p.id = '$id'");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Kartu Antrian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white flex items-center justify-center h-screen">
    <div class="w-96 border-2 border-blue-500 p-6 rounded-xl text-center">
        <h2 class="text-2xl font-bold text-blue-700 mb-4">Kartu Antrian</h2>
        <p><strong>Nama Pasien:</strong> <?= $data['pasien'] ?></p>
        <p><strong>Dokter:</strong> <?= $data['dokter'] ?></p>
        <p><strong>Tanggal:</strong> <?= $data['tanggal'] ?></p>
        <p><strong>Keluhan:</strong> <?= $data['keluhan'] ?></p>
        <p class="mt-4 font-bold text-blue-600">Nomor Antrian: #<?= $data['id'] ?></p>

        <button onclick="window.print()" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Cetak
        </button>
    </div>
</body>

</html>