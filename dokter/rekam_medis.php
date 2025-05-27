<?php
session_start();
require '../config/koneksi.php';

// Pastikan dokter sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: ../auth/login.php');
    exit;
}

$dokter_id = $_SESSION['user']['id'];

// Ambil pasien_id dari query string
$pasien_id = isset($_GET['pasien_id']) ? intval($_GET['pasien_id']) : 0;

// Ambil data pendaftaran yang valid untuk dokter dan pasien ini (status diterima)
$stmt = $conn->prepare("
    SELECT p.*, 
           u_pasien.id AS pasien_id, u_pasien.username AS nama_pasien,
           u_dokter.id AS dokter_id, u_dokter.username AS nama_dokter
    FROM pendaftaran p
    JOIN users u_pasien ON p.pasien_id = u_pasien.id AND u_pasien.role = 'pasien'
    JOIN users u_dokter ON p.dokter_id = u_dokter.id AND u_dokter.role = 'dokter'
    WHERE p.dokter_id = ? AND p.pasien_id = ? AND p.status = 'diterima'
    ORDER BY p.tanggal DESC
    LIMIT 1
");
$stmt->bind_param("ii", $dokter_id, $pasien_id);
$stmt->execute();
$pendaftaran = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pendaftaran) {
    die("Data pendaftaran tidak ditemukan atau belum disetujui.");
}

// Handle form submit untuk simpan rekam medis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosa = $_POST['diagnosa'] ?? '';
    $tindakan = $_POST['tindakan'] ?? '';
    $resep = $_POST['resep'] ?? '';

    if (!$diagnosa || !$tindakan) {
        $error = "Diagnosa dan tindakan wajib diisi.";
    } else {
      $stmt = $conn->prepare("INSERT INTO rekam_medis (pendaftaran_id, dokter_id, pasien_id, diagnosa, tindakan, resep) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiisss", $pendaftaran_id, $dokter_id, $pasien_id, $diagnosa, $tindakan, $resep);

        $stmt->execute();
        $stmt->close();

        header("Location: konsultasi.php?message=rekam_medis_berhasil");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Isi Rekam Medis</title>
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
                    <h1 class="text-3xl font-bold mb-2">Isi Rekam Medis Pasien</h1>
                    <p class="text-blue-100">Catat hasil pemeriksaan pasien</p>
                </div>
                <div class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center bg-white/20 backdrop-blur-sm">
                    <i class="fas fa-file-medical text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <?php if (!empty($error)): ?>
        <div class="glass-effect p-4 rounded-xl mb-6 border-l-4 border-red-500">
            <div class="flex items-center text-red-700">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Patient Info Card -->
        <div class="glass-effect rounded-xl p-8 mb-6 max-w-3xl">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800 text-lg"><?= htmlspecialchars($pendaftaran['nama_pasien']) ?></h3>
                    <p class="text-gray-600 text-sm">Pasien</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Konsultasi</p>
                        <p class="font-medium text-gray-800"><?= htmlspecialchars($pendaftaran['tanggal']) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <i class="fas fa-comment-medical text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Keluhan</p>
                        <p class="font-medium text-gray-800"><?= htmlspecialchars($pendaftaran['keluhan']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Rekam Medis -->
        <div class="glass-effect rounded-xl p-8 shadow-lg max-w-3xl">
            <form method="POST" class="space-y-6">
                <div class="space-y-6">
                    <div class="relative">
                        <label for="diagnosa" class="block text-sm font-medium text-gray-700 mb-1">Diagnosa *</label>
                        <div class="relative">
                            <i class="fas fa-stethoscope absolute left-3 top-3 text-gray-400"></i>
                            <textarea id="diagnosa" name="diagnosa" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-none"
                                rows="4" placeholder="Masukkan hasil diagnosa"><?= $_POST['diagnosa'] ?? '' ?></textarea>
                        </div>
                    </div>

                    <div class="relative">
                        <label for="tindakan" class="block text-sm font-medium text-gray-700 mb-1">Tindakan *</label>
                        <div class="relative">
                            <i class="fas fa-hand-holding-medical absolute left-3 top-3 text-gray-400"></i>
                            <textarea id="tindakan" name="tindakan" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-none"
                                rows="4" placeholder="Masukkan tindakan yang dilakukan"><?= $_POST['tindakan'] ?? '' ?></textarea>
                        </div>
                    </div>

                    <div class="relative">
                        <label for="resep" class="block text-sm font-medium text-gray-700 mb-1">Resep (Opsional)</label>
                        <div class="relative">
                            <i class="fas fa-prescription absolute left-3 top-3 text-gray-400"></i>
                            <textarea id="resep" name="resep"
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-none"
                                rows="3" placeholder="Masukkan resep obat jika ada"><?= $_POST['resep'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                        class="gradient-card px-6 py-2.5 rounded-lg text-white font-semibold hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan Rekam Medis
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>