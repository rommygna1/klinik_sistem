<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'pasien') {
    header('Location: /auth/login.php');
    exit;
}
$username = $_SESSION['user']['username']; // misal ada username di session
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Pasien - Klinik </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    modify: ['Inter', 'sans-serif'],
                },
                colors: {
                    primary: '#3B82F6', // Tailwind blue-500
                }
            }
        }
    };
    </script>
</head>

<body class="bg-gray-100 flex min-h-screen font-modify">

    <?php include '../components/sidebar.php'; ?>

    <!-- Konten utama kanan -->
    <main class="flex-1 ml-64 p-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-4xl font-bold text-primary">Selamat Datang, <?= htmlspecialchars($username) ?>!</h2>
                <p class="text-gray-700 mt-2">Ini adalah halaman dashboard pasien di Klinik Sehat.</p>
            </div>
            <img src="https://png.pngtree.com/png-clipart/20211226/original/pngtree-cute-chibi-male-doctor-cartoon-png-image_6979229.png"
                alt="Patient Illustration" class="w-32 h-32 object-contain hidden md:block" />
        </div>

        <!-- Informasi Terbaru -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-2xl font-semibold text-primary flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20 10 10 0 010-20z" />
                </svg>
                Informasi Terbaru
            </h3>
            <ul class="list-disc list-inside text-gray-600 space-y-2">
                <li>🗓 Jadwal dokter tersedia setiap <strong>Senin - Jumat pukul 08.00 - 16.00</strong>.</li>
                <li>💬 Silakan lakukan <strong>konsultasi</strong> jika mengalami keluhan kesehatan.</li>
                <li>📄 Rekam medis dapat <strong>diakses & dicetak</strong> melalui menu Rekam Medis Saya.</li>
                <li>💳 Pastikan <strong>pembayaran pendaftaran</strong> dilakukan sebelum konsultasi.</li>
            </ul>
        </div>
    </main>
</body>

</html>