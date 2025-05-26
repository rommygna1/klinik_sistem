<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../config/koneksi.php';

if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit;
}

$role = $_SESSION['user']['role'];
?>


<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

.font-modify {
    font-family: 'Poppins', sans-serif;
}
</style>
<!-- Tailwind & Heroicons CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<aside class="w-64 h-screen bg-white text-blue-700 p-4 fixed shadow-md font-modify">
    <h2 class="text-2xl font-bold mb-6 text-blue-800">RomCare Clinic</h2>

    <nav class="space-y-2 text-sm">
        <?php if ($role === 'admin'): ?>
        <a href="../admin/index.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="../admin/crud.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-users-cog"></i> Kelola Pengguna
        </a>
        <a href="../admin/jadwal_dokter.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-calendar-check"></i> Jadwal Dokter
        </a>
        <a href="../admin/pendaftaran_admin.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-user-plus"></i> Pendaftaran
        </a>
        <a href="../admin/konsultasi.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-comments"></i> Konsultasi
        </a>
        <!-- <a href="../admin/rekam_medis.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-file-medical"></i> Rekam Medis
        </a> -->
        <a href="../admin/tagihan_input.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-money-bill-wave"></i> Pembayaran
        </a>
        <a href="../admin/tagihan_kelola.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-receipt"></i> Show Tagihan
        </a>

        <?php elseif ($role === 'dokter'): ?>
        <a href="../dokter/index.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="../dokter/jadwal_saya.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-calendar-alt"></i> Jadwal Saya
        </a>
        <a href="../dokter/tambah_dokter.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-user-md"></i> Dokter
        </a>
        <a href="../dokter/konsultasi.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-comments-medical"></i> Konsultasi
        </a>
        <a href="../dokter/hystory_rekam_medis.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-history"></i> Daftar Rekam Medis
        </a>

        <?php elseif ($role === 'resepsionis'): ?>
        <a href="../resepsionis/index.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="../resepsionis/pendaftaran_offline.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-user-plus"></i> Pendaftaran Pasien
        </a>
        <a href="../resepsionis/jadwal_dokter.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-calendar-check"></i> Jadwal Dokter
        </a>
        <a href="../resepsionis/kelola_pembayaran.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-money-check-alt"></i> Pembayaran
        </a>

        <?php elseif ($role === 'pasien'): ?>
        <a href="../pasien/index.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="../pasien/jadwal.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-calendar-alt"></i> Lihat Jadwal
        </a>
        <a href="../pasien/konsultasi.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-headset"></i> Konsultasi
        </a>
        <a href="../pasien/rekam_medis.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-notes-medical"></i> Rekam Medis Saya
        </a>
        <a href="../pasien/lihat_tagihan.php" class="flex items-center gap-2 hover:bg-blue-100 p-2 rounded">
            <i class="fas fa-file-invoice-dollar"></i> Pembayaran
        </a>
        <?php endif; ?>

        <a href="../auth/login.php"
            class="flex items-center justify-center hover:bg-red-600 p-2 rounded mt-4 bg-red-500 text-white">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </nav>
</aside>