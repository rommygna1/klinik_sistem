<?php
session_start();
require '../config/koneksi.php';

// Pastikan dokter sudah login
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: ../auth/login.php');
    exit;
}

$dokter_id = $_SESSION['user']['id'];

// Ambil pasien_id dari query string
$pasien_id = isset($_GET['pasien_id']) ? intval($_GET['pasien_id']) : 0;

// Ambil data pendaftaran yang valid untuk dokter dan pasien ini (status diterima)
$stmt = $conn->prepare("
    SELECT p.*, 
           u_pasien.id AS pasien_id, u_pasien.username AS nama_pasien,
           u_dokter.id AS dokter_id, u_dokter.username AS nama_dokter
    FROM pendaftaran p
    JOIN users u_pasien ON p.pasien_id = u_pasien.id AND u_pasien.role = 'pasien'
    JOIN users u_dokter ON p.dokter_id = u_dokter.id AND u_dokter.role = 'dokter'
    WHERE p.dokter_id = ? AND p.pasien_id = ? AND p.status = 'diterima'
    ORDER BY p.tanggal DESC
    LIMIT 1
");
$stmt->bind_param("ii", $dokter_id, $pasien_id);
$stmt->execute();
$pendaftaran = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$pendaftaran) {
    die("Data pendaftaran tidak ditemukan atau belum disetujui.");
}

// Handle form submit untuk simpan rekam medis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnosa = $_POST['diagnosa'] ?? '';
    $tindakan = $_POST['tindakan'] ?? '';
    $resep = $_POST['resep'] ?? '';

    if (!$diagnosa || !$tindakan) {
        $error = "Diagnosa dan tindakan wajib diisi.";
    } else {
      $stmt = $conn->prepare("INSERT INTO rekam_medis (pendaftaran_id, dokter_id, pasien_id, diagnosa, tindakan, resep) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiisss", $pendaftaran_id, $dokter_id, $pasien_id, $diagnosa, $tindakan, $resep);

        $stmt->execute();
        $stmt->close();

        header("Location: konsultasi.php?message=rekam_medis_berhasil");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Isi Rekam Medis</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">
    <aside class="w-64 bg-white shadow-md min-h-screen sticky top-0">
        <?php include '../components/sidebar.php'; ?>
    </aside>

    <main class="flex-1 p-8 overflow-auto bg-white rounded shadow-md m-6">
        <h1 class="text-3xl font-bold mb-6 text-blue-700">Isi Rekam Medis Pasien</h1>

        <div class="mb-4 text-gray-700">
            <p><strong>Pasien:</strong> <?= htmlspecialchars($pendaftaran['nama_pasien']) ?></p>
            <p><strong>Tanggal Konsultasi:</strong> <?= htmlspecialchars($pendaftaran['tanggal']) ?></p>
            <p><strong>Keluhan:</strong> <?= htmlspecialchars($pendaftaran['keluhan']) ?></p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-6 max-w-2xl">
            <div>
                <label for="diagnosa" class="block font-semibold text-gray-700 mb-1">Diagnosa *</label>
                <textarea id="diagnosa" name="diagnosa" required
                    class="w-full border border-gray-300 rounded px-3 py-2 resize-y"
                    rows="4"><?= $_POST['diagnosa'] ?? '' ?></textarea>
            </div>

            <div>
                <label for="tindakan" class="block font-semibold text-gray-700 mb-1">Tindakan *</label>
                <textarea id="tindakan" name="tindakan" required
                    class="w-full border border-gray-300 rounded px-3 py-2 resize-y"
                    rows="4"><?= $_POST['tindakan'] ?? '' ?></textarea>
            </div>

            <div>
                <label for="resep" class="block font-semibold text-gray-700 mb-1">Resep (Opsional)</label>
                <textarea id="resep" name="resep" class="w-full border border-gray-300 rounded px-3 py-2 resize-y"
                    rows="3"><?= $_POST['resep'] ?? '' ?></textarea>
            </div>

            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-semibold transition">
                Simpan Rekam Medis
            </button>
        </form>
    </main>
</body>

</html>