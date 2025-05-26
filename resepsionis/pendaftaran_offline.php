<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resepsionis') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

// Ambil data dokter dari tabel users
$dokter = mysqli_query($conn, "SELECT * FROM users WHERE role='dokter'");

if (isset($_POST['submit'])) {
    $nama_pasien = $_POST['nama_pasien'];
    $dokter_id = $_POST['dokter_id'];
    $tanggal = $_POST['tanggal'];
    $keluhan = $_POST['keluhan'];

    // Buat user pasien baru
    $password = password_hash('12345', PASSWORD_DEFAULT); // Password default
    $insert_pasien = mysqli_query($conn, "INSERT INTO users (username, role) VALUES ('$nama_pasien', 'pasien')");
    
    if ($insert_pasien) {
        $pasien_id = mysqli_insert_id($conn);
        // Simpan ke database pendaftaran
        $insert = mysqli_query($conn, "INSERT INTO pendaftaran (pasien_id, dokter_id, tanggal, keluhan, status) VALUES ('$pasien_id', '$dokter_id', '$tanggal', '$keluhan', 'menunggu')");

        if ($insert) {
            $last_id = mysqli_insert_id($conn);
            header("Location: cetak_kartu.php?id=$last_id");
            exit;
        } else {
            $error = "Gagal menyimpan pendaftaran.";
        }
    } else {
        $error = "Gagal membuat akun pasien.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pendaftaran Pasien - RomCare Clinic</title>
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

    <main class="ml-[300px] pr-8 pl-2 py-6">
        <!-- Header Section -->
        <div class="gradient-card rounded-2xl p-6 mb-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#FFFFFF" d="M47.5,-57.5C59.2,-46.1,65.1,-29.3,65.6,-13.1C66.1,3.1,61.1,18.6,51.8,30.5C42.5,42.4,28.9,50.6,13.4,55.2C-2.1,59.8,-19.4,60.8,-33.9,54.3C-48.4,47.8,-60.1,33.9,-65.3,17.1C-70.5,0.3,-69.3,-19.4,-60.1,-33.8C-50.9,-48.2,-33.7,-57.4,-16.1,-61.4C1.5,-65.4,19.4,-64.2,35.8,-68.9C52.2,-73.6,67.1,-84.2,47.5,-57.5Z" transform="translate(100 100)" />
                </svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-2xl font-bold mb-1">Pendaftaran Pasien Baru</h1>
                <p class="text-blue-100">Daftarkan pasien baru untuk konsultasi</p>
            </div>
        </div>

        <?php if (isset($error)): ?>
        <div class="glass-effect p-4 rounded-xl mb-6 border-l-4 border-red-500">
            <div class="text-red-700">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= $error ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="glass-effect rounded-xl p-6 shadow-lg">
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Manual Patient Input -->
                    <div class="relative">
                        <label for="nama_pasien" class="block text-sm font-medium text-gray-700 mb-1">Nama Pasien</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" id="nama_pasien" name="nama_pasien" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                placeholder="Masukkan nama lengkap" />
                        </div>
                    </div>

                    <div class="relative">
                        <label for="dokter_id" class="block text-sm font-medium text-gray-700 mb-1">Dokter</label>
                        <div class="relative">
                            <i class="fas fa-user-md absolute left-3 top-3 text-gray-400"></i>
                            <select id="dokter_id" name="dokter_id" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none">
                                <option value="">Pilih Dokter</option>
                                <?php while ($row = mysqli_fetch_assoc($dokter)): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['username']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kunjungan</label>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-3 text-gray-400"></i>
                        <input type="date" id="tanggal" name="tanggal" required
                            class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" />
                    </div>
                </div>

                <div class="relative">
                    <label for="keluhan" class="block text-sm font-medium text-gray-700 mb-1">Keluhan</label>
                    <div class="relative">
                        <i class="fas fa-comment-medical absolute left-3 top-3 text-gray-400"></i>
                        <textarea id="keluhan" name="keluhan" rows="4" required
                            class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-none"
                            placeholder="Tuliskan keluhan pasien..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" name="submit"
                        class="gradient-card px-6 py-2.5 rounded-lg text-white font-semibold hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>