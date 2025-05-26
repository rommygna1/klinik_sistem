<?php
session_start();
include_once '../config/koneksi.php';

// Cek role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Ambil list pasien dari tabel users
$pasienQuery = mysqli_query($conn, "SELECT id, username FROM users WHERE role='pasien' ORDER BY username ASC");

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pasien_id = $_POST['pasien_id'] ?? '';
    $nominal = $_POST['nominal'] ?? '';

    if (!$pasien_id) $errors[] = 'Pasien harus dipilih.';
    if (!$nominal || !is_numeric($nominal) || $nominal <= 0) $errors[] = 'Nominal harus diisi dengan angka lebih dari 0.';

    if (!$errors) {
        $pasien_id = mysqli_real_escape_string($conn, $pasien_id);
        $nominal = mysqli_real_escape_string($conn, $nominal);

        $insert = mysqli_query($conn, "INSERT INTO tagihan_pembayaran (pasien_id, nominal) VALUES ('$pasien_id', '$nominal')");

        if ($insert) {
            $success = 'Tagihan berhasil ditambahkan.';
        } else {
            $errors[] = 'Gagal menambahkan tagihan: ' . mysqli_error($conn);
        }
    }
}
?>

<body class="bg-white flex min-h-screen font-modify">

    <?php include '../components/sidebar.php'; ?>

    <main class="flex-1 ml-64 p-8 bg-white min-h-screen font-modify">
        <h1 class="text-3xl font-bold mb-6 text-blue-600">Input Tagihan Pembayaran</h1>

        <?php if ($errors): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 border border-red-300">
            <?= implode('<br>', $errors) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-blue-100 text-blue-700 p-3 rounded mb-4 border border-blue-300">
            <?= $success ?>
        </div>
        <?php endif; ?>

        <div class="max-w-xl bg-white border border-blue-400 p-6 rounded-xl shadow space-y-6">
            <form method="POST" class="space-y-5">
                <div>
                    <label for="pasien_id" class="block font-semibold mb-2 text-blue-700">Pasien</label>
                    <select id="pasien_id" name="pasien_id" required
                        class="w-full border border-blue-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Pasien --</option>
                        <?php while ($row = mysqli_fetch_assoc($pasienQuery)): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['username']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label for="nominal" class="block font-semibold mb-2 text-blue-700">Nominal Tagihan (Rp)</label>
                    <input type="number" id="nominal" name="nominal" min="1" step="0.01" required
                        class="w-full border border-blue-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                        placeholder="Masukkan nominal tagihan" />
                </div>

                <div class="text-right">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-semibold shadow">
                        Tambah Tagihan
                    </button>
                </div>
            </form>
        </div>
    </main>


    <style>
    body {
        display: flex;
        min-height: 100vh;
        background-color: #f3f4f6;
    }
    </style>