<?php
session_start();
require '../config/koneksi.php';

// Pastikan pasien sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$pasien_id = $_SESSION['user']['id'];

// Ambil rekam medis pasien dengan nama dokter dari tabel users
$query = "
    SELECT 
        rm.id,
        rm.created_at,
        rm.diagnosa,
        rm.tindakan,
        rm.resep,
        u.username AS nama_dokter
    FROM rekam_medis rm
    JOIN users u ON rm.dokter_id = u.id
    WHERE rm.pasien_id = ?
    ORDER BY rm.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pasien_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pasien - Rekam Medis Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md min-h-screen sticky top-0">
        <?php include '../components/sidebar.php'; ?>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 p-8 overflow-auto bg-white rounded-l-lg">
        <h1 class="text-3xl font-bold mb-6 text-blue-700">Rekam Medis Saya</h1>

        <?php if ($result->num_rows === 0): ?>
        <p class="text-gray-700">Seluruh rekam medis</p>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border border-gray-300 rounded">
                <thead>
                    <tr class="bg-blue-600 text-white text-left">
                        <th class="px-4 py-3 border border-blue-700">Tanggal</th>
                        <th class="px-4 py-3 border border-blue-700">Dokter</th>
                        <th class="px-4 py-3 border border-blue-700">Diagnosa</th>
                        <th class="px-4 py-3 border border-blue-700">Tindakan</th>
                        <th class="px-4 py-3 border border-blue-700">Resep</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-blue-50">
                        <td class="px-4 py-3 border border-gray-300">
                            <?= htmlspecialchars(date('d M Y', strtotime($row['created_at']))) ?></td>
                        <td class="px-4 py-3 border border-gray-300"><?= htmlspecialchars($row['nama_dokter']) ?></td>
                        <td class="px-4 py-3 border border-gray-300"><?= nl2br(htmlspecialchars($row['diagnosa'])) ?>
                        </td>
                        <td class="px-4 py-3 border border-gray-300"><?= nl2br(htmlspecialchars($row['tindakan'])) ?>
                        </td>
                        <td class="px-4 py-3 border border-gray-300">
                            <?= $row['resep'] ? nl2br(htmlspecialchars($row['resep'])) : '-' ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</body>

</html>