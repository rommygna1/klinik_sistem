<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
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
    <title>Form Konsultasi - Klinik</title>
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
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-1">Form Konsultasi</h1>
                <p class="text-gray-600">Manajemen konsultasi pasien dengan dokter</p>
            </div>
        </div>

        <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-lg glass-effect mb-6">
            Konsultasi berhasil didaftarkan. Silakan tunggu konfirmasi dari dokter.
        </div>
        <?php endif; ?>

        <?php if ($errors): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded-lg glass-effect mb-6">
            <ul class="list-disc pl-5 space-y-1">
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="glass-effect rounded-xl p-6 shadow-lg max-w-3xl">
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="dokter_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Dokter</label>
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
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Konsultasi</label>
                        <div class="relative">
                            <i class="fas fa-calendar absolute left-3 top-3 text-gray-400"></i>
                            <input type="date" name="tanggal" id="tanggal" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="relative md:col-span-2">
                        <label for="keluhan" class="block text-sm font-medium text-gray-700 mb-1">Keluhan</label>
                        <div class="relative">
                            <i class="fas fa-comment-medical absolute left-3 top-3 text-gray-400"></i>
                            <textarea name="keluhan" id="keluhan" rows="4" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-none"
                                placeholder="Tuliskan keluhan pasien..."><?= htmlspecialchars($_POST['keluhan'] ?? '') ?></textarea>
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