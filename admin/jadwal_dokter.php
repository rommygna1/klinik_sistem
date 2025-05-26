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
    <meta charset="UTF-8">
    <title>Jadwal Dokter - Klinik</title>
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
</head>

<body class="bg-gray-50">
    <?php include '../components/sidebar.php'; ?>

    <main class="ml-72 p-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-1">Jadwal Dokter</h1>
                <p class="text-gray-600">Manajemen jadwal praktik dokter</p>
            </div>
            <button onclick="openModal()" 
                    class="gradient-card px-6 py-2.5 rounded-full text-white font-semibold hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Jadwal
            </button>
        </div>

        <?php if ($errors): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">
            <?= implode('<br>', $errors) ?>
        </div>
        <?php endif; ?>

        <div class="table-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Dokter</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Hari</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Jam Praktik</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user-md text-blue-500"></i>
                                    </div>
                                    <span class="font-medium text-gray-700"><?= htmlspecialchars($row['nama_dokter']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium 
                                    <?php
                                    switch($row['hari']) {
                                        case 'Senin':
                                            echo 'bg-purple-100 text-purple-700';
                                            break;
                                        case 'Selasa':
                                            echo 'bg-blue-100 text-blue-700';
                                            break;
                                        case 'Rabu':
                                            echo 'bg-green-100 text-green-700';
                                            break;
                                        case 'Kamis':
                                            echo 'bg-yellow-100 text-yellow-700';
                                            break;
                                        case 'Jumat':
                                            echo 'bg-red-100 text-red-700';
                                            break;
                                        case 'Sabtu':
                                            echo 'bg-indigo-100 text-indigo-700';
                                            break;
                                        case 'Minggu':
                                            echo 'bg-pink-100 text-pink-700';
                                            break;
                                    }
                                    ?>">
                                    <?= $row['hari'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="far fa-clock text-gray-400 mr-2"></i>
                                    <span class="text-gray-600">
                                        <?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button onclick="openModal(<?= $row['id'] ?>, <?= $row['dokter_id'] ?>, '<?= $row['hari'] ?>', '<?= substr($row['jam_mulai'], 0, 5) ?>', '<?= substr($row['jam_selesai'], 0, 5) ?>')"
                                        class="p-2 rounded-full hover:bg-blue-100 text-blue-600 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus jadwal ini?')"
                                        class="p-2 rounded-full hover:bg-red-100 text-red-600 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form -->
        <div id="modal" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
            <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
                <div class="glass-effect rounded-xl p-6 shadow-xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 id="modalTitle" class="text-xl font-semibold text-gray-800">Tambah Jadwal</h3>
                        <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="jadwalForm" method="POST" class="space-y-4">
                        <input type="hidden" name="id" id="jadwal_id" />

                        <div class="relative">
                            <i class="fas fa-user-md absolute left-3 top-3 text-gray-400"></i>
                            <select name="dokter_id" id="dokter_id" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="">Pilih Dokter</option>
                                <?php foreach ($dokterResult as $dokter): ?>
                                <option value="<?= $dokter['id'] ?>"><?= htmlspecialchars($dokter['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-3 text-gray-400"></i>
                            <select name="hari" id="hari" required
                                class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                <option value="">Pilih Hari</option>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                                <div class="relative">
                                    <i class="fas fa-clock absolute left-3 top-3 text-gray-400"></i>
                                    <input type="time" name="jam_mulai" id="jam_mulai" required
                                        class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                                <div class="relative">
                                    <i class="fas fa-clock absolute left-3 top-3 text-gray-400"></i>
                                    <input type="time" name="jam_selesai" id="jam_selesai" required
                                        class="w-full pl-10 pr-4 py-2 bg-white/50 border border-gray-200 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all appearance-none" />
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                                Batal
                            </button>
                            <button type="submit"
                                class="gradient-card px-6 py-2 rounded-lg text-white font-medium hover:shadow-lg transition-all duration-300">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
    function openModal(id = '', dokter_id = '', hari = '', jam_mulai = '', jam_selesai = '') {
        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('modal').classList.add('flex');
        document.getElementById('jadwal_id').value = id;
        document.getElementById('dokter_id').value = dokter_id;
        document.getElementById('hari').value = hari;
        document.getElementById('jam_mulai').value = jam_mulai;
        document.getElementById('jam_selesai').value = jam_selesai;
        document.getElementById('modalTitle').innerText = id ? 'Edit Jadwal' : 'Tambah Jadwal';
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
        document.getElementById('modal').classList.remove('flex');
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('modal');
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
    </script>
</body>
</html>