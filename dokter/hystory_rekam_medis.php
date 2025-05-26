<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: ../auth/login.php');
    exit;
}

$dokter_id = $_SESSION['user']['id'];

$errors = [];
$success = null;

// Proses update rekam medis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = (int)$_POST['edit_id'];
    $diagnosa = trim($_POST['diagnosa']);
    $tindakan = trim($_POST['tindakan']);
    $resep = trim($_POST['resep']);

    if (!$diagnosa) $errors[] = "Diagnosa tidak boleh kosong.";
    if (!$tindakan) $errors[] = "Tindakan tidak boleh kosong.";

    if (!$errors) {
        $stmtUpdate = $conn->prepare("UPDATE rekam_medis SET diagnosa = ?, tindakan = ?, resep = ?, updated_at = NOW() WHERE id = ? AND dokter_id = ?");
        $stmtUpdate->bind_param("sssii", $diagnosa, $tindakan, $resep, $edit_id, $dokter_id);
        $stmtUpdate->execute();

        if ($stmtUpdate->affected_rows > 0) {
            $success = "Rekam medis berhasil diperbarui.";
        } else {
            $errors[] = "Gagal memperbarui rekam medis atau tidak ada perubahan.";
        }
    }
}

// Hapus rekam medis
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmtDel = $conn->prepare("DELETE FROM rekam_medis WHERE id = ? AND dokter_id = ?");
    $stmtDel->bind_param("ii", $delete_id, $dokter_id);
    $stmtDel->execute();
    header('Location: hystory_rekam_medis.php');
    exit;
}

// Ambil data rekam medis dokter
$query = "
    SELECT 
        rm.id,
        rm.created_at,
        rm.diagnosa,
        rm.tindakan,
        rm.resep,
        u.username AS nama_pasien
    FROM rekam_medis rm
    JOIN users u ON rm.pasien_id = u.id
    WHERE rm.dokter_id = ?
    ORDER BY rm.created_at DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $dokter_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dokter - History Rekam Medis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        <!-- Header Section -->
        <div class="gradient-card rounded-2xl p-8 mb-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 opacity-10">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#FFFFFF" d="M47.5,-57.5C59.2,-46.1,65.1,-29.3,65.6,-13.1C66.1,3.1,61.1,18.6,51.8,30.5C42.5,42.4,28.9,50.6,13.4,55.2C-2.1,59.8,-19.4,60.8,-33.9,54.3C-48.4,47.8,-60.1,33.9,-65.3,17.1C-70.5,0.3,-69.3,-19.4,-60.1,-33.8C-50.9,-48.2,-33.7,-57.4,-16.1,-61.4C1.5,-65.4,19.4,-64.2,35.8,-68.9C52.2,-73.6,67..." />
                </svg>
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-2">History Rekam Medis</h1>
                <p class="text-blue-100">Kelola riwayat rekam medis pasien</p>
            </div>
        </div>

        <!-- Notifications -->
        <?php if ($errors): ?>
        <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php elseif ($success): ?>
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <!-- Main Content -->
        <?php if ($result->num_rows === 0): ?>
        <div class="text-center py-12 glass-effect rounded-xl">
            <div class="w-16 h-16 gradient-card rounded-full mx-auto flex items-center justify-center mb-4">
                <i class="fas fa-file-medical text-white text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Rekam Medis</h3>
            <p class="text-gray-600">Belum ada rekam medis yang dibuat.</p>
        </div>
        <?php else: ?>
        <div class="table-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Pasien</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Diagnosa</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Tindakan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Resep</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar-alt text-blue-500"></i>
                                    </div>
                                    <span class="text-gray-700"><?= htmlspecialchars(date('d M Y', strtotime($row['created_at']))) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-blue-500"></i>
                                    </div>
                                    <span class="font-medium text-gray-700"><?= htmlspecialchars($row['nama_pasien']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate"><?= nl2br(htmlspecialchars($row['diagnosa'])) ?></td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate"><?= nl2br(htmlspecialchars($row['tindakan'])) ?></td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate"><?= $row['resep'] ? nl2br(htmlspecialchars($row['resep'])) : '-' ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button onclick='openModal(<?= htmlspecialchars(json_encode($row)) ?>)'
                                        class="p-2 rounded-full hover:bg-blue-100 text-blue-600 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="confirmDelete(<?= $row['id'] ?>)"
                                        class="p-2 rounded-full hover:bg-red-100 text-red-600 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Modal Edit -->
    <div id="modalEdit" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
        <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl">
            <div class="glass-effect rounded-xl p-6 shadow-xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">
                        <i class="fas fa-edit text-blue-600 mr-2"></i>Edit Rekam Medis
                    </h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="post" id="formEdit" class="space-y-4">
                    <input type="hidden" name="edit_id" id="edit_id" />

                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosa</label>
                        <div class="relative">
                            <i class="fas fa-stethoscope absolute left-3 top-3 text-gray-400"></i>
                            <textarea name="diagnosa" id="diagnosa" rows="4" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"></textarea>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tindakan</label>
                        <div class="relative">
                            <i class="fas fa-hand-holding-medical absolute left-3 top-3 text-gray-400"></i>
                            <textarea name="tindakan" id="tindakan" rows="4" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"></textarea>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Resep (opsional)</label>
                        <div class="relative">
                            <i class="fas fa-prescription absolute left-3 top-3 text-gray-400"></i>
                            <textarea name="resep" id="resep" rows="3"
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="gradient-card px-6 py-2 rounded-lg text-white font-medium hover:shadow-lg transition-all duration-300">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openModal(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('diagnosa').value = data.diagnosa;
        document.getElementById('tindakan').value = data.tindakan;
        document.getElementById('resep').value = data.resep || '';
        document.getElementById('modalEdit').classList.remove('hidden');
        document.getElementById('modalEdit').classList.add('flex');
        window.scrollTo(0, 0);
    }

    function closeModal() {
        document.getElementById('modalEdit').classList.add('hidden');
        document.getElementById('modalEdit').classList.remove('flex');
    }

    function confirmDelete(id) {
        if (confirm("Yakin ingin menghapus rekam medis ini?")) {
            window.location.href = "?delete_id=" + id;
        }
    }
    </script>
</body>
</html>