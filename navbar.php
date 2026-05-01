<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Navbar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      
      <!-- Logo -->
      <div class="text-2xl font-bold text-indigo-600 tracking-tight">
        Kost Ibu Siti
      </div>

      <!-- Menu Desktop -->
      <div class="hidden md:flex items-center space-x-8">
        <a href="index.php" class="text-gray-600 hover:text-indigo-600 transition duration-200 font-medium">Beranda</a>
        <a href="kamar.php" class="text-gray-600 hover:text-indigo-600 transition duration-200 font-medium">Kamar</a>
        <a href="fasilitas.php" class="text-gray-600 hover:text-indigo-600 transition duration-200 font-medium">Fasilitas</a>
      </div>

      <!-- CTA Desktop -->
      <div class="hidden md:block">
        <a href="booking.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 shadow-sm">
          Booking Sekarang
        </a>
      </div>

      <!-- Hamburger Icon -->
      <div class="md:hidden flex items-center">
        <button id="menu-btn" class="focus:outline-none">
          <svg id="menu-icon" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor"
               viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path id="menu-open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16" />
            <path id="menu-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden md:hidden px-4 pb-4 space-y-3 transition-all duration-300">
    <a href="index.php" class="block text-gray-700 hover:text-indigo-600 font-medium">Beranda</a>
    <a href="kamar.php" class="block text-gray-700 hover:text-indigo-600 font-medium">Kamar</a>
    <a href="fasilitas.php" class="block text-gray-700 hover:text-indigo-600 font-medium">Fasilitas</a>
    <a href="booking.php" class="block text-center bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
      Booking Sekarang
    </a>
  </div>

  <!-- Script Toggle Menu & Icon -->
  <script>
    const btn = document.getElementById('menu-btn');
    const menu = document.getElementById('mobile-menu');
    const openIcon = document.getElementById('menu-open');
    const closeIcon = document.getElementById('menu-close');

    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden');
      openIcon.classList.toggle('hidden');
      closeIcon.classList.toggle('hidden');
    });
  </script>
</nav>
