<?php
session_start();
include_once '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pasien') {
    header('Location: ../auth/login.php');
    exit;
}

$pasien_id = $_SESSION['user']['id'];
$errors = [];
$success = '';

// Handle upload bukti transfer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_bukti'])) {
    $tagihan_id = $_POST['tagihan_id'];
    if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Gagal upload file bukti transfer.';
    } else {
        $file = $_FILES['bukti'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format file tidak diperbolehkan. Gunakan jpg, jpeg, png, gif.';
        } else {
            $folder = '../uploads/bukti_transfer/';
            if (!is_dir($folder)) mkdir($folder, 0755, true);

            $newName = 'bukti_'.$tagihan_id.'_'.time().'.'.$ext;
            $targetPath = $folder . $newName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $tagihan_id = mysqli_real_escape_string($conn, $tagihan_id);
                $newName = mysqli_real_escape_string($conn, $newName);

                $update = mysqli_query($conn, "UPDATE tagihan_pembayaran SET bukti_transfer='$newName', status='pending' WHERE id='$tagihan_id' AND pasien_id='$pasien_id'");

                if ($update) {
                    $success = 'Bukti transfer berhasil diupload. Silakan tunggu konfirmasi dari admin.';
                } else {
                    $errors[] = 'Gagal menyimpan data bukti transfer.';
                    unlink($targetPath); // hapus file jika gagal simpan DB
                }
            } else {
                $errors[] = 'Gagal memindahkan file upload.';
            }
        }
    }
}

// Ambil data tagihan pasien
$query = mysqli_query($conn, "SELECT * FROM tagihan_pembayaran WHERE pasien_id = '$pasien_id' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tagihan Pembayaran - RomCare Clinic</title>
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
        .file-input-wrapper:hover {
            background: rgba(59, 130, 246, 0.1);
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
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Tagihan Pembayaran</h1>
                    <p class="text-blue-100">Kelola tagihan pembayaran Anda di RomCare Clinic</p>
                </div>
                <div class="w-12 h-12 gradient-card rounded-lg flex items-center justify-center bg-white/20 backdrop-blur-sm">
                    <i class="fas fa-file-invoice-dollar text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <?php if ($errors): ?>
        <div class="glass-effect p-4 rounded-xl mb-6 border-l-4 border-red-500">
            <div class="text-red-700">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="font-medium">Mohon perbaiki kesalahan berikut:</span>
                </div>
                <ul class="space-y-1 ml-6 list-disc">
                    <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="glass-effect p-4 rounded-xl mb-6 border-l-4 border-green-500">
            <div class="flex items-center text-green-700">
                <i class="fas fa-check-circle mr-2"></i>
                <?= $success ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($query) === 0): ?>
        <div class="glass-effect rounded-xl p-8 text-center">
            <div class="w-16 h-16 gradient-card rounded-full mx-auto flex items-center justify-center mb-4">
                <i class="fas fa-file-invoice text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Tidak Ada Tagihan</h3>
            <p class="text-gray-600">Anda belum memiliki tagihan pembayaran yang perlu diselesaikan.</p>
        </div>
        <?php else: ?>
        <div class="table-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-600">Nominal</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Bukti Transfer</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-calendar-day text-blue-500"></i>
                                    </div>
                                    <span class="text-gray-700">
                                        <?= date('d M Y', strtotime($row['created_at'])) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-semibold text-gray-800">
                                    Rp <?= number_format($row['nominal'], 2, ',', '.') ?>
                                </span>
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
                            <td class="px-6 py-4">
                                <?php if ($row['status'] !== 'lunas'): ?>
                                <form method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                                    <input type="hidden" name="tagihan_id" value="<?= $row['id'] ?>">
                                    <div class="relative file-input-wrapper rounded-lg border-2 border-dashed border-blue-200 hover:border-blue-400 transition-colors p-2">
                                        <input type="file" name="bukti" accept="image/*" required
                                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                                        <div class="text-center text-sm text-gray-500">
                                            <i class="fas fa-cloud-upload-alt text-blue-400 text-lg mb-1"></i><br>
                                            Pilih file bukti transfer
                                        </div>
                                    </div>
                                    <button type="submit" name="upload_bukti"
                                        class="gradient-card w-full px-4 py-2 rounded-lg text-white text-sm font-medium hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
                                        <i class="fas fa-paper-plane"></i>
                                        Upload Bukti
                                    </button>
                                </form>
                                <?php else: ?>
                                <div class="text-center">
                                    <span class="px-3 py-1 rounded-lg bg-green-100 text-green-700 inline-flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i>
                                        Pembayaran Selesai
                                    </span>
                                </div>
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