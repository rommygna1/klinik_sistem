<?php
session_start();
include_once '../config/koneksi.php';

// Pastikan user sudah login dan role-nya dokter atau admin
if (!isset($_SESSION['user'])) {
    header("Location: /auth/login.php");
    exit;
}

$role = $_SESSION['user']['role'];
if (!in_array($role, ['admin', 'dokter'])) {
    die("Anda tidak punya akses ke halaman ini.");
}

// Ambil semua dokter (untuk dropdown)
$dokterResult = $conn->query("SELECT id, username FROM users WHERE role='dokter'");

// Handle form tambah/edit
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $dokter_id = $_POST['dokter_id'] ?? null;
    $hari = $_POST['hari'] ?? null;
    $jam_mulai = $_POST['jam_mulai'] ?? null;
    $jam_selesai = $_POST['jam_selesai'] ?? null;

    if (!$dokter_id || !$hari || !$jam_mulai || !$jam_selesai) {
        $errors[] = "Semua field wajib diisi.";
    }

    if (!$errors) {
        if ($id) {
            // Update
            $stmt = $conn->prepare("UPDATE jadwal_dokter SET dokter_id=?, hari=?, jam_mulai=?, jam_selesai=? WHERE id=?");
            $stmt->bind_param("isssi", $dokter_id, $hari, $jam_mulai, $jam_selesai, $id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Insert
            $stmt = $conn->prepare("INSERT INTO jadwal_dokter (dokter_id, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $dokter_id, $hari, $jam_mulai, $jam_selesai);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: jadwal_dokter.php");
        exit;
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $idDelete = intval($_GET['delete']);
    $conn->query("DELETE FROM jadwal_dokter WHERE id = $idDelete");
    header("Location: jadwal_dokter.php");
    exit;
}

// Ambil data jadwal dokter
$result = $conn->query("SELECT jd.*, u.username AS nama_dokter FROM jadwal_dokter jd JOIN users u ON jd.dokter_id = u.id ORDER BY FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), jam_mulai");

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Jadwal Dokter - Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen bg-white font-modify">

    <!-- Sidebar -->
    <?php include_once '../components/sidebar.php'; ?>

    <!-- Konten utama -->
    <main class="ml-64 p-6">
        <h1 class="text-3xl font-bold text-blue-600 mb-6">Jadwal Dokter</h1>

        <?php if ($errors): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            <?= implode('<br>', $errors) ?>
        </div>
        <?php endif; ?>

        <!-- Button tambah jadwal -->
        <button onclick="openModal()"
            class="mb-6 bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition">
            + Tambah Jadwal
        </button>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-blue-100 text-blue-700 text-sm uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-3">Dokter</th>
                        <th class="px-6 py-3">Hari</th>
                        <th class="px-6 py-3">Jam Mulai</th>
                        <th class="px-6 py-3">Jam Selesai</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-3"><?= htmlspecialchars($row['nama_dokter']) ?></td>
                        <td class="px-6 py-3"><?= $row['hari'] ?></td>
                        <td class="px-6 py-3"><?= substr($row['jam_mulai'], 0, 5) ?></td>
                        <td class="px-6 py-3"><?= substr($row['jam_selesai'], 0, 5) ?></td>
                        <td class="px-6 py-3 flex gap-3">
                            <button
                                onclick="openModal(<?= $row['id'] ?>, <?= $row['dokter_id'] ?>, '<?= $row['hari'] ?>', '<?= substr($row['jam_mulai'], 0, 5) ?>', '<?= substr($row['jam_selesai'], 0, 5) ?>')"
                                class="text-blue-600 hover:underline transition">Edit</button>
                            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus jadwal ini?')"
                                class="text-red-600 hover:underline transition">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal Form -->
        <div id="modal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h2 id="modalTitle" class="text-2xl font-semibold text-blue-600 mb-4">Tambah Jadwal</h2>
                <form id="jadwalForm" method="POST" action="">
                    <input type="hidden" name="id" id="jadwal_id" />

                    <div class="mb-3">
                        <label for="dokter_id" class="block text-gray-700 mb-1">Dokter:</label>
                        <select name="dokter_id" id="dokter_id" class="border border-gray-300 rounded px-3 py-2 w-full"
                            required>
                            <option value="">-- Pilih Dokter --</option>
                            <?php foreach ($dokterResult as $dokter): ?>
                            <option value="<?= $dokter['id'] ?>"><?= htmlspecialchars($dokter['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="hari" class="block text-gray-700 mb-1">Hari:</label>
                        <select name="hari" id="hari" class="border border-gray-300 rounded px-3 py-2 w-full" required>
                            <option value="">-- Pilih Hari --</option>
                            <?php
                        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                        foreach ($hariOptions as $hariOption): ?>
                            <option value="<?= $hariOption ?>"><?= $hariOption ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jam_mulai" class="block text-gray-700 mb-1">Jam Mulai:</label>
                        <input type="time" name="jam_mulai" id="jam_mulai"
                            class="border border-gray-300 rounded px-3 py-2 w-full" required />
                    </div>

                    <div class="mb-4">
                        <label for="jam_selesai" class="block text-gray-700 mb-1">Jam Selesai:</label>
                        <input type="time" name="jam_selesai" id="jam_selesai"
                            class="border border-gray-300 rounded px-3 py-2 w-full" required />
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal()"
                            class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>


    <script>
    function openModal(id = '', dokter_id = '', hari = '', jam_mulai = '', jam_selesai = '') {
        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('jadwal_id').value = id;
        document.getElementById('dokter_id').value = dokter_id;
        document.getElementById('hari').value = hari;
        document.getElementById('jam_mulai').value = jam_mulai;
        document.getElementById('jam_selesai').value = jam_selesai;
        document.getElementById('modalTitle').innerText = id ? 'Edit Jadwal' : 'Tambah Jadwal';
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
    </script>

</body>

</html>