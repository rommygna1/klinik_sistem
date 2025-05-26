<?php
session_start();
include_once '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: /auth/login.php");
    exit;
}

// Query total pendapatan dari pembayaran dengan status 'lunas'
$result = mysqli_query($conn, "SELECT SUM(nominal) AS total_pendapatan FROM tagihan_pembayaran WHERE status='lunas'");
$row = mysqli_fetch_assoc($result);
$totalPendapatan = $row['total_pendapatan'] ?? 0;

// Query pendapatan bulanan (tahun ini) untuk grafik
$year = date('Y');
$queryPendapatanBulan = mysqli_query($conn,
    "SELECT MONTH(created_at) AS bulan, SUM(nominal) AS total 
     FROM tagihan_pembayaran 
     WHERE status='lunas' AND YEAR(created_at) = '$year' 
     GROUP BY bulan 
     ORDER BY bulan ASC");

// Siapkan data bulan dan total untuk chart
$pendapatanBulan = array_fill(1, 12, 0); // default 0 untuk semua bulan 1-12
while ($data = mysqli_fetch_assoc($queryPendapatanBulan)) {
    $pendapatanBulan[(int)$data['bulan']] = (float)$data['total'];
}

// Buat array label bulan (nama bulan)
$namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - RomCare Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
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
        <h1 class="text-2xl font-bold text-gray-800 mb-8">Dashboard Admin</h1>

        <!-- Total Pendapatan -->
        <div class="mb-8 gradient-card p-6 rounded-2xl shadow-lg text-white">
            <h2 class="text-lg font-semibold mb-2 opacity-90">Total Pendapatan (<?= $year ?>)</h2>
            <p class="text-3xl font-bold">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
        </div>

        <!-- Grafik Pendapatan Bulanan -->
        <div class="mb-10 glass-card p-6 rounded-2xl shadow-lg">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Pendapatan Bulanan</h2>
            <div class="relative h-72">
                <canvas id="pendapatanChart"></canvas>
            </div>
        </div>        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Data Pasien -->
            <div class="relative overflow-hidden rounded-2xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-400 opacity-90"></div>
                <div class="relative p-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <i class="fas fa-user-injured text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Data Pasien</h2>
                                <p class="text-white/80 text-sm mt-1">Lihat dan kelola informasi pasien</p>
                            </div>
                        </div>
                        <a href="crud.php" class="mt-4 group-hover:bg-white/30 bg-white/20 text-white w-full py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300">
                            <span>Kelola Data</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Jadwal Dokter -->
            <div class="relative overflow-hidden rounded-2xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-blue-400 opacity-90"></div>
                <div class="relative p-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <i class="fas fa-calendar-check text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Jadwal Dokter</h2>
                                <p class="text-white/80 text-sm mt-1">Atur dan pantau jadwal dokter</p>
                            </div>
                        </div>
                        <a href="jadwal_dokter.php" class="mt-4 group-hover:bg-white/30 bg-white/20 text-white w-full py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300">
                            <span>Kelola Jadwal</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pendaftaran -->
            <div class="relative overflow-hidden rounded-2xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-400 to-cyan-500 opacity-90"></div>
                <div class="relative p-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <i class="fas fa-notes-medical text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Pendaftaran</h2>
                                <p class="text-white/80 text-sm mt-1">Lihat data pendaftaran pasien</p>
                            </div>
                        </div>
                        <a href="pendaftaran_admin.php" class="mt-4 group-hover:bg-white/30 bg-white/20 text-white w-full py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300">
                            <span>Kelola Pendaftaran</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Konsultasi -->
            <div class="relative overflow-hidden rounded-2xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-400 to-blue-500 opacity-90"></div>
                <div class="relative p-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <i class="fas fa-stethoscope text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Konsultasi</h2>
                                <p class="text-white/80 text-sm mt-1">Pantau sesi konsultasi</p>
                            </div>
                        </div>
                        <a href="konsultasi.php" class="mt-4 group-hover:bg-white/30 bg-white/20 text-white w-full py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300">
                            <span>Kelola Konsultasi</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Rekam Medis -->
            <div class="relative overflow-hidden rounded-2xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-cyan-400 opacity-90"></div>
                <div class="relative p-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <i class="fas fa-file-medical text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Rekam Medis</h2>
                                <p class="text-white/80 text-sm mt-1">Lihat riwayat rekam medis</p>
                            </div>
                        </div>
                        <a href="rekam_medis.php" class="mt-4 group-hover:bg-white/30 bg-white/20 text-white w-full py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300">
                            <span>Kelola Rekam Medis</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pembayaran -->
            <div class="relative overflow-hidden rounded-2xl group">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-500 to-blue-400 opacity-90"></div>
                <div class="relative p-6">
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white/20 rounded-lg">
                                <i class="fas fa-credit-card text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-white">Pembayaran</h2>
                                <p class="text-white/80 text-sm mt-1">Kelola transaksi pembayaran</p>
                            </div>
                        </div>
                        <a href="tagihan_kelola.php" class="mt-4 group-hover:bg-white/30 bg-white/20 text-white w-full py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300">
                            <span>Kelola Pembayaran</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    const ctx = document.getElementById('pendapatanChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(30, 144, 255, 0.5)');
    gradient.addColorStop(1, 'rgba(0, 198, 251, 0.0)');
    
    const pendapatanChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($namaBulan) ?>,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= json_encode(array_values($pendapatanBulan)) ?>,
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
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
    </script>
</body>

</html>