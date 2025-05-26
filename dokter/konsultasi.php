<?php
session_start();
require '../config/koneksi.php';

// Pastikan dokter sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: ../auth/login.php');
    exit;
}

$dokter_id = $_SESSION['user']['id'];

// Proses jika dokter menyetujui pendaftaran pasien
if (isset($_GET['approve'])) {
    $approve_id = intval($_GET['approve']);

    // Update status menjadi 'diterima'
    $stmt = $conn->prepare("UPDATE pendaftaran SET status = 'diterima' WHERE id = ? AND dokter_id = ?");
    $stmt->bind_param("ii", $approve_id, $dokter_id);
    $stmt->execute();
    $stmt->close();

    // Redirect kembali ke halaman untuk hindari resubmission
    header("Location: konsultasi.php");
    exit;
}

// Ambil semua data pendaftaran pasien untuk dokter ini
$query = "
    SELECT 
        p.id AS pendaftaran_id,
        p.status,
        p.tanggal,
        p.keluhan,
        u.id AS pasien_id,
        u.username AS nama_pasien
    FROM pendaftaran p
    JOIN users u ON p.pasien_id = u.id
    WHERE p.dokter_id = $dokter_id
    ORDER BY p.created_at DESC
";

$result = $conn->query($query);

// Jika query gagal, tampilkan error untuk debugging
if (!$result) {
    die("Query error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Konsultasi Pasien - RomCare Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
                    <path fill="#FFFFFF"
                        d="M47.5,-57.5C59.2,-46.1,65.1,-29.3,65.6,-13.1C66.1,3.1,61.1,18.6,51.8,30.5C42.5,42.4,28.9,50.6,13.4,55.2C-2.1,59.8,-19.4,60.8,-33.9,54.3C-48.4,47.8,-60.1,33.9,-65.3,17.1C-70.5,0.3,-69.3,-19.4,-60.1,-33.8C-50.9,-48.2,-33.7,-57.4,-16.1,-61.4C1.5,-65.4,19.4,-64.2,35.8,-68.9C52.2,-73.6,67.1,-84.2,47.5,-57.5Z"
                        transform="translate(100 100)" />
                </svg>
            </div>
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Daftar Pasien Konsultasi</h1>
                    <p class="text-blue-100">Kelola konsultasi pasien di RomCare Clinic</p>
                </div>
                <div
                    class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center bg-white/20 backdrop-blur-sm">
                    <i class="fas fa-comment-medical text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <?php if ($result->num_rows === 0): ?>
        <div class="glass-effect rounded-xl p-8 text-center">
            <div class="w-16 h-16 gradient-card rounded-full mx-auto flex items-center justify-center mb-4">
                <i class="fas fa-user-clock text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Pasien</h3>
            <p class="text-gray-600">Belum ada pasien yang mendaftar konsultasi.</p>
        </div>
        <?php else: ?>
        <div class="table-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Pasien</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Keluhan</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 text-gray-700">
                                #<?= htmlspecialchars($row['pendaftaran_id']) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-500"></i>
                                    </div>
                                    <span class="font-medium text-gray-700"><?= htmlspecialchars($row['nama_pasien']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-400 mr-2"></i>
                                    <?= htmlspecialchars($row['tanggal']) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3 max-w-xs">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-comment-medical text-blue-500"></i>
                                    </div>
                                    <span class="text-gray-600 line-clamp-2"><?= htmlspecialchars($row['keluhan']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($row['status'] === 'diterima'): ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i>Diterima
                                </span>
                                <?php else: ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($row['status'] === 'diterima'): ?>
                                <a href="rekam_medis.php?pasien_id=<?= $row['pasien_id'] ?>"
                                    class="gradient-card px-4 py-2 rounded-lg text-white text-sm font-medium hover:shadow-lg transition-all duration-300 inline-flex items-center gap-2">
                                    <i class="fas fa-notes-medical"></i>
                                    Isi Rekam Medis
                                </a>
                                <?php else: ?>
                                <a href="konsultasi.php?approve=<?= $row['pendaftaran_id'] ?>"
                                    class="gradient-card px-4 py-2 rounded-lg text-white text-sm font-medium hover:shadow-lg transition-all duration-300 inline-flex items-center gap-2">
                                    <i class="fas fa-check"></i>
                                    Setujui
                                </a>
                                <?php endif; ?>
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