<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

$dokter_id = $_SESSION['user']['id'];

// Ambil jadwal dokter sesuai dokter yang login
$queryJadwal = "SELECT hari, jam_mulai, jam_selesai FROM jadwal_dokter WHERE dokter_id = ? ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam_mulai";
$stmtJadwal = $conn->prepare($queryJadwal);
$stmtJadwal->bind_param("i", $dokter_id);
$stmtJadwal->execute();
$resultJadwal = $stmtJadwal->get_result();
$jadwals = $resultJadwal->fetch_all(MYSQLI_ASSOC);
$stmtJadwal->close();

// Ambil daftar konsultasi pasien untuk dokter ini
$queryKonsul = "
    SELECT p.id, u.username AS nama_pasien, p.tanggal, p.keluhan, p.status, p.created_at
    FROM pendaftaran p
    JOIN users u ON p.pasien_id = u.id AND u.role = 'pasien'
    WHERE p.dokter_id = ?
    ORDER BY p.tanggal DESC, p.created_at DESC
";
$stmtKonsul = $conn->prepare($queryKonsul);
$stmtKonsul->bind_param("i", $dokter_id);
$stmtKonsul->execute();
$resultKonsul = $stmtKonsul->get_result();
$konsultasis = $resultKonsul->fetch_all(MYSQLI_ASSOC);
$stmtKonsul->close();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Jadwal Praktik - RomCare Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
        }
        .gradient-card {
            background: linear-gradient(135deg, #1e90ff 0%, #00c6fb 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .schedule-card {
            transition: all 0.3s ease;
        }
        .schedule-card:hover {
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
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Jadwal Praktik dan Konsultasi Saya</h1>
                <p class="text-blue-100">Kelola jadwal praktik dan konsultasi Anda di RomCare Clinic</p>
            </div>
        </div>

        <!-- Jadwal Praktik Section -->
        <div class="glass-effect rounded-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 gradient-card rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Jadwal Praktik</h2>
                        <p class="text-sm text-gray-600">Jadwal praktik Anda di klinik</p>
                    </div>
                </div>
            </div>

            <?php if (count($jadwals) === 0): ?>
            <div class="text-center py-8">
                <div class="w-16 h-16 gradient-card rounded-full mx-auto flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-times text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Jadwal</h3>
                <p class="text-gray-600">Jadwal praktik Anda belum tersedia</p>
            </div>
            <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($jadwals as $j): ?>
                <div class="glass-effect schedule-card rounded-xl p-6 border-l-4 border-blue-500">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-day text-blue-500"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 text-lg"><?= htmlspecialchars($j['hari']) ?></h3>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-blue-500"></i>
                        </div>
                        <div>
                            <p class="font-medium">
                                <?php
                                $jam_mulai = strtotime($j['jam_mulai']);
                                $jam_selesai = strtotime($j['jam_selesai']);
                                echo date('H:i', $jam_mulai) . ' - ';
                                if ($jam_selesai <= $jam_mulai) {
                                    echo date('H:i', $jam_selesai) . " (+1 hari)";
                                } else {
                                    echo date('H:i', $jam_selesai);
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Daftar Konsultasi Section -->
        <div class="glass-effect rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 gradient-card rounded-lg flex items-center justify-center">
                        <i class="fas fa-stethoscope text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Daftar Konsultasi</h2>
                        <p class="text-sm text-gray-600">Daftar konsultasi pasien Anda</p>
                    </div>
                </div>
            </div>

            <?php if (count($konsultasis) === 0): ?>
            <div class="text-center py-8">
                <div class="w-16 h-16 gradient-card rounded-full mx-auto flex items-center justify-center mb-4">
                    <i class="fas fa-user-clock text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Konsultasi</h3>
                <p class="text-gray-600">Belum ada pasien yang mendaftar konsultasi</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Nama Pasien</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Keluhan</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Didaftarkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($konsultasis as $k): ?>
                        <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($k['id']) ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-500"></i>
                                    </div>
                                    <span class="font-medium text-gray-700"><?= htmlspecialchars($k['nama_pasien']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-400 mr-2"></i>
                                    <?= htmlspecialchars($k['tanggal']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700 max-w-xs">
                                <div class="line-clamp-2"><?= htmlspecialchars($k['keluhan']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($k['status'] === 'diterima'): ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i>Diterima
                                </span>
                                <?php else: ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-history text-blue-400 mr-2"></i>
                                    <?= date('d M Y H:i', strtotime($k['created_at'])) ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>
