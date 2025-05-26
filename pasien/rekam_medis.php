<?php
session_start();
require '../config/koneksi.php';

// Pastikan pasien sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pasien') {
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
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    modify: ['Inter', 'sans-serif'],
                },
                colors: {
                    primary: '#3B82F6', // blue-500
                }
            }
        }
    };
    </script>
</head>

<body class="bg-gray-100 min-h-screen flex font-modify">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md min-h-screen sticky top-0">
        <?php include '../components/sidebar.php'; ?>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 p-8 overflow-auto bg-white rounded-l-lg shadow">
        <!-- Judul -->
        <div class="flex items-center gap-3 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 7v10a1 1 0 001 1h4m10-11h2a1 1 0 011 1v10a1 1 0 01-1 1h-2M7 7V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h1 class="text-3xl font-bold text-primary">Rekam Medis Saya</h1>
        </div>

        <!-- Tabel Rekam Medis -->
        <?php if ($result->num_rows === 0): ?>
        <p class="text-gray-700">Belum ada rekam medis.</p>
        <?php else: ?>
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="px-4 py-3 border-r border-blue-700">Tanggal</th>
                        <th class="px-4 py-3 border-r border-blue-700">Dokter</th>
                        <th class="px-4 py-3 border-r border-blue-700">Diagnosa</th>
                        <th class="px-4 py-3 border-r border-blue-700">Tindakan</th>
                        <th class="px-4 py-3">Resep</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-blue-50 border-t">
                        <td class="px-4 py-3 border-r">
                            <?= htmlspecialchars(date('d M Y', strtotime($row['created_at']))) ?></td>
                        <td class="px-4 py-3 border-r"><?= htmlspecialchars($row['nama_dokter']) ?></td>
                        <td class="px-4 py-3 border-r"><?= nl2br(htmlspecialchars($row['diagnosa'])) ?></td>
                        <td class="px-4 py-3 border-r"><?= nl2br(htmlspecialchars($row['tindakan'])) ?></td>
                        <td class="px-4 py-3"><?= $row['resep'] ? nl2br(htmlspecialchars($row['resep'])) : '-' ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</body>

</html>