<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'resepsionis') {
    header('Location: /auth/login.php');
    exit;
}

include_once '../config/koneksi.php';

// Ambil data pasien dan dokter dari tabel users
$pasien = mysqli_query($conn, "SELECT * FROM users WHERE role='pasien'");
$dokter = mysqli_query($conn, "SELECT * FROM users WHERE role='dokter'");

if (isset($_POST['submit'])) {
    $pasien_id = $_POST['pasien_id'];
    $dokter_id = $_POST['dokter_id'];
    $tanggal = $_POST['tanggal'];
    $keluhan = $_POST['keluhan'];

    // Simpan ke database
    $insert = mysqli_query($conn, "INSERT INTO pendaftaran (pasien_id, dokter_id, tanggal, keluhan, status) VALUES ('$pasien_id', '$dokter_id', '$tanggal', '$keluhan', 'menunggu')");

    if ($insert) {
        $last_id = mysqli_insert_id($conn);
        header("Location: cetak_kartu.php?id=$last_id");
        exit;
    } else {
        $error = "Gagal menyimpan pendaftaran.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Pendaftaran Offline</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-50 min-h-screen font-modify">
    <?php include '../components/sidebar.php'; ?>
    <div class="p-6 ml-64">
        <div class="max-w-xl mx-auto bg-white shadow-md rounded-xl p-6 border border-blue-200">
            <h2 class="text-2xl font-bold text-blue-700 mb-4 text-center">Pendaftaran Offline Pasien</h2>

            <?php if (isset($error)): ?>
            <p class="text-red-600 text-center mb-4"><?= $error ?></p>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-blue-700 mb-1">Pasien</label>
                    <select name="pasien_id" required class="w-full p-2 border rounded-lg border-blue-300">
                        <option value="">-- Pilih Pasien --</option>
                        <?php while ($row = mysqli_fetch_assoc($pasien)): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['username'] ?> (<?= $row['email'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-blue-700 mb-1">Dokter</label>
                    <select name="dokter_id" required class="w-full p-2 border rounded-lg border-blue-300">
                        <option value="">-- Pilih Dokter --</option>
                        <?php while ($row = mysqli_fetch_assoc($dokter)): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['username'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-blue-700 mb-1">Tanggal Kunjungan</label>
                    <input type="date" name="tanggal" required class="w-full p-2 border rounded-lg border-blue-300" />
                </div>

                <div>
                    <label class="block text-blue-700 mb-1">Keluhan</label>
                    <textarea name="keluhan" rows="3" required
                        class="w-full p-2 border rounded-lg border-blue-300"></textarea>
                </div>

                <button type="submit" name="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white w-full py-2 rounded-lg">
                    Simpan & Cetak Kartu
                </button>
            </form>
        </div>
</body>

</html>