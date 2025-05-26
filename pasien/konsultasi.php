<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pasien') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

// Ambil daftar dokter dari tabel users (role = dokter)
$queryDokter = "SELECT id, username FROM users WHERE role = 'dokter' ORDER BY username";
$resultDokter = mysqli_query($conn, $queryDokter);
$dokters = [];
if ($resultDokter) {
    while ($row = mysqli_fetch_assoc($resultDokter)) {
        $dokters[] = $row;
    }
}

// Proses form submit
$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pasien_id = $_SESSION['user']['id'];
    $dokter_id = $_POST['dokter_id'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $keluhan = trim($_POST['keluhan'] ?? '');

    if (!$dokter_id) $errors[] = "Pilih dokter.";
    if (!$tanggal) $errors[] = "Isi tanggal konsultasi.";
    if (!$keluhan) $errors[] = "Tuliskan keluhan Anda.";

    if (empty($errors)) {
        $status = 'pending';
        $created_at = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO pendaftaran (pasien_id, dokter_id, tanggal, keluhan, status, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $pasien_id, $dokter_id, $tanggal, $keluhan, $status, $created_at);

        if ($stmt->execute()) {
            $success = true;
        } else {
            $errors[] = "Gagal menyimpan data konsultasi: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Form Konsultasi - Klinik Sehat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white flex min-h-screen text-gray-800 font-modify">
    <?php include '../components/sidebar.php'; ?>

    <main class="flex-1 ml-64 p-10">
        <h2 class="text-4xl font-bold mb-6 text-blue-600">Form Konsultasi</h2>

        <?php if ($success): ?>
        <div class="mb-4 p-4 bg-blue-100 text-blue-700 border border-blue-300 rounded-lg shadow">
            Konsultasi berhasil didaftarkan. Silakan tunggu konfirmasi dari dokter.
        </div>
        <?php endif; ?>

        <?php if ($errors): ?>
        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg shadow">
            <ul class="list-disc pl-5 space-y-1">
                <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form action="" method="post"
            class="bg-white p-8 rounded-2xl shadow-xl max-w-xl space-y-6 border border-gray-200">
            <div>
                <label for="dokter_id" class="block mb-2 font-semibold text-gray-700">Pilih Dokter</label>
                <select name="dokter_id" id="dokter_id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    required>
                    <option value="">-- Pilih Dokter --</option>
                    <?php foreach ($dokters as $d): ?>
                    <option value="<?= $d['id'] ?>"
                        <?= (isset($_POST['dokter_id']) && $_POST['dokter_id'] == $d['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['username']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="tanggal" class="block mb-2 font-semibold text-gray-700">Tanggal Konsultasi</label>
                <input type="date" name="tanggal" id="tanggal"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    required value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>" />
            </div>

            <div>
                <label for="keluhan" class="block mb-2 font-semibold text-gray-700">Keluhan</label>
                <textarea name="keluhan" id="keluhan" rows="4"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    required><?= htmlspecialchars($_POST['keluhan'] ?? '') ?></textarea>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="bg-blue-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-600 transition">
                    Kirim Konsultasi
                </button>
            </div>
        </form>
    </main>
</body>

</html>