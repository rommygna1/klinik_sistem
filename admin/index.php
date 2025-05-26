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
</head>

<body class="bg-white font-modify">
    <?php include '../components/sidebar.php'; ?>

    <main class="ml-64 p-6">
        <h1 class="text-3xl font-bold text-blue-500 mb-6">Dashboard Admin</h1>

        <!-- Total Pendapatan -->
        <div class="mb-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-blue-600 mb-2">Total Pendapatan (<?= $year ?>)</h2>
            <p class="text-3xl font-bold text-blue-800">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
        </div>

        <!-- Grafik Pendapatan Bulanan -->
        <div class="mb-10 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold text-blue-500 mb-4">Pendapatan Bulanan</h2>
            <div class="relative h-64">
                <canvas id="pendapatanChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Data Pasien -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-semibold text-blue-500">Data Pasien</h2>
                    <i class="fas fa-user-injured text-blue-500 text-2xl"></i>
                </div>
                <p class="text-gray-600">Lihat dan kelola informasi pasien.</p>
                <a href="crud.php" class="text-blue-600 hover:underline mt-2 inline-block">Kelola</a>
            </div>

            <!-- Jadwal Dokter -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-semibold text-blue-500">Jadwal Dokter</h2>
                    <i class="fas fa-calendar-check text-blue-500 text-2xl"></i>
                </div>
                <p class="text-gray-600">Atur dan pantau jadwal dokter.</p>
                <a href="jadwal_dokter.php" class="text-blue-600 hover:underline mt-2 inline-block">Kelola</a>
            </div>

            <!-- Pendaftaran -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-semibold text-blue-500">Pendaftaran</h2>
                    <i class="fas fa-notes-medical text-blue-500 text-2xl"></i>
                </div>
                <p class="text-gray-600">Lihat data pendaftaran pasien.</p>
                <a href="pendaftaran_admin.php" class="text-blue-600 hover:underline mt-2 inline-block">Kelola</a>
            </div>

            <!-- Konsultasi -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-semibold text-blue-500">Konsultasi</h2>
                    <i class="fas fa-stethoscope text-blue-500 text-2xl"></i>
                </div>
                <p class="text-gray-600">Pantau sesi konsultasi.</p>
                <a href="konsultasi.php" class="text-blue-600 hover:underline mt-2 inline-block">Kelola</a>
            </div>

            <!-- Rekam Medis -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-semibold text-blue-500">Rekam Medis</h2>
                    <i class="fas fa-file-medical text-blue-500 text-2xl"></i>
                </div>
                <p class="text-gray-600">Lihat riwayat rekam medis pasien.</p>
                <a href="rekam_medis.php" class="text-blue-600 hover:underline mt-2 inline-block">Kelola</a>
            </div>

            <!-- Pembayaran -->
            <div class="bg-white p-4 rounded-lg shadow hover:shadow-md">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-xl font-semibold text-blue-500">Pembayaran</h2>
                    <i class="fas fa-credit-card text-blue-500 text-2xl"></i>
                </div>
                <p class="text-gray-600">Kelola transaksi pembayaran.</p>
                <a href="tagihan_kelola.php" class="text-blue-600 hover:underline mt-2 inline-block">Kelola</a>
            </div>
        </div>
    </main>

    <script>
    const ctx = document.getElementById('pendapatanChart').getContext('2d');
    const pendapatanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($namaBulan) ?>,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= json_encode(array_values($pendapatanBulan)) ?>,
                backgroundColor: 'rgba(59, 130, 246, 0.7)',  // blue-500 dengan opacity 0.7
                borderColor: 'rgba(59, 130, 246, 1)',       // blue-500 full opacity

                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
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
        }
    });
    </script>
</body>

</html>