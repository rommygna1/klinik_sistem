<?php
session_start();
require '../config/koneksi.php';

// Pastikan dokter sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: ../auth/login.php');
    exit;
}

$dokter_id = $_SESSION['user']['id'];

// Proses jika dokter menyetujui pendaftaran pasien
if (isset($_GET['approve'])) {
    $approve_id = intval($_GET['approve']);

    // Update status menjadi 'diterima'
    $stmt = $conn->prepare("UPDATE pendaftaran SET status = 'diterima' WHERE id = ? AND dokter_id = ?");
    $stmt->bind_param("ii", $approve_id, $dokter_id);
    $stmt->execute();
    $stmt->close();

    // Redirect kembali ke halaman untuk hindari resubmission
    header("Location: konsultasi.php");
    exit;
}

// Ambil semua data pendaftaran pasien untuk dokter ini
$query = "
    SELECT 
        p.id AS pendaftaran_id,
        p.status,
        p.tanggal,
        p.keluhan,
        u.id AS pasien_id,
        u.username AS nama_pasien
    FROM pendaftaran p
    JOIN users u ON p.pasien_id = u.id
    WHERE p.dokter_id = $dokter_id
    ORDER BY p.created_at DESC
";

$result = $conn->query($query);

// Jika query gagal, tampilkan error untuk debugging
if (!$result) {
    die("Query error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dokter - Daftar Pasien Konsultasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white min-h-screen flex font-sans font-modify">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md min-h-screen sticky top-0 border-r border-blue-200">
        <?php include '../components/sidebar.php'; ?>
    </aside>

    <!-- Konten Utama -->
    <main class="flex-1 p-8 overflow-auto">
        <h1 class="text-3xl font-bold mb-6 text-blue-700">Daftar Pasien Konsultasi</h1>

        <?php if ($result->num_rows === 0): ?>
        <p class="text-gray-700">Belum ada pasien yang mendaftar konsultasi.</p>
        <?php else: ?>
        <div class="overflow-x-auto bg-white rounded-lg shadow-md p-6 border border-blue-100">
            <table class="min-w-full table-auto border-collapse">
                <thead>
                    <tr class="bg-blue-600 text-white text-left">
                        <th class="px-5 py-3 font-semibold">ID</th>
                        <th class="px-5 py-3 font-semibold">Username Pasien</th>
                        <th class="px-5 py-3 font-semibold">Tanggal</th>
                        <th class="px-5 py-3 font-semibold">Keluhan</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b border-blue-100 hover:bg-blue-50 transition-colors">
                        <td class="px-5 py-4 text-gray-800"><?= htmlspecialchars($row['pendaftaran_id']) ?></td>
                        <td class="px-5 py-4 text-gray-800 font-medium"><?= htmlspecialchars($row['nama_pasien']) ?>
                        </td>
                        <td class="px-5 py-4 text-gray-700"><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td class="px-5 py-4 text-gray-700 max-w-xs truncate"><?= htmlspecialchars($row['keluhan']) ?>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-3 py-1 text-sm rounded font-semibold
                                        <?= $row['status'] === 'diterima' 
                                            ? 'bg-blue-200 text-blue-800' 
                                            : 'bg-yellow-200 text-yellow-800' ?>">
                                <?= htmlspecialchars(ucfirst($row['status'])) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <?php if ($row['status'] === 'diterima'): ?>
                            <a href="rekam_medis.php?pasien_id=<?= $row['pasien_id'] ?>"
                                class="text-blue-700 hover:text-blue-900 font-semibold underline">
                                Isi Rekam Medis
                            </a>
                            <?php else: ?>
                            <a href="konsultasi.php?approve=<?= $row['pendaftaran_id'] ?>"
                                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold transition">
                                Setujui
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
</body>

</html>