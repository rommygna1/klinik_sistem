<?php
session_start();
require '../config/koneksi.php'; // koneksi ke database

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        $error = "Email tidak valid.";
    } else {
        // Cari user berdasarkan email dari database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Verifikasi password hashed
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id'       => $user['id'],      // simpan id user
                    'email'    => $user['email'],
                    'role'     => $user['role'],
                    'username' => $user['username'],
                ];

                // Redirect sesuai role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: ../admin/index.php");
                        exit();
                    case 'dokter':
                        header("Location: ../dokter/index.php");
                        exit();
                    case 'resepsionis':
                        header("Location: ../resepsionis/index.php");
                        exit();
                    case 'pasien':
                        header("Location: ../pasien/index.php");
                        exit();
                    default:
                        $error = "Role tidak dikenali.";
                }
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Akun tidak ditemukan.";
        }
        $stmt->close();
    }
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Login Klinik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');
        body {
            font-family: 'Montserrat', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #1e90ff 0%, #00c6fb 100%);
        }
        .input-custom::placeholder {
            color: #bdbdbd;
            opacity: 1;
        }
        .input-custom:focus {
            outline: none;
            border-color: #1e90ff;
            background: #f8fafc;
        }
        .btn-gradient {
            background: linear-gradient(90deg, #1e90ff 0%, #00c6fb 100%);
        }
        .btn-gradient:hover {
            background: linear-gradient(90deg, #00c6fb 0%, #1e90ff 100%);
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center">
    <form action="" method="POST"
        class="bg-white rounded-3xl shadow-2xl px-10 py-12 w-full max-w-md flex flex-col items-center">
        <h2 class="text-3xl font-semibold text-gray-800 mb-2 w-full text-left">Selamat Datang,<br>Silahkan Anda Login</h2>
        <div class="w-full mt-8">
            <?php if ($error): ?>
            <p class="text-red-500 text-sm mb-4 text-center"><?= $error ?></p>
            <?php endif; ?>
            <input name="email" type="email" placeholder="Email address" required
                class="input-custom w-full mb-4 px-5 py-3 rounded-full bg-gray-200 border border-gray-200 text-gray-700 transition" />
            <input name="password" type="password" placeholder="Password" required
                class="input-custom w-full mb-5 px-5 py-3 rounded-full bg-gray-200 border border-gray-200 text-gray-700 transition" />
            <button type="submit" name="login"
                class="btn-gradient w-full py-3 rounded-full text-white font-semibold tracking-wide shadow-md transition text-lg mb-9">
                LOGIN
            </button>
            <div class="text-center text-gray-500 text-sm">
                Belum punya akun?
                <a href="register.php" class="text-sky-500 font-semibold hover:underline">Daftar</a>
            </div>
        </div>
    </form>
</body>

</html>