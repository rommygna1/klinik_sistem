<?php
session_start();
include_once '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
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
    <title>Dashboard Klinik - Kelola Tagihan</title>
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
                <h1 class="text-2xl font-bold text-gray-800 mb-1">Kelola Tagihan Pembayaran</h1>
                <p class="text-gray-600">Manajemen pembayaran dan status tagihan</p>
            </div>
        </div>

        <?php if (!empty($success)): ?>
        <div class="glass-effect p-4 rounded-xl mb-6 border-l-4 border-green-500">
            <div class="flex items-center text-green-700">
                <i class="fas fa-check-circle mr-2"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($query) === 0): ?>
        <div class="glass-effect p-8 rounded-2xl text-center">
            <div class="text-gray-500">
                <i class="fas fa-file-invoice text-4xl mb-3"></i>
                <p class="text-lg">Belum ada tagihan pembayaran.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="table-container overflow-hidden p-1">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Pasien</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">Nominal (Rp)</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Bukti Transfer</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-500"></i>
                                    </div>
                                    <span class="font-medium text-gray-700"><?= htmlspecialchars($row['pasien_nama']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-400 mr-2"></i>
                                    <?= date('d M Y', strtotime($row['created_at'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                Rp <?= number_format($row['nominal'], 2, ',', '.') ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($row['status'] === 'lunas'): ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle mr-1"></i>Lunas
                                </span>
                                <?php else: ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if ($row['bukti_transfer']): ?>
                                <a href="../uploads/bukti_transfer/<?= htmlspecialchars($row['bukti_transfer']) ?>"
                                    target="_blank" 
                                    class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                                    <i class="fas fa-image mr-1"></i>
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
                                    Tunggu upload bukti
                                </span>
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