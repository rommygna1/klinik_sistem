<?php
session_start();
include_once '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resepsionis') {
    header('Location: ../auth/login.php');
    exit;
}

// Handle update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $tagihan_id = $_POST['tagihan_id'];
    $status = $_POST['status'];

    if (in_array($status, ['pending', 'lunas'])) {
        $tagihan_id = mysqli_real_escape_string($conn, $tagihan_id);
        $status = mysqli_real_escape_string($conn, $status);

        mysqli_query($conn, "UPDATE tagihan_pembayaran SET status='$status' WHERE id='$tagihan_id'");
        $success = 'Status pembayaran berhasil diupdate.';
    }
}

// Ambil data tagihan dengan nama pasien dari tabel users
$query = mysqli_query($conn, "SELECT tp.*, u.username AS pasien_nama FROM tagihan_pembayaran tp JOIN users u ON tp.pasien_id = u.id ORDER BY tp.created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Pembayaran - RomCare Clinic</title>
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
                    <path fill="#FFFFFF" d="M47.5,-57.5C59.2,-46.1,65.1,-29.3,65.6,-13.1C66.1,3.1,61.1,18.6,51.8,30.5C42.5,42.4,28.9,50.6,13.4,55.2C-2.1,59.8,-19.4,60.8,-33.9,54.3C-48.4,47.8,-60.1,33.9,-65.3,17.1C-70.5,0.3,-69.3,-19.4,-60.1,-33.8C-50.9,-48.2,-33.7,-57.4,-16.1,-61.4C1.5,-65.4,19.4,-64.2,35.8,-68.9C52.2,-73.6,67.1,-84.2,47.5,-57.5Z" transform="translate(100 100)" />
                </svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">Kelola Tagihan Pembayaran</h1>
                <p class="text-blue-100">Manajemen dan verifikasi pembayaran pasien</p>
            </div>
        </div>

        <?php if (!empty($success)): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-lg glass-effect mb-6">
            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <!-- Table Section -->
        <div class="glass-effect rounded-xl shadow-lg overflow-hidden">
            <?php if (mysqli_num_rows($query) === 0): ?>
                <div class="p-8 text-center">
                    <div class="w-16 h-16 gradient-card rounded-full mx-auto flex items-center justify-center mb-4">
                        <i class="fas fa-file-invoice text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Tidak Ada Tagihan</h3>
                    <p class="text-gray-600">Belum ada tagihan pembayaran yang perlu dikelola.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-600">
                                <th class="px-6 py-4 text-left font-semibold">Pasien</th>
                                <th class="px-6 py-4 text-left font-semibold">Tanggal</th>
                                <th class="px-6 py-4 text-right font-semibold">Nominal</th>
                                <th class="px-6 py-4 text-center font-semibold">Status</th>
                                <th class="px-6 py-4 text-center font-semibold">Bukti Transfer</th>
                                <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full gradient-card flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <span class="font-medium text-gray-800"><?= htmlspecialchars($row['pasien_nama']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 text-right font-medium">
                                    Rp <?= number_format($row['nominal'], 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($row['status'] === 'lunas'): ?>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                            Lunas
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                                            Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($row['bukti_transfer']): ?>
                                        <a href="../uploads/bukti_transfer/<?= htmlspecialchars($row['bukti_transfer']) ?>"
                                           target="_blank"
                                           class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                                            <i class="fas fa-image"></i>
                                            Lihat Bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-lg bg-gray-100 text-gray-500">
                                            <i class="fas fa-upload mr-1"></i>
                                            Belum upload
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($row['bukti_transfer']): ?>
                                        <form method="POST" class="inline-flex items-center gap-2">
                                            <input type="hidden" name="tagihan_id" value="<?= $row['id'] ?>">
                                            <select name="status" 
                                                class="pl-3 pr-8 py-1.5 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none text-sm">
                                                <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="lunas" <?= $row['status'] === 'lunas' ? 'selected' : '' ?>>Lunas</option>
                                            </select>
                                            <button type="submit" name="update_status"
                                                    class="gradient-card px-4 py-1.5 rounded-lg text-white text-sm font-medium hover:shadow-lg transition-all duration-300 inline-flex items-center">
                                                <i class="fas fa-save mr-1"></i>
                                                Update
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-lg bg-gray-100 text-gray-400">
                                            <i class="fas fa-hourglass mr-1"></i>
                                            Menunggu
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>