<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistem Informasi RomCare Clinic</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
    body {
      font-family: 'Poppins', sans-serif;
    }
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body class="bg-white text-gray-800" id="home">

  <!-- Navbar -->
  <header class="bg-white shadow-md fixed top-0 left-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-blue-600">RomCare Clinic</h1>
      </div>
      <nav class="hidden md:flex space-x-6 text-gray-700 font-semibold">
        <a href="#home" class="hover:text-blue-600 transition">Home</a>
        <a href="#features" class="hover:text-blue-600 transition">Features</a>
        <a href="#tentang" class="hover:text-blue-600 transition">About</a>
        <a href="#kontak" class="hover:text-blue-600 transition">Contact</a>
      </nav>
      <a href="auth/login.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">
        Login
      </a>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="pt-24 min-h-screen flex items-center bg-white">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">
      <div>
        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-blue-700 leading-tight">
          Pelayanan Kesehatan <br/> Terpercaya Hanya di <br/> RomCare Clinic
        </h2>
        <p class="text-lg mb-6 text-gray-700">
          Dapatkan kemudahan dengan pelayanan pendaftaran online, konsultasi dokter, serta rekam medis yang terintegrasi dalam satu platform.
          Daftar sekarang, lakukan konsultasi, dan pantau kondisi kesehatan Anda dengan lebih mudah, cepat, dan aman.
        </p>
        <a href="auth/login.php"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow transition">
          Konsultasi Sekarang
        </a>
      </div>
      <div>
        <img src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fGhvc3BpdGFsfGVufDB8fDB8fHww" alt="Klinik"
          class="rounded-xl shadow-lg" />
      </div>
    </div>
  </section>

  <!-- Fitur Section -->
  <section class="py-20 bg-white" id="features">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h3 class="text-3xl font-bold mb-8 text-blue-700">Fitur Unggulan</h3>
      <div class="grid md:grid-cols-3 gap-10">
        <div class="bg-blue-50 p-6 rounded-lg shadow hover:shadow-lg transition">
          <img src="https://cdn-icons-png.flaticon.com/512/3039/3039432.png" class="w-16 mx-auto mb-4" alt="Daftar" />
          <h4 class="text-xl font-semibold mb-2">Pendaftaran Online</h4>
          <p class="text-gray-600">Pasien dapat mendaftar dari rumah tanpa antri lama.</p>
        </div>
        <div class="bg-blue-50 p-6 rounded-lg shadow hover:shadow-lg transition">
          <img src="https://cdn-icons-png.flaticon.com/512/2920/2920090.png" class="w-16 mx-auto mb-4" alt="Konsultasi" />
          <h4 class="text-xl font-semibold mb-2">Konsultasi Langsung</h4>
          <p class="text-gray-600">Langsung konsultasi dengan dokter dari aplikasi web.</p>
        </div>
        <div class="bg-blue-50 p-6 rounded-lg shadow hover:shadow-lg transition">
          <img src="https://cdn-icons-png.flaticon.com/512/4290/4290854.png" class="w-16 mx-auto mb-4" alt="Rekam Medis" />
          <h4 class="text-xl font-semibold mb-2">Rekam Medis Digital</h4>
          <p class="text-gray-600">Pantau riwayat kesehatan Anda secara lengkap dan aman.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Tentang Section -->
  <section class="py-20 bg-white" id="tentang">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">
      <div>
        <img src="https://plus.unsplash.com/premium_photo-1675686363477-c28d5bf65adb?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NTl8fGhvc3BpdGFsfGVufDB8fDB8fHww"
          alt="Tentang" class="rounded-lg shadow-md" />
      </div>
      <div>
        <h3 class="text-3xl font-bold text-blue-700 mb-4">Tentang Klinik Kami</h3>
        <div class="text-gray-700 leading-relaxed space-y-4">
          <p>RomCare Clinic merupakan fasilitas kesehatan modern yang mengedepankan pelayanan medis terbaik melalui teknologi terkini dan sistem informasi terintegrasi demi memastikan layanan yang efisien, akurat, dan nyaman bagi setiap pasien.</p>
          <p>Visi kami adalah menjadi penyedia layanan kesehatan terdepan yang mengutamakan kualitas, keamanan, dan kepuasan pasien dalam setiap aspek pelayanan kami.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Kontak Section -->
  <section class="py-20 bg-white-50" id="kontak">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h3 class="text-3xl font-bold text-blue-700 mb-8">Hubungi Kami</h3>
      <div class="grid md:grid-cols-3 gap-10">
        <div class="bg-blue-50 p-6 rounded-lg shadow hover:shadow-lg transition">
          <img src="https://cdn-icons-png.flaticon.com/512/684/684908.png" class="w-16 mx-auto mb-4" alt="Alamat" />
          <h4 class="text-xl font-semibold mb-2">Alamat</h4>
          <p class="text-gray-600">Jl. Almamater No.123, Medan, Sumut</p>
        </div>
        <div class="bg-blue-50 p-6 rounded-lg shadow hover:shadow-lg transition">
          <img src="https://cdn-icons-png.flaticon.com/512/724/724664.png" class="w-16 mx-auto mb-4" alt="Telepon" />
          <h4 class="text-xl font-semibold mb-2">Telepon</h4>
          <p class="text-gray-600">+62 895-6122-7986</p>
        </div>
        <div class="bg-blue-50 p-6 rounded-lg shadow hover:shadow-lg transition">
          <img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" class="w-16 mx-auto mb-4" alt="Email" />
          <h4 class="text-xl font-semibold mb-2">Email</h4>
          <p class="text-gray-600">info@romcareclinic.com</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-blue-700 text-white py-6 mt-10">
    <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center">
      <p class="text-sm">© 2025 RomCare Clinic. All rights reserved.</p>
      <div class="space-x-4 mt-4 md:mt-0">
        <a href="#home" class="hover:underline">Home</a>
        <a href="#features" class="hover:underline">Fitur</a>
        <a href="#tentang" class="hover:underline">Tentang</a>
        <a href="#kontak" class="hover:underline">Kontak</a>
        <a href="auth/login.php" class="hover:underline">Login</a>
      </div>
    </div>
  </footer>

</body>
</html>