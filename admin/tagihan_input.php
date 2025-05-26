<?php
session_start();
include_once '../config/koneksi.php';

// Cek role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Ambil list pasien dari tabel users
$pasienQuery = mysqli_query($conn, "SELECT id, username FROM users WHERE role='pasien' ORDER BY username ASC");

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pasien_id = $_POST['pasien_id'] ?? '';
    $nominal = $_POST['nominal'] ?? '';

    if (!$pasien_id) $errors[] = 'Pasien harus dipilih.';
    if (!$nominal || !is_numeric($nominal) || $nominal <= 0) $errors[] = 'Nominal harus diisi dengan angka lebih dari 0.';

    if (!$errors) {
        $pasien_id = mysqli_real_escape_string($conn, $pasien_id);
        $nominal = mysqli_real_escape_string($conn, $nominal);

        $insert = mysqli_query($conn, "INSERT INTO tagihan_pembayaran (pasien_id, nominal) VALUES ('$pasien_id', '$nominal')");

        if ($insert) {
            $success = 'Tagihan berhasil ditambahkan.';
        } else {
            $errors[] = 'Gagal menambahkan tagihan: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input Tagihan - Klinik</title>
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
                <h1 class="text-2xl font-bold text-gray-800 mb-1">Input Tagihan Pembayaran</h1>
                <p class="text-gray-600">Manajemen tagihan pembayaran pasien</p>
            </div>
        </div>

        <?php if ($errors): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded-lg glass-effect mb-6">
            <?= implode('<br>', $errors) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-lg glass-effect mb-6">
            <?= $success ?>
        </div>
        <?php endif; ?>

        <div class="glass-effect rounded-xl p-6 shadow-lg max-w-3xl">
            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 gap-6">
                    <div class="relative">
                        <label for="pasien_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Pasien</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-3 text-gray-400"></i>
                            <select id="pasien_id" name="pasien_id" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="">Pilih Pasien</option>
                                <?php while ($row = mysqli_fetch_assoc($pasienQuery)): ?>
                                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['username']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="relative">
                        <label for="nominal" class="block text-sm font-medium text-gray-700 mb-1">Nominal Tagihan (Rp)</label>
                        <div class="relative">
                            <i class="fas fa-money-bill absolute left-3 top-3 text-gray-400"></i>
                            <input type="number" id="nominal" name="nominal" min="1" step="0.01" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                placeholder="Masukkan nominal tagihan" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                        class="gradient-card px-6 py-2.5 rounded-lg text-white font-semibold hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-plus"></i>
                        Tambah Tagihan
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>