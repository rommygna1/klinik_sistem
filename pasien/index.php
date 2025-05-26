<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pasien') {
    header('Location: /auth/login.php');
    exit;
}
$username = $_SESSION['user']['username'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Pasien - RomCare Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
        .gradient-card {
            background: linear-gradient(135deg, #1e90ff 0%, #00c6fb 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .info-card {
            transition: transform 0.3s ease;
        }
        .info-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include '../components/sidebar.php'; ?>

    <main class="ml-72 p-8">
        <!-- Header Section -->
        <div class="gradient-card rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#FFFFFF" d="M47.5,-57.5C59.2,-46.1,65.1,-29.3,65.6,-13.1C66.1,3.1,61.1,18.6,51.8,30.5C42.5,42.4,28.9,50.6,13.4,55.2C-2.1,59.8,-19.4,60.8,-33.9,54.3C-48.4,47.8,-60.1,33.9,-65.3,17.1C-70.5,0.3,-69.3,-19.4,-60.1,-33.8C-50.9,-48.2,-33.7,-57.4,-16.1,-61.4C1.5,-65.4,19.4,-64.2,35.8,-68.9C52.2,-73.6,67.1,-84.2,47.5,-57.5Z" transform="translate(100 100)" />
                </svg>
            </div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Selamat Datang, <?= htmlspecialchars($username) ?>!</h1>
                    <p class="text-blue-100">Selamat datang di Dashboard RomCare Clinic</p>
                </div>
                <div class="w-32 h-32 bg-white/20 rounded-2xl p-4 backdrop-blur-sm hidden md:block">
                    <img src="https://cdn.pixabay.com/photo/2017/09/30/18/53/prognosis-icon-2803190_1280.png"
                        alt="Patient Illustration" class="w-full h-full object-contain" />
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass-effect info-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <span class="text-blue-500 text-sm font-medium">Jadwal</span>
                </div>
                <h3 class="text-gray-800 font-semibold mb-1">Jadwal Dokter</h3>
                <p class="text-gray-600 text-sm">Cek jadwal praktik dokter</p>
            </div>

            <div class="glass-effect info-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center">
                        <i class="fas fa-comments text-white text-xl"></i>
                    </div>
                    <span class="text-blue-500 text-sm font-medium">Konsultasi</span>
                </div>
                <h3 class="text-gray-800 font-semibold mb-1">Chat Dokter</h3>
                <p class="text-gray-600 text-sm">Konsultasi dengan dokter</p>
            </div>

            <div class="glass-effect info-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center">
                        <i class="fas fa-notes-medical text-white text-xl"></i>
                    </div>
                    <span class="text-blue-500 text-sm font-medium">Rekam Medis</span>
                </div>
                <h3 class="text-gray-800 font-semibold mb-1">Riwayat Medis</h3>
                <p class="text-gray-600 text-sm">Lihat rekam medis Anda</p>
            </div>

            <div class="glass-effect info-card rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                    </div>
                    <span class="text-blue-500 text-sm font-medium">Pembayaran</span>
                </div>
                <h3 class="text-gray-800 font-semibold mb-1">Tagihan</h3>
                <p class="text-gray-600 text-sm">Cek & bayar tagihan</p>
            </div>
        </div>

        <!-- Informasi Section -->
        <div class="glass-effect rounded-xl p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 gradient-card rounded-lg flex items-center justify-center">
                    <i class="fas fa-info-circle text-white text-xl"></i>
                </div>
                <h2 class="text-2xl font-semibold text-gray-800">Informasi Terbaru</h2>
            </div>
            
            <div class="grid gap-4">
                <div class="flex items-start gap-4 p-4 rounded-lg hover:bg-blue-50/50 transition-colors">
                    <div class="w-8 h-8 gradient-card rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Jadwal Praktik</h4>
                        <p class="text-gray-600">Dokter tersedia setiap Senin - Jumat pukul 08.00 - 16.00</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 rounded-lg hover:bg-blue-50/50 transition-colors">
                    <div class="w-8 h-8 gradient-card rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-comment-medical text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Konsultasi Online</h4>
                        <p class="text-gray-600">Lakukan konsultasi jika mengalami keluhan kesehatan</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 rounded-lg hover:bg-blue-50/50 transition-colors">
                    <div class="w-8 h-8 gradient-card rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-medical text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Rekam Medis Digital</h4>
                        <p class="text-gray-600">Rekam medis dapat diakses & dicetak melalui menu Rekam Medis Saya</p>
                    </div>
                </div>

                <div class="flex items-start gap-4 p-4 rounded-lg hover:bg-blue-50/50 transition-colors">
                    <div class="w-8 h-8 gradient-card rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-credit-card text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-1">Pembayaran</h4>
                        <p class="text-gray-600">Pastikan pembayaran pendaftaran dilakukan sebelum konsultasi</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>