<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

$dokter_id = $_SESSION['user']['id'];

// Ambil jadwal dokter sesuai dokter yang login
$queryJadwal = "SELECT hari, jam_mulai, jam_selesai FROM jadwal_dokter WHERE dokter_id = ? ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam_mulai";
$stmtJadwal = $conn->prepare($queryJadwal);
$stmtJadwal->bind_param("i", $dokter_id);
$stmtJadwal->execute();
$resultJadwal = $stmtJadwal->get_result();
$jadwals = $resultJadwal->fetch_all(MYSQLI_ASSOC);
$stmtJadwal->close();

// Ambil daftar konsultasi pasien untuk dokter ini
// Ambil daftar konsultasi pasien untuk dokter ini
$queryKonsul = "
    SELECT p.id, u.username AS nama_pasien, p.tanggal, p.keluhan, p.status, p.created_at
    FROM pendaftaran p
    JOIN users u ON p.pasien_id = u.id AND u.role = 'pasien'
    WHERE p.dokter_id = ?
    ORDER BY p.tanggal DESC, p.created_at DESC
";
$stmtKonsul = $conn->prepare($queryKonsul);
$stmtKonsul->bind_param("i", $dokter_id);
$stmtKonsul->execute();
$resultKonsul = $stmtKonsul->get_result();
$konsultasis = $resultKonsul->fetch_all(MYSQLI_ASSOC);
$stmtKonsul->close();


?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Dokter - Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white flex min-h-screen font-sans  font-modify">
    <?php include '../components/sidebar.php'; ?>

    <main class="flex-1 ml-64 p-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-4xl font-bold text-blue-600">Dashboard Dokter</h1>
            <!-- Gambar profil dokter -->
            <div class="w-16 h-16 rounded-full overflow-hidden border-4 border-blue-600 shadow-md">
                <img src="<?= htmlspecialchars($foto_dokter ?? 'https://png.pngtree.com/png-clipart/20231006/original/pngtree-cartoon-character-doctor-png-image_13129994.png') ?>"
                    alt="Foto Dokter" class="object-cover w-full h-full" />
            </div>
        </div>

        <!-- Jadwal Dokter -->
        <section class="mb-12">
            <h2 class="text-2xl font-semibold mb-5 text-blue-700">Jadwal Praktek Anda</h2>
            <?php if (count($jadwals) === 0): ?>
            <p class="text-gray-700">Jadwal praktek belum tersedia.</p>
            <?php else: ?>
            <table class="min-w-full bg-white rounded-lg shadow border border-blue-200">
                <thead>
                    <tr class="bg-blue-600 text-white text-left">
                        <th class="px-6 py-3 rounded-tl-lg">Hari</th>
                        <th class="px-6 py-3">Jam Mulai</th>
                        <th class="px-6 py-3 rounded-tr-lg">Jam Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jadwals as $j): ?>
                    <tr class="border-t border-blue-200 hover:bg-blue-50 transition-colors duration-200">
                        <td class="px-6 py-4 text-gray-800 font-medium"><?= htmlspecialchars($j['hari']) ?></td>
                        <td class="px-6 py-4 text-gray-700"><?= date('H:i', strtotime($j['jam_mulai'])) ?></td>
                        <td class="px-6 py-4 text-gray-700">
                            <?php
                                    $jam_mulai = strtotime($j['jam_mulai']);
                                    $jam_selesai = strtotime($j['jam_selesai']);
                                    if ($jam_selesai <= $jam_mulai) {
                                        echo date('H:i', $jam_selesai) . " (+1 hari)";
                                    } else {
                                        echo date('H:i', $jam_selesai);
                                    }
                                ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </section>

        <!-- Daftar Konsultasi Pasien -->
        <section>
            <h2 class="text-2xl font-semibold mb-5 text-blue-700">Daftar Konsultasi Pasien</h2>
            <?php if (count($konsultasis) === 0): ?>
            <p class="text-gray-700">Belum ada konsultasi dari pasien.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow border border-blue-200 text-sm">
                    <thead>
                        <tr class="bg-blue-600 text-white text-left">
                            <th class="px-6 py-3 rounded-tl-lg">ID</th>
                            <th class="px-6 py-3">Nama Pasien</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Keluhan</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 rounded-tr-lg">Didaftarkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($konsultasis as $k): ?>
                        <tr
                            class="border-t border-blue-200 hover:bg-blue-50 transition-colors duration-200 align-top">
                            <td class="px-6 py-4 text-gray-800 font-medium"><?= htmlspecialchars($k['id']) ?></td>
                            <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($k['nama_pasien']) ?></td>
                            <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($k['tanggal']) ?></td>
                            <td class="px-6 py-4 text-gray-700 whitespace-pre-line">
                                <?= htmlspecialchars($k['keluhan']) ?></td>
                            <td class="px-6 py-4 text-gray-700 font-semibold">
                                <?= htmlspecialchars(ucfirst($k['status'])) ?></td>
                            <td class="px-6 py-4 text-gray-600"><?= date('d M Y H:i', strtotime($k['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </section>
    </main>
</body>

</html>
