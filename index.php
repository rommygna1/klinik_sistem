<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistem Informasi RomCare Clinic</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
    body {
      font-family: 'Poppins', sans-serif;
    }
    html {
      scroll-behavior: smooth;
    }
    .gradient-bg {
      background: linear-gradient(135deg, #1e90ff 0%, #00c6fb 100%);
    }
    .glass-card {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .feature-card {
      transition: all 0.3s ease;
    }
    .feature-card:hover {
      transform: translateY(-5px);
    }
    .gradient-text {
      background: linear-gradient(135deg, #1e90ff 0%, #00c6fb 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    /* Smooth scroll offset for fixed navbar */
    #features::before,
    #tentang::before {
      content: '';
      display: block;
      height: 120px;
      margin-top: -120px;
      visibility: hidden;
    }
  </style>
</head>
<body class="bg-gray-50" id="home">

  <!-- Navbar -->
  <header class="bg-white/80 backdrop-blur-md shadow-md fixed top-0 left-0 w-full z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <div class="flex items-center space-x-2">
        <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold gradient-text">RomCare Clinic</h1>
      </div>
      <nav class="hidden md:flex space-x-8 text-gray-700 font-semibold">
        <a href="#home" class="hover:text-blue-600 transition">HOME</a>
        <a href="#features" class="hover:text-blue-600 transition">FEATURES</a>
        <a href="#tentang" class="hover:text-blue-600 transition">ABOUT</a>
        <a href="#kontak" class="hover:text-blue-600 transition">CONTACT</a>
      </nav>
      <a href="auth/login.php" class="gradient-bg text-white px-6 py-2.5 rounded-xl font-medium hover:shadow-lg transition duration-300">
        Login
      </a>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="pt-20 min-h-screen flex items-center bg-gradient-to-br from-blue-50 to-white">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
      <div class="space-y-8">
        <h2 class="text-4xl md:text-5xl font-bold leading-tight">
          <span class="gradient-text">Pelayanan Kesehatan</span><br/>
          <span class="gradient-text">Terpercaya Hanya di<br/>
          <span class="gradient-text">RomCare Clinic</span>
        </h2>
        <p class="text-lg text-gray-600 leading-relaxed">
          Dapatkan kemudahan dengan pelayanan pendaftaran online, konsultasi dokter, serta rekam medis yang terintegrasi dalam satu platform.
          Daftar sekarang, lakukan konsultasi, dan pantau kondisi kesehatan Anda dengan lebih mudah, cepat, dan aman.
        </p>
        <div class="flex gap-4">
          <a href="auth/login.php" class="gradient-bg text-white px-8 py-4 rounded-xl font-medium hover:shadow-lg transition duration-300 inline-flex items-center gap-2">
            Konsultasi Sekarang
            <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>
      <div class="relative">
        <div class="absolute inset-0 gradient-bg opacity-10 rounded-3xl transform rotate-6"></div>
        <img src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fGhvc3BpdGFsfGVufDB8fDB8fHww" alt="Klinik"
          class="rounded-3xl shadow-xl relative z-10" />
      </div>
    </div>
  </section>

  <!-- Fitur Section -->
  <section class="py-20 bg-white min-h-screen flex items-center" id="features">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h3 class="text-3xl font-bold mb-4 gradient-text">Fitur Unggulan</h3>
      <p class="text-gray-600 mb-12 max-w-2xl mx-auto">Nikmati berbagai fitur modern yang kami sediakan untuk kenyamanan dan kemudahan Anda</p>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="feature-card glass-card p-8 rounded-2xl shadow-lg hover:shadow-xl">
          <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-user-plus text-3xl text-white"></i>
          </div>
          <h4 class="text-xl font-semibold mb-3">Pendaftaran Online</h4>
          <p class="text-gray-600">Pasien dapat mendaftar dari rumah tanpa antri lama.</p>
        </div>
        <div class="feature-card glass-card p-8 rounded-2xl shadow-lg hover:shadow-xl">
          <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-stethoscope text-3xl text-white"></i>
          </div>
          <h4 class="text-xl font-semibold mb-3">Konsultasi Langsung</h4>
          <p class="text-gray-600">Langsung konsultasi dengan dokter dari aplikasi web.</p>
        </div>
        <div class="feature-card glass-card p-8 rounded-2xl shadow-lg hover:shadow-xl">
          <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-notes-medical text-3xl text-white"></i>
          </div>
          <h4 class="text-xl font-semibold mb-3">Rekam Medis Digital</h4>
          <p class="text-gray-600">Pantau riwayat kesehatan Anda secara lengkap dan aman.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Tentang Section -->
  <section class="py-20 bg-gradient-to-br from-blue-50 to-white min-h-screen flex items-center" id="tentang">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
      <div class="relative">
        <div class="absolute inset-0 gradient-bg opacity-10 rounded-3xl transform -rotate-6"></div>
        <img src="https://plus.unsplash.com/premium_photo-1675686363477-c28d5bf65adb?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NTl8fGhvc3BpdGFsfGVufDB8fDB8fHww"
          alt="Tentang" class="rounded-3xl shadow-xl relative z-10" />
      </div>
      <div class="space-y-6">
        <h3 class="text-3xl font-bold gradient-text mb-4">Tentang Klinik Kami</h3>
        <div class="text-gray-600 leading-relaxed space-y-4">
          <p>RomCare Clinic merupakan fasilitas kesehatan modern yang mengedepankan pelayanan medis terbaik melalui teknologi terkini dan sistem informasi terintegrasi demi memastikan layanan yang efisien, akurat, dan nyaman bagi setiap pasien.</p>
          <p>Visi kami adalah menjadi penyedia layanan kesehatan terdepan yang mengutamakan kualitas, keamanan, dan kepuasan pasien dalam setiap aspek pelayanan kami.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Kontak Section -->
  <section class="py-20 bg-white" id="kontak">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h3 class="text-3xl font-bold gradient-text mb-4">Hubungi Kami</h3>
      <p class="text-gray-600 mb-12 max-w-2xl mx-auto">Jangan ragu untuk menghubungi kami jika Anda memiliki pertanyaan</p>
      <div class="grid md:grid-cols-3 gap-8">
        <div class="feature-card glass-card p-8 rounded-2xl shadow-lg hover:shadow-xl">
          <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-map-marker-alt text-2xl text-white"></i>
          </div>
          <h4 class="text-xl font-semibold mb-3">Alamat</h4>
          <p class="text-gray-600">Jl. Almamater No.123, Medan, Sumut</p>
        </div>
        <div class="feature-card glass-card p-8 rounded-2xl shadow-lg hover:shadow-xl">
          <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-phone-alt text-2xl text-white"></i>
          </div>
          <h4 class="text-xl font-semibold mb-3">Telepon</h4>
          <p class="text-gray-600">+62 895-6122-7986</p>
        </div>
        <div class="feature-card glass-card p-8 rounded-2xl shadow-lg hover:shadow-xl">
          <div class="w-16 h-16 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-envelope text-2xl text-white"></i>
          </div>
          <h4 class="text-xl font-semibold mb-3">Email</h4>
          <p class="text-gray-600">info@romcareclinic.com</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="gradient-bg text-white py-8">
    <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center">
      <p class="text-sm mb-4 md:mb-0">© 2025 RomCare Clinic. All rights reserved.</p>
      <div class="flex gap-6">
        <a href="#home" class="hover:text-blue-100 transition">Home</a>
        <a href="#features" class="hover:text-blue-100 transition">Fitur</a>
        <a href="#tentang" class="hover:text-blue-100 transition">Tentang</a>
        <a href="#kontak" class="hover:text-blue-100 transition">Kontak</a>
        <a href="auth/login.php" class="hover:text-blue-100 transition">Login</a>
      </div>
    </div>
  </footer>

</body>
</html>