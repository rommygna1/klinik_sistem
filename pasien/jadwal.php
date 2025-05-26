<?php
session_start();
include_once '../config/koneksi.php';

// Cek login dan role pasien
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pasien') {
    header("Location: /auth/login.php");
    exit;
}

// Get selected day from URL parameter, default to 'all'
$selectedDay = isset($_GET['hari']) ? $_GET['hari'] : 'all';

// Modify query based on selected day
$query = "SELECT jd.*, u.username AS nama_dokter FROM jadwal_dokter jd 
          JOIN users u ON jd.dokter_id = u.id";
if ($selectedDay !== 'all') {
    $query .= " WHERE jd.hari = '" . $conn->real_escape_string($selectedDay) . "'";
}
$query .= " ORDER BY FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), jam_mulai";

$result = $conn->query($query);

// Get count of schedules per day for the badge
$countQuery = "SELECT hari, COUNT(*) as count FROM jadwal_dokter GROUP BY hari";
$countResult = $conn->query($countQuery);
$dayCounts = [];
while ($row = $countResult->fetch_assoc()) {
    $dayCounts[$row['hari']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Jadwal Dokter - RomCare Clinic</title>
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
        .schedule-card {
            transition: all 0.3s ease;
        }
        .schedule-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include_once '../components/sidebar.php'; ?>

    <main class="ml-72 p-8">
        <!-- Header Section -->
        <div class="gradient-card rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#FFFFFF" d="M47.5,-57.5C59.2,-46.1,65.1,-29.3,65.6,-13.1C66.1,3.1,61.1,18.6,51.8,30.5C42.5,42.4,28.9,50.6,13.4,55.2C-2.1,59.8,-19.4,60.8,-33.9,54.3C-48.4,47.8,-60.1,33.9,-65.3,17.1C-70.5,0.3,-69.3,-19.4,-60.1,-33.8C-50.9,-48.2,-33.7,-57.4,-16.1,-61.4C1.5,-65.4,19.4,-64.2,35.8,-68.9C52.2,-73.6,67.1,-84.2,47.5,-57.5Z" transform="translate(100 100)" />
                </svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Jadwal Praktik Dokter</h1>
                <p class="text-blue-100">Lihat jadwal praktik dokter di RomCare Clinic</p>
            </div>
        </div>

        <!-- Filter Days Section -->
        <div class="glass-effect rounded-xl p-6 mb-8 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 gradient-card rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Jadwal Mingguan</h3>
                    <p class="text-sm text-gray-600">Pilih hari untuk melihat jadwal</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="?hari=all" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= $selectedDay === 'all' ? 'gradient-card text-white' : 'hover:bg-blue-50 text-blue-600' ?>">
                    Semua
                </a>
                <?php
                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                foreach ($days as $day):
                    $count = isset($dayCounts[$day]) ? $dayCounts[$day] : 0;
                ?>
                    <a href="?hari=<?= $day ?>" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all relative 
                              <?= $selectedDay === $day ? 'gradient-card text-white' : 
                                 ($day == 'Minggu' ? 'bg-gray-200 text-gray-500' : 'hover:bg-blue-50 text-blue-600') ?>">
                        <?= $day ?>
                        <?php if ($count > 0): ?>
                            <span class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-xs flex items-center justify-center">
                                <?= $count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Jadwal Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($result && $result->num_rows > 0): 
                while ($row = $result->fetch_assoc()): ?>
                <div class="glass-effect schedule-card rounded-xl p-6 border-l-4 border-blue-500">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-md text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-lg"><?= htmlspecialchars($row['nama_dokter']) ?></h3>
                            <span class="text-blue-500 text-sm">Dokter Spesialis</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-gray-600">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-day text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Hari Praktik</p>
                                <p class="font-medium"><?= $row['hari'] ?></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-gray-600">
                            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jam Praktik</p>
                                <p class="font-medium">
                                    <?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; 
            else: ?>
                <div class="col-span-full glass-effect rounded-xl p-8 text-center">
                    <div class="w-16 h-16 gradient-card rounded-full mx-auto flex items-center justify-center mb-4">
                        <i class="fas fa-calendar-times text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                        <?= $selectedDay === 'all' ? 'Tidak Ada Jadwal' : "Tidak Ada Jadwal untuk Hari " . $selectedDay ?>
                    </h3>
                    <p class="text-gray-600">
                        <?= $selectedDay === 'all' ? 
                            'Saat ini tidak ada jadwal dokter yang tersedia.' : 
                            'Tidak ada jadwal dokter yang tersedia untuk hari ini.' ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>