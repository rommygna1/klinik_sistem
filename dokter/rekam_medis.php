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
    <meta charset="UTF-8">
    <title>Dokter - Rekam Medis Pasien</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
        .form-container {
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
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-1">Rekam Medis Pasien</h1>
                <p class="text-gray-600">Isi rekam medis untuk pasien konsultasi</p>
            </div>
        </div>

        <div class="form-container p-6">
            <form method="POST" action="" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user-injured mr-2 text-blue-500"></i>Diagnosa
                            </label>
                            <textarea name="diagnosa" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                rows="4"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-pills mr-2 text-blue-500"></i>Obat yang Diberikan
                            </label>
                            <textarea name="obat" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                rows="4"></textarea>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-notes-medical mr-2 text-blue-500"></i>Catatan Tambahan
                            </label>
                            <textarea name="catatan"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                rows="4"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-money-bill-wave mr-2 text-blue-500"></i>Biaya Konsultasi (Rp)
                            </label>
                            <input type="number" name="biaya" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                placeholder="Masukkan biaya">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="konsultasi.php"
                        class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit"
                        class="gradient-card px-6 py-2 text-white rounded-lg hover:shadow-lg transition-all duration-300">
                        <i class="fas fa-save mr-2"></i>Simpan Rekam Medis
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>