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


<style>    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
    .sidebar-gradient {
        background: linear-gradient(135deg, #1e90ff 0%, #00c6fb 100%);
    }
    .sidebar-link {
        transition: all 0.3s ease;
        color: rgba(255, 255, 255, 0.85);
        border-radius: 9999px;
        padding: 0.75rem 1.25rem;
    }
    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        backdrop-filter: blur(10px);
    }
    .sidebar-link.active {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff !important;
    }
    .sidebar-link i {
        width: 20px;
        text-align: center;
    }
    .sidebar-logout {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        transition: all 0.3s ease;
        border-radius: 9999px;
        backdrop-filter: blur(10px);
    }
    .sidebar-logout:hover {
        background: rgba(255, 255, 255, 0.25);
    }
    .sidebar-title {
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
</style>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<aside class="sidebar-gradient min-h-screen flex flex-col justify-between py-8 px-6 fixed left-0 top-0 z-30"    style="font-family: 'Poppins', sans-serif; width: 280px;">
    <div class="flex flex-col h-full">
        <div class="flex items-center space-x-2 mb-6">
            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
            </div>
            <h2 class="sidebar-title text-[23px] font-bold tracking-wide">RomCare Clinic</h2>

        </div>
        
        <nav class="flex-1 flex flex-col gap-2 text-base">
            <?php if ($role === 'admin'): ?>
            <a href="../admin/index.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-chart-line"></i> Dashboard
            </a>
            <a href="../admin/crud.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-users-cog"></i> Kelola Pengguna
            </a>
            <a href="../admin/jadwal_dokter.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-calendar-check"></i> Jadwal Dokter
            </a>
            <a href="../admin/pendaftaran_admin.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-user-plus"></i> Pendaftaran
            </a>
            <a href="../admin/konsultasi.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-comments"></i> Konsultasi
            </a>
            <a href="../admin/tagihan_input.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-money-bill-wave"></i> Pembayaran
            </a>
            <a href="../admin/tagihan_kelola.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-receipt"></i> Show Tagihan
            </a>

            <?php elseif ($role === 'dokter'): ?>
            <a href="../dokter/index.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="../dokter/jadwal_saya.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-calendar-alt"></i> Jadwal Saya
            </a>
            <a href="../dokter/tambah_dokter.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-user-md"></i> Dokter
            </a>            <a href="../dokter/konsultasi.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-comment-medical"></i> Konsultasi
            </a>
            <a href="../dokter/hystory_rekam_medis.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-history"></i> Daftar Rekam Medis
            </a>

            <?php elseif ($role === 'resepsionis'): ?>
            <a href="../resepsionis/index.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="../resepsionis/pendaftaran_offline.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-user-plus"></i> Pendaftaran Pasien
            </a>
            <a href="../resepsionis/jadwal_dokter.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-calendar-check"></i> Jadwal Dokter
            </a>
            <a href="../resepsionis/kelola_pembayaran.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-money-check-alt"></i> Pembayaran
            </a>

            <?php elseif ($role === 'pasien'): ?>
            <a href="../pasien/index.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="../pasien/jadwal.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-calendar-alt"></i> Lihat Jadwal
            </a>
            <a href="../pasien/konsultasi.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-headset"></i> Konsultasi
            </a>
            <a href="../pasien/rekam_medis.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-notes-medical"></i> Rekam Medis Saya
            </a>
            <a href="../pasien/lihat_tagihan.php" class="sidebar-link flex items-center gap-3">
                <i class="fas fa-file-invoice-dollar"></i> Pembayaran
            </a>
            <?php endif; ?>
        </nav>
        
        <a href="../auth/login.php"
            class="sidebar-logout flex items-center justify-center gap-2 px-4 py-3 mt-8 font-semibold">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>