<?php
session_start();
require '../config/koneksi.php';

// Pastikan pasien sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$pasien_id = $_SESSION['user']['id'];

// Ambil rekam medis pasien dengan nama dokter dari tabel users
$query = "
    SELECT 
        rm.id,
        rm.created_at,
        rm.diagnosa,
        rm.tindakan,
        rm.resep,
        u.username AS nama_dokter
    FROM rekam_medis rm
    JOIN users u ON rm.dokter_id = u.id
    WHERE rm.pasien_id = ?
    ORDER BY rm.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pasien_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Rekam Medis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
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
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
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
                    <path fill="#FFFFFF" d="M47.5,-57.5C59.2,-46.1,65.1,-29.3,65.6,-13.1C66.1,3.1,61.1,18.6,51.8,30.5C42.5,42.4,28.9,50.6,13.4,55.2C-2.1,59.8,-19.4,60.8,-33.9,54.3C-48.4,47.8,-60.1,33.9,-65.3,17.1C-70.5,0.3,-69.3,-19.4,-60.1,-33.8C-50.9,-48.2,-33.7,-57.4,-16.1,-61.4C1.5,-65.4,19.4,-64.2,35.8,-68.9C52.2,-73.6,67..." />
                </svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Rekam Medis</h1>
                <p class="text-blue-100">Riwayat rekam medis pasien</p>
            </div>
        </div>

        <!-- Main Content -->
        <?php if ($result->num_rows === 0): ?>
        <div class="text-center py-12 glass-effect rounded-xl">
            <div class="w-16 h-16 gradient-card rounded-full mx-auto flex items-center justify-center mb-4">
                <i class="fas fa-file-medical text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Rekam Medis</h3>
            <p class="text-gray-600">Belum ada rekam medis yang tersedia.</p>
        </div>
        <?php else: ?>
        <div class="table-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Dokter</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Diagnosa</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Tindakan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Resep</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars(date('d M Y', strtotime($row['created_at']))) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <?= htmlspecialchars($row['nama_dokter']) ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate"><?= nl2br(htmlspecialchars($row['diagnosa'])) ?></td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate"><?= nl2br(htmlspecialchars($row['tindakan'])) ?></td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate">
                                <?= $row['resep'] ? nl2br(htmlspecialchars($row['resep'])) : '-' ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </main>
</body>

</html>