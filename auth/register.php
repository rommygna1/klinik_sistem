<?php include '../config/koneksi.php'; ?>

<?php 
if (isset($_POST['register'])) { 
    $username = $_POST['username']; 
    $email = $_POST['email'];
    $password = $_POST['password']; // tanpa hash, langsung simpan plain password
    $role = 'pasien'; // role default untuk register

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
</head>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

.font-modify {
    font-family: 'Poppins', sans-serif;
}
</style>

<body class="bg-green-50 flex items-center justify-center min-h-screen font-modify">
    <form method="POST" class="bg-white p-8 rounded-xl shadow-lg w-full max-w-sm border border-green-200">
        <h2 class="text-2xl font-bold mb-4 text-center text-green-700">Registrasi Pasien</h2>

        <input name="username" type="text" placeholder="Nama Lengkap" required
            class="w-full mb-4 p-3 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />

        <input name="email" type="email" placeholder="Email" required
            class="w-full mb-4 p-3 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />

        <input name="password" type="password" placeholder="Password" required
            class="w-full mb-4 p-3 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" />

        <button name="register"
            class="bg-green-500 hover:bg-green-600 w-full text-white p-3 rounded-lg transition shadow">
            Register
        </button>

        <p class="text-center mt-4 text-sm">
            <a class="text-green-600 hover:underline" href="login.php">Sudah punya akun? Login</a>
        </p>
    </form>
</body>

</html>