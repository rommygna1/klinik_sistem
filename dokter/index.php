<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

$dokter_id = $_SESSION['user']['id'];

// Ambil total konsultasi untuk dokter ini
$queryTotalKonsul = "SELECT COUNT(*) as total FROM pendaftaran WHERE dokter_id = ?";
$stmtTotalKonsul = $conn->prepare($queryTotalKonsul);
$stmtTotalKonsul->bind_param("i", $dokter_id);
$stmtTotalKonsul->execute();
$totalKonsultasi = $stmtTotalKonsul->get_result()->fetch_assoc()['total'];
$stmtTotalKonsul->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Dokter - RomCare Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
        }
        .gradient-card {
            background: linear-gradient(135deg, #1e90ff 0%, #00c6fb 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include '../components/sidebar.php'; ?>

    <main class="ml-72 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-8">Dashboard Dokter</h1>

        <!-- Total Statistics -->
        <div class="mb-8">
            <div class="gradient-card p-6 rounded-2xl shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 opacity-10">
                    <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#FFFFFF" d="M47.5,-57.5C59.2,-46.1,65.1,-29.3,65.6,-13.1C66.1,3.1,61.1,18.6,51.8,30.5C42.5,42.4,28.9,50.6,13.4,55.2C-2.1,59.8,-19.4,60.8,-33.9,54.3C-48.4,47.8,-60.1,33.9,-65.3,17.1C-70.5,0.3,-69.3,-19.4,-60.1,-33.8C-50.9,-48.2,-33.7,-57.4,-16.1,-61.4C1.5,-65.4,19.4,-64.2,35.8,-68.9C52.2,-73.6,67.1,-84.2,47.5,-57.5Z" transform="translate(100 100)" />
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="p-3 bg-white/20 rounded-lg">
                            <i class="fas fa-stethoscope text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold opacity-90">Total Konsultasi</h2>
                            <p class="text-3xl font-bold"><?= $totalKonsultasi ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="mb-8 glass-card rounded-2xl p-6 shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Statistik Konsultasi</h2>
            <div class="relative h-72">
                <canvas id="dataChart"></canvas>
            </div>
        </div>

        <!-- Quick Access Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Jadwal Saya Card -->
            <div class="relative overflow-hidden rounded-2xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-400 opacity-90"></div>
                <div class="relative p-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <i class="fas fa-calendar-check text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Jadwal Saya</h2>
                                <p class="text-white/80 text-sm mt-1">Lihat jadwal praktik Anda</p>
                            </div>
                        </div>
                        <a href="jadwal_saya.php" class="mt-4 group-hover:bg-white/30 bg-white/20 text-white w-full py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300">
                            <span>Lihat Jadwal</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Konsultasi Card -->
            <div class="relative overflow-hidden rounded-2xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-blue-400 opacity-90"></div>
                <div class="relative p-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <i class="fas fa-stethoscope text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Konsultasi</h2>
                                <p class="text-white/80 text-sm mt-1">Kelola konsultasi pasien</p>
                            </div>
                        </div>
                        <a href="konsultasi.php" class="mt-4 group-hover:bg-white/30 bg-white/20 text-white w-full py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300">
                            <span>Kelola Konsultasi</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Riwayat Rekam Medis Card -->
            <div class="relative overflow-hidden rounded-2xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-cyan-500 opacity-90"></div>
                <div class="relative p-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <i class="fas fa-history text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Riwayat</h2>
                                <p class="text-white/80 text-sm mt-1">Lihat riwayat rekam medis</p>
                            </div>
                        </div>
                        <a href="hystory_rekam_medis.php" class="mt-4 group-hover:bg-white/30 bg-white/20 text-white w-full py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300">
                            <span>Lihat Riwayat</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    const ctx = document.getElementById('dataChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(30, 144, 255, 0.5)');
    gradient.addColorStop(1, 'rgba(0, 198, 251, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label: 'Jumlah Konsultasi',
                data: [<?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>, <?= $totalKonsultasi ?>],
                borderColor: '#1e90ff',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#1e90ff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
    </script>
</body>

</html>
