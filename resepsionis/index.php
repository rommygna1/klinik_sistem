<?php 
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resepsionis') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

// Ambil jumlah data dari tabel
$queryPendaftaran = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran");
$totalPendaftaran = mysqli_fetch_assoc($queryPendaftaran)['total'];

$queryKonsultasi = mysqli_query($conn, "SELECT COUNT(*) as total FROM rekam_medis");
$totalKonsultasi = mysqli_fetch_assoc($queryKonsultasi)['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Resepsionis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-blue-50 min-h-screen font-modify">
    <?php include '../components/sidebar.php'; ?>

    <div class="p-6 ml-64">
        <h1 class="text-3xl font-bold text-blue-700 mb-6">Dashboard Resepsionis</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kartu Pendaftaran -->
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-500">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Total Pendaftaran</h2>
                <p class="text-4xl font-bold text-blue-600"><?= $totalPendaftaran ?></p>
            </div>

            <!-- Kartu Konsultasi -->
            <div class="bg-white p-6 rounded-xl shadow border-l-4 border-blue-500">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Total Konsultasi</h2>
                <p class="text-4xl font-bold text-blue-600"><?= $totalKonsultasi ?></p>
            </div>
        </div>

        <!-- Chart -->
        <div class="mt-10 bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold text-blue-700 mb-4">Statistik Pendaftaran & Konsultasi</h2>
            <canvas id="dataChart" height="100"></canvas>
        </div>
    </div>

    <script>
    const ctx = document.getElementById('dataChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pendaftaran', 'Konsultasi'],
            datasets: [{
                label: 'Jumlah',
                data: [<?= $totalPendaftaran ?>, <?= $totalKonsultasi ?>],
                backgroundColor: ['#34d399', '#10b981'],
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    </script>
</body>

</html>