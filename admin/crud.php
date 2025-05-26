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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8fafc;
        }
        .gradient-card {
            background: linear-gradient(135deg, #1e90ff 0%, #00c6fb 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
    <script>
    function openModal(id) {
        document.getElementById('modal-' + id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
    }
    </script>
</head>

<body class="bg-gray-50">
    <?php include '../components/sidebar.php'; ?>

    <main class="ml-72 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-1">Kelola Akun Klinik</h1>
                <p class="text-gray-600">Manajemen data pengguna sistem</p>
            </div>
            <button onclick="document.getElementById('addUserForm').classList.toggle('hidden')" 
                    class="gradient-card px-6 py-2.5 rounded-full text-white font-semibold hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Pengguna
            </button>
        </div>

        <!-- Form Tambah (Hidden by default) -->
        <form id="addUserForm" method="post" class="hidden mb-8">
            <div class="glass-effect p-6 rounded-2xl">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Pengguna Baru</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="relative">
                        <i class="fas fa-user absolute left-3 top-3 text-gray-400"></i>
                        <input name="username" placeholder="Username" required 
                               class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" />
                    </div>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
                        <input name="email" type="email" placeholder="Email" required
                               class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" />
                    </div>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-3 text-gray-400"></i>
                        <input name="password" type="password" placeholder="Password" required
                               class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" />
                    </div>
                    <div class="relative">
                        <i class="fas fa-user-tag absolute left-3 top-3 text-gray-400"></i>
                        <select name="role" required 
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none">
                            <option value="">Pilih Role</option>
                            <option>admin</option>
                            <option>dokter</option>
                            <option>resepsionis</option>
                            <option>pasien</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="document.getElementById('addUserForm').classList.add('hidden')"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium mr-2">
                        Batal
                    </button>
                    <button name="create"
                            class="gradient-card px-6 py-2 rounded-lg text-white font-medium hover:shadow-lg transition-all duration-300">
                        Simpan
                    </button>
                </div>
            </div>
        </form>

        <!-- Tabel Akun -->
        <div class="table-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Username</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Role</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>                <tbody>
                    <?php while ($d = $result->fetch_assoc()): ?>
                    <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-500"></i>
                                </div>
                                <span class="font-medium text-gray-700"><?= htmlspecialchars($d['username']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($d['email']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                <?php
                                switch($d['role']) {
                                    case 'admin':
                                        echo 'bg-purple-100 text-purple-700';
                                        break;
                                    case 'dokter':
                                        echo 'bg-green-100 text-green-700';
                                        break;
                                    case 'resepsionis':
                                        echo 'bg-blue-100 text-blue-700';
                                        break;
                                    default:
                                        echo 'bg-gray-100 text-gray-700';
                                }
                                ?>">
                                <?= htmlspecialchars($d['role']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button onclick="openModal('<?= $d['id'] ?>')"
                                    class="p-2 rounded-full hover:bg-blue-100 text-blue-600 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')"
                                    class="p-2 rounded-full hover:bg-red-100 text-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div id="modal-<?= $d['id'] ?>"
                        class="hidden fixed inset-0 z-50 bg-black/30 backdrop-blur-sm flex items-center justify-center">
                        <div class="glass-effect p-8 rounded-2xl w-[500px] shadow-xl">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl font-semibold text-gray-800">Edit Pengguna</h2>
                                <button onclick="closeModal('<?= $d['id'] ?>')"
                                    class="p-2 hover:bg-gray-100 rounded-full">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form method="post" class="space-y-4">
                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                <div class="relative">
                                    <i class="fas fa-user absolute left-3 top-3 text-gray-400"></i>
                                    <input name="username" value="<?= $d['username'] ?>" required
                                        class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" />
                                </div>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-3 top-3 text-gray-400"></i>
                                    <input name="email" type="email" value="<?= $d['email'] ?>" required
                                        class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all" />
                                </div>
                                <div class="relative">
                                    <i class="fas fa-user-tag absolute left-3 top-3 text-gray-400"></i>
                                    <select name="role" 
                                        class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none">
                                        <option <?= $d['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                        <option <?= $d['role'] === 'dokter' ? 'selected' : '' ?>>dokter</option>
                                        <option <?= $d['role'] === 'resepsionis' ? 'selected' : '' ?>>resepsionis</option>
                                        <option <?= $d['role'] === 'pasien' ? 'selected' : '' ?>>pasien</option>
                                    </select>
                                </div>
                                <div class="flex justify-end gap-3 mt-6">
                                    <button type="button" onclick="closeModal('<?= $d['id'] ?>')"
                                        class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                                        Batal
                                    </button>
                                    <button name="edit"
                                        class="gradient-card px-6 py-2 rounded-lg text-white font-medium hover:shadow-lg transition-all duration-300">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>