<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pasien') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

// Ambil daftar dokter dari tabel users (role = dokter)
$queryDokter = "SELECT id, username FROM users WHERE role = 'dokter' ORDER BY username";
$resultDokter = mysqli_query($conn, $queryDokter);
$dokters = [];
if ($resultDokter) {
    while ($row = mysqli_fetch_assoc($resultDokter)) {
        $dokters[] = $row;
    }
}

// Proses form submit
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pasien_id = $_SESSION['user']['id'];
    $dokter_id = $_POST['dokter_id'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $keluhan = trim($_POST['keluhan'] ?? '');

    if (!$dokter_id) $errors[] = "Pilih dokter.";
    if (!$tanggal) $errors[] = "Isi tanggal konsultasi.";
    if (!$keluhan) $errors[] = "Tuliskan keluhan Anda.";

    if (empty($errors)) {
        $status = 'pending';
        $created_at = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO pendaftaran (pasien_id, dokter_id, tanggal, keluhan, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $pasien_id, $dokter_id, $tanggal, $keluhan, $status, $created_at);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Gagal menyimpan data konsultasi: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Form Konsultasi - RomCare Clinic</title>
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
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Form Konsultasi</h1>
                <p class="text-blue-100">Ajukan konsultasi dengan dokter RomCare Clinic</p>
            </div>
        </div>

        <?php if ($success): ?>
        <div class="glass-effect p-4 rounded-xl mb-6 border-l-4 border-green-500">
            <div class="flex items-center text-green-700">
                <i class="fas fa-check-circle mr-2"></i>
                Konsultasi berhasil didaftarkan. Silakan tunggu konfirmasi dari dokter.
            </div>
        </div>
        <?php endif; ?>

        <?php if ($errors): ?>
        <div class="glass-effect p-4 rounded-xl mb-6 border-l-4 border-red-500">
            <div class="text-red-700">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="font-medium">Mohon perbaiki kesalahan berikut:</span>
                </div>
                <ul class="space-y-1 ml-6 list-disc">
                    <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <div class="glass-effect rounded-xl p-6 shadow-lg max-w-3xl">
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="dokter_id"
                            class="block text-sm font-medium text-gray-700 mb-1">Pilih Dokter</label>
                        <div class="relative">
                            <i class="fas fa-user-md absolute left-3 top-3 text-gray-400"></i>
                            <select name="dokter_id" id="dokter_id" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="">Pilih Dokter</option>
                                <?php foreach ($dokters as $d): ?>
                                <option value="<?= $d['id'] ?>"
                                    <?= (isset($_POST['dokter_id']) && $_POST['dokter_id'] == $d['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['username']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="relative">
                        <label for="tanggal"
                            class="block text-sm font-medium text-gray-700 mb-1">Tanggal Konsultasi</label>
                        <div class="relative">
                            <i class="fas fa-calendar absolute left-3 top-3 text-gray-400"></i>
                            <input type="date" name="tanggal" id="tanggal" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="relative md:col-span-2">
                        <label for="keluhan"
                            class="block text-sm font-medium text-gray-700 mb-1">Keluhan</label>
                        <div class="relative">
                            <i class="fas fa-comment-medical absolute left-3 top-3 text-gray-400"></i>
                            <textarea name="keluhan" id="keluhan" rows="4" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-none"
                                placeholder="Tuliskan keluhan Anda..."><?= htmlspecialchars($_POST['keluhan'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                        class="gradient-card px-6 py-2.5 rounded-lg text-white font-semibold hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Konsultasi
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>