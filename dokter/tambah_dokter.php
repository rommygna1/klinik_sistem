<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_dokter'])) {
    $nama = trim($_POST['nama']);
    $spesialisasi = trim($_POST['spesialis']);
    $no_telepon = trim($_POST['no_telepon']);
    $email = trim($_POST['email']);
    $alamat = trim($_POST['alamat']);

    if ($nama === '' || $spesialisasi === '' || $no_telepon === '' || $email === '' || $alamat === '') {
        $error = 'Semua field harus diisi.';
    } else {
        // Simpan ke tabel dokter
        $stmt = $conn->prepare("INSERT INTO dokter (nama, spesialis, no_telepon, email, alamat) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $spesialisasi, $no_telepon, $email, $alamat);

        if ($stmt->execute()) {
            $success = "Dokter berhasil ditambahkan.";
        } else {
            $error = "Terjadi kesalahan saat menyimpan data: " . $stmt->error;
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
    <title>Tambah Dokter - RomCare Clinic</title>
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
                    <h1 class="text-3xl font-bold mb-2">Tambah Dokter Baru</h1>
                    <p class="text-blue-100">Tambahkan profil dokter baru ke RomCare Clinic</p>
                </div>
                <div
                    class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center bg-white/20 backdrop-blur-sm">
                    <i class="fas fa-user-md text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
        <div class="glass-effect p-4 rounded-xl mb-6 border-l-4 border-red-500">
            <div class="flex items-center text-red-700">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="glass-effect p-4 rounded-xl mb-6 border-l-4 border-green-500">
            <div class="flex items-center text-green-700">
                <i class="fas fa-check-circle mr-2"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="glass-effect rounded-xl p-8 shadow-lg max-w-3xl">
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Dokter</label>
                        <div class="relative">
                            <i class="fas fa-user-md absolute left-3 top-3 text-gray-400"></i>
                            <input id="nama" name="nama" type="text" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                placeholder="Masukkan nama dokter" />
                        </div>
                    </div>

                    <div class="relative">
                        <label for="spesialisasi" class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi
                        </label>
                        <div class="relative">
                            <i class="fas fa-stethoscope absolute left-3 top-3 text-gray-400"></i>
                            <input id="spesialisasi" name="spesialis" type="text" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                placeholder="Masukkan spesialisasi" />
                        </div>
                    </div>

                    <div class="relative">
                        <label for="no_telepon" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-3 top-3 text-gray-400"></i>
                            <input id="no_telepon" name="no_telepon" type="tel" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                placeholder="Masukkan nomor telepon" />
                        </div>
                    </div>

                    <div class="relative">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
                            <input id="email" name="email" type="email" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                placeholder="Masukkan alamat email" />
                        </div>
                    </div>

                    <div class="relative md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                        <div class="relative">
                            <i class="fas fa-map-marker-alt absolute left-3 top-3 text-gray-400"></i>
                            <textarea id="alamat" name="alamat" rows="4" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-none"
                                placeholder="Masukkan alamat lengkap"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" name="tambah_dokter"
                        class="gradient-card px-6 py-2.5 rounded-lg text-white font-semibold hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        Tambah Dokter
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>
