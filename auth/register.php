<?php
include '../config/koneksi.php';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    
    // Hash password sebelum disimpan
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    
    $role = 'pasien'; // role default

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<script>
            alert('Registrasi berhasil! Silakan login.');
            window.location = 'login.php';
        </script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Registrasi Pasien</title>
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
    <form method="POST"
        class="bg-white rounded-3xl shadow-2xl px-10 py-12 w-full max-w-md flex flex-col items-center">
        <h2 class="text-3xl font-semibold text-gray-800 mb-2 w-full text-left">Silahkan Registrasikan <br> Akun Pasien Anda</h2>
        <div class="w-full mt-8">
            <input name="username" type="text" placeholder="Nama Lengkap" required
                class="input-custom w-full mb-4 px-5 py-3 rounded-full bg-gray-200 border border-gray-200 text-gray-700 transition" />
            <input name="email" type="email" placeholder="Email address" required
                class="input-custom w-full mb-4 px-5 py-3 rounded-full bg-gray-200 border border-gray-200 text-gray-700 transition" />
            <input name="password" type="password" placeholder="Password" required
                class="input-custom w-full mb-5 px-5 py-3 rounded-full bg-gray-200 border border-gray-200 text-gray-700 transition" />
            <button name="register"
                class="btn-gradient w-full py-3 rounded-full text-white font-semibold tracking-wide shadow-md transition text-lg mb-9">
                REGISTER
            </button>
            <div class="text-center text-gray-500 text-sm">
                Sudah punya akun?
                <a href="login.php" class="text-sky-500 font-semibold hover:underline">Login</a>
            </div>
        </div>
    </form>
</body>

</html>