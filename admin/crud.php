<?php
require '../config/koneksi.php';

// Create
if (isset($_POST['create'])) {
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt->bind_param("ssss", $_POST['username'], $_POST['email'], $hash, $_POST['role']);
    $stmt->execute();
    header("Location: crud.php");
    exit;
}

// Edit
if (isset($_POST['edit'])) {
    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $_POST['username'], $_POST['email'], $_POST['role'], $_POST['id']);
    $stmt->execute();
    header("Location: crud.php");
    exit;
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: crud.php");
    exit;
}

// Fetch all data
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    function openModal(id) {
        document.getElementById('modal-' + id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
    }
    </script>
</head>

<body class="bg-white text-gray-800 font-modify">

    <?php include '../components/sidebar.php'; ?>

    <div class="ml-64 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-500">Kelola Akun Klinik</h1>
        </div>

        <!-- Form Tambah -->
        <form method="post" class="bg-white shadow-md rounded px-6 py-4 mb-6 border border-blue-200">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input name="username" placeholder="Username" required class="border rounded px-3 py-2 col-span-1" />
                <input name="email" type="email" placeholder="Email" required
                    class="border rounded px-3 py-2 col-span-1" />
                <input name="password" type="password" placeholder="Password" required
                    class="border rounded px-3 py-2 col-span-1" />
                <select name="role" required class="border rounded px-3 py-2 col-span-1">
                    <option value="">Pilih Role</option>
                    <option>admin</option>
                    <option>dokter</option>
                    <option>resepsionis</option>
                    <option>pasien</option>
                </select>
                <button name="create"
                    class="bg-blue-500 text-white rounded px-4 py-2 col-span-1 hover:bg-blue-600 transition">
                    Tambah
                </button>
            </div>
        </form>

        <!-- Tabel Akun -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">Username</th>
                        <th class="px-4 py-2 text-left">Email</th>
                        <th class="px-4 py-2 text-left">Role</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($d = $result->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-4 py-2"><?= htmlspecialchars($d['username']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($d['email']) ?></td>
                        <td class="px-4 py-2 capitalize"><?= htmlspecialchars($d['role']) ?></td>
                        <td class="px-4 py-2 space-x-2">
                            <button onclick="openModal('<?= $d['id'] ?>')"
                                class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">Edit</button>
                            <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')"
                                class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Hapus</a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div id="modal-<?= $d['id'] ?>"
                        class="hidden fixed inset-0 z-50 bg-black bg-opacity-30 flex items-center justify-center">
                        <div class="bg-white p-6 rounded-lg w-96 shadow-lg border border-gray-200">
                            <h2 class="text-xl font-semibold text-blue-500 mb-4">Edit Akun</h2>
                            <form method="post" class="space-y-3">
                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                <input name="username" value="<?= $d['username'] ?>" required
                                    class="w-full border px-3 py-2 rounded" />
                                <input name="email" type="email" value="<?= $d['email'] ?>" required
                                    class="w-full border px-3 py-2 rounded" />
                                <select name="role" class="w-full border px-3 py-2 rounded">
                                    <option <?= $d['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                    <option <?= $d['role'] === 'dokter' ? 'selected' : '' ?>>dokter</option>
                                    <option <?= $d['role'] === 'resepsionis' ? 'selected' : '' ?>>resepsionis</option>
                                    <option <?= $d['role'] === 'pasien' ? 'selected' : '' ?>>pasien</option>
                                </select>
                                <div class="flex justify-end gap-2 mt-4">
                                    <button type="button" onclick="closeModal('<?= $d['id'] ?>')"
                                        class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                                    <button name="edit"
                                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>