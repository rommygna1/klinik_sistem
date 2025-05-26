<?php
session_start();
require '../config/koneksi.php';

// Cek role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $no_telp = trim($_POST['no_telp']);

    if (!$nama || !$alamat || !$no_telp) {
        $error = "Semua field harus diisi!";
    } else {
        $stmt = $conn->prepare("INSERT INTO pasien (nama, alamat, no_telp) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $alamat, $no_telp);

        if ($stmt->execute()) {
            $success = "Data pasien berhasil ditambahkan.";
            $nama = $alamat = $no_telp = '';
        } else {
            $error = "Gagal menyimpan data: " . $conn->error;
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
    <title>Tambah Pasien - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar kiri -->
    <aside class="w-64 bg-white shadow-md fixed inset-y-0 left-0 overflow-y-auto">
        <?php include '../components/sidebar.php'; ?>
    </aside>

    <!-- Konten utama -->
    <main class="flex-1 ml-64 p-8">
        <h1 class="text-3xl font-bold mb-6">Tambah Pasien Baru</h1>

        <?php if ($error): ?>
        <div class="bg-red-200 text-red-800 p-3 mb-4 rounded"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-blue-200 text-blue-800 p-3 mb-4 rounded"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="max-w-md bg-white p-6 rounded shadow">
            <label class="block mb-2 font-semibold" for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama ?? '') ?>" required
                class="w-full p-2 border border-gray-300 rounded mb-4" />

            <label class="block mb-2 font-semibold" for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" required
                class="w-full p-2 border border-gray-300 rounded mb-4"><?= htmlspecialchars($alamat ?? '') ?></textarea>

            <label class="block mb-2 font-semibold" for="no_telp">No. Telepon</label>
            <input type="text" id="no_telp" name="no_telp" value="<?= htmlspecialchars($no_telp ?? '') ?>" required
                class="w-full p-2 border border-gray-300 rounded mb-4" />

            <button type="submit" name="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
        </form>
    </main>

</body>

</html>