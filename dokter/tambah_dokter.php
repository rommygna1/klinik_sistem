<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'dokter') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_dokter'])) {
    $nama = trim($_POST['nama']);
    $spesialisasi = trim($_POST['spesialis']);
    $no_telepon = trim($_POST['no_telepon']);
    $email = trim($_POST['email']);
    $alamat = trim($_POST['alamat']);

    if ($nama === '' || $spesialisasi === '' || $no_telepon === '' || $email === '' || $alamat === '') {
        $error = 'Semua field harus diisi.';
    } else {
        // Simpan ke tabel dokter
        $stmt = $conn->prepare("INSERT INTO dokter (nama, spesialis, no_telepon, email, alamat) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $spesialisasi, $no_telepon, $email, $alamat);

        if ($stmt->execute()) {
            $success = "Dokter berhasil ditambahkan.";
        } else {
            $error = "Terjadi kesalahan saat menyimpan data: " . $stmt->error;
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
    <title>Dashboard Dokter - Tambah Dokter</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white flex min-h-screen font-sans font-modify">
    <?php include '../components/sidebar.php'; ?>

    <main class="flex-1 ml-64 p-8">
        <h1 class="text-4xl font-bold mb-8 text-blue-600">Tambah Dokter Baru</h1>

        <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded border border-red-300"><?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="mb-6 p-4 bg-blue-100 text-blue-700 rounded border border-blue-300">
            <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-8 rounded-lg shadow-lg max-w-lg border border-blue-200">
            <div class="mb-6">
                <label for="nama" class="block mb-2 font-semibold text-gray-800">Nama Dokter</label>
                <input id="nama" name="nama" type="text" required
                    class="w-full px-4 py-3 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="mb-6">
                <label for="spesialisasi" class="block mb-2 font-semibold text-gray-800">Spesialisasi</label>
                <input id="spesialisasi" name="spesialis" type="text" required
                    class="w-full px-4 py-3 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="mb-6">
                <label for="no_telepon" class="block mb-2 font-semibold text-gray-800">No. Telepon</label>
                <input id="no_telepon" name="no_telepon" type="tel" required
                    class="w-full px-4 py-3 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="mb-6">
                <label for="email" class="block mb-2 font-semibold text-gray-800">Email</label>
                <input id="email" name="email" type="email" required
                    class="w-full px-4 py-3 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div class="mb-8">
                <label for="alamat" class="block mb-2 font-semibold text-gray-800">Alamat</label>
                <textarea id="alamat" name="alamat" rows="4" required
                    class="w-full px-4 py-3 border border-blue-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>

            <button type="submit" name="tambah_dokter"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md transition-colors duration-200">
                Tambah Dokter
            </button>
        </form>
    </main>
</body>

</html>
