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
</head>

<body class="bg-white font-modify min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md min-h-screen sticky top-0">
        <?php include '../components/sidebar.php'; ?>
    </aside>

    <!-- Konten utama -->
    <main class="flex-1 p-8 overflow-auto bg-white rounded-l-lg">
        <h1 class="text-3xl font-bold mb-6 text-blue-700">History Rekam Medis</h1>

        <!-- Notifikasi -->
        <?php if ($errors): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php elseif ($success): ?>
        <div class="mb-4 p-4 bg-blue-100 text-blue-700 rounded">
            <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <?php if ($result->num_rows === 0): ?>
        <p class="text-gray-700">Belum ada rekam medis yang dibuat.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border border-blue-200 rounded">
                <thead>
                    <tr class="bg-blue-600 text-white text-left">
                        <th class="px-5 py-3 border border-blue-700 font-semibold">Tanggal</th>
                        <th class="px-5 py-3 border border-blue-700 font-semibold">Pasien</th>
                        <th class="px-5 py-3 border border-blue-700 font-semibold">Diagnosa</th>
                        <th class="px-5 py-3 border border-blue-700 font-semibold">Tindakan</th>
                        <th class="px-5 py-3 border border-blue-700 font-semibold">Resep</th>
                        <th class="px-5 py-3 border border-blue-700 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b border-blue-100 hover:bg-blue-50 transition-colors">
                        <td class="px-5 py-4 border border-blue-200 text-gray-800">
                            <?= htmlspecialchars(date('d M Y', strtotime($row['created_at']))) ?></td>
                        <td class="px-5 py-4 border border-blue-200 font-medium text-gray-800">
                            <?= htmlspecialchars($row['nama_pasien']) ?></td>
                        <td class="px-5 py-4 border border-blue-200 text-gray-700 whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($row['diagnosa'])) ?></td>
                        <td class="px-5 py-4 border border-blue-200 text-gray-700 whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($row['tindakan'])) ?></td>
                        <td class="px-5 py-4 border border-blue-200 text-gray-700 whitespace-pre-line">
                            <?= $row['resep'] ? nl2br(htmlspecialchars($row['resep'])) : '-' ?></td>
                        <td class="px-5 py-4 border border-blue-200 space-x-3">
                            <button onclick='openModal(<?= htmlspecialchars(json_encode($row)) ?>)'
                                class="text-yellow-600 hover:underline font-semibold">Edit</button>
                            <button onclick="confirmDelete(<?= $row['id'] ?>)"
                                class="text-red-600 hover:underline font-semibold">Hapus</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>

    <!-- Modal Edit -->
    <div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative border border-blue-200">
            <button onclick="closeModal()"
                class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 font-bold text-xl">&times;</button>
            <h2 class="text-2xl font-bold mb-4 text-blue-700">Edit Rekam Medis</h2>

            <form method="post" id="formEdit">
                <input type="hidden" name="edit_id" id="edit_id" />

                <label class="block mb-2 font-semibold text-gray-700">Diagnosa:</label>
                <textarea name="diagnosa" id="diagnosa" rows="4" class="w-full p-2 border border-blue-300 rounded"
                    required></textarea>

                <label class="block mt-4 mb-2 font-semibold text-gray-700">Tindakan:</label>
                <textarea name="tindakan" id="tindakan" rows="4" class="w-full p-2 border border-blue-300 rounded"
                    required></textarea>

                <label class="block mt-4 mb-2 font-semibold text-gray-700">Resep (opsional):</label>
                <textarea name="resep" id="resep" rows="3"
                    class="w-full p-2 border border-blue-300 rounded"></textarea>

                <div class="mt-6 flex justify-end space-x-4">
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Simpan</button>
                    <button type="button" onclick="closeModal()"
                        class="px-6 py-2 border border-blue-400 rounded hover:bg-blue-100 transition">Batal</button>
                </div>
            </form>
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