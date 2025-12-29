<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warung Pak Er'Te - Nikmatnya Masakan Rumahan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .promo-card {
            background: linear-gradient(to right, #f97316, #ea580c);
        }

        .swiper-button-next,
        .swiper-button-prev {
            color: #fff;
            --swiper-navigation-size: 30px;
        }

        .swiper-pagination-bullet-active {
            background: #fff;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Header & Navbar -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="text-2xl font-bold text-orange-600">Pak Er'Te</a>
            <div class="hidden md:flex space-x-8">
                <a href="#home" class="text-gray-600 hover:text-orange-600">Home</a>
                <a href="#menu" class="text-gray-600 hover:text-orange-600">Menu</a>
                <a href="#about" class="text-gray-600 hover:text-orange-600">Kontak</a>
                @guest
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-orange-600">Login</a>
                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-orange-600">Register</a>
                @endguest
                @auth
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-orange-600">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="text-gray-600 hover:text-orange-600">
                            Logout
                        </a>
                    </form>
                @endauth
            </div>
            <button id="mobile-menu-button" class="md:hidden flex items-center">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </nav>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden px-6 pb-4">
            <a href="#home" class="block py-2 text-gray-600 hover:text-orange-600">Home</a>
            <a href="#menu" class="block py-2 text-gray-600 hover:text-orange-600">Menu</a>
            <a href="#about" class="block py-2 text-gray-600 hover:text-orange-600">Kontak</a>
            @guest
                <a href="{{ route('login') }}" class="block py-2 text-gray-600 hover:text-orange-600">Login</a>
                <a href="{{ route('register') }}" class="block py-2 text-gray-600 hover:text-orange-600">Register</a>
            @endguest
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 text-gray-600 hover:text-orange-600">Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block py-2 text-gray-600 hover:text-orange-600">
                        Logout
                    </a>
                </form>
            @endauth
        </div>
    </header>

    <main class="container mx-auto px-6 py-8">

        <!-- Home Section -->
        <section id="home" class="mb-16">
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Nikmatnya Masakan Khas Pak Er'Te</h1>
                <p class="text-lg text-gray-600">Cita rasa rumahan yang bikin kangen, harga bersahabat!</p>
            </div>
            <!-- Slider main container -->
            <div class="swiper promo-slider max-w-4xl mx-auto rounded-xl shadow-lg">
                <div class="swiper-wrapper">
                    <!-- Slide 1 -->
                    <div class="swiper-slide promo-card text-white p-8 text-center">
                        <h2 class="text-3xl font-bold mb-2">Diskon Spesial Hari Ini!</h2>
                        <p class="text-lg">Dapatkan diskon 15% untuk semua menu Ayam Bakar. Pesan sekarang jangan sampai
                            kehabisan!</p>
                    </div>
                    <!-- Slide 2 -->
                    <div class="swiper-slide promo-card text-white p-8 text-center">
                        <h2 class="text-3xl font-bold mb-2">Paket Hemat Berdua</h2>
                        <p class="text-lg">2 Porsi Pecel Lele + 2 Es Teh Manis hanya Rp 35.000!</p>
                    </div>
                    <!-- Slide 3 -->
                    <div class="swiper-slide promo-card text-white p-8 text-center">
                        <h2 class="text-3xl font-bold mb-2">Gratis Es Teh</h2>
                        <p class="text-lg">Pesan menu Nasi Goreng Spesial atau Soto Ayam dan dapatkan gratis Es Teh
                            Manis.</p>
                    </div>
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
                <!-- Add Navigation -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>

        <!-- Menu & Order Section -->
        <section id="menu" class="mb-16">
            <h2 class="text-3xl font-bold text-center mb-8">Menu Andalan Kami</h2>
            <div class="lg:flex lg:space-x-8">
                <!-- Menu Items -->
                <div id="menu-list" class="grid md:grid-cols-2 lg:grid-cols-2 gap-6 lg:w-2/3">
                    <!-- Menu items will be injected by JavaScript -->
                </div>

                <!-- Order Form -->
                <div class="lg:w-1/3 mt-8 lg:mt-0 sticky top-24">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-2xl font-bold mb-4 border-b pb-2">Pesanan Anda</h3>
                        <div id="cart-items" class="space-y-4 mb-4 max-h-60 overflow-y-auto">
                            <p id="cart-empty" class="text-gray-500">Keranjang masih kosong.</p>
                            <!-- Cart items will be injected by JavaScript -->
                        </div>
                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total:</span>
                                <span id="cart-total">Rp 0</span>
                            </div>
                            <form id="order-form">
                                <div id="address-field" class="mt-4">
                                    <label for="address" class="block font-semibold mb-2">Alamat Pengiriman:</label>
                                    <textarea id="address" name="address" rows="3" class="w-full p-2 border rounded-md"
                                        placeholder="Contoh: Jl. Pahlawan No. 123, RT 01/RW 02, Kel. Suka Maju"></textarea>
                                </div>
                                <div class="mt-4">
                                    <label for="order-notes" class="block font-semibold mb-2">Catatan Pesanan (Opsional):</label>
                                    <textarea id="order-notes" name="order_notes" rows="3" class="w-full p-2 border rounded-md"
                                        placeholder="Contoh: Tanpa bawang, pedas sedang, bungkus terpisah"></textarea>
                                </div>
                                <div class="mt-4">
                                    <label for="payment" class="block font-semibold mb-2">Metode Pembayaran:</label>
                                    <select id="payment" name="payment" class="w-full p-2 border rounded-md">
                                        <option value="COD">Bayar di Tempat (COD)</option>
                                        <option value="Tunai">Tunai di Warung</option>
                                        <option value="Transfer Dana">Transfer Dana (083169605731)</option>
                                    </select>
                                </div>
                                <button type="submit"
                                    class="w-full bg-orange-600 text-white font-bold py-3 px-4 rounded-lg mt-4 hover:bg-orange-700 transition-colors">
                                    <i class="fas fa-paper-plane mr-2"></i>Kirim Pesanan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="mb-16 bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-center mb-8">Hubungi & Kunjungi Kami</h2>
            <div class="md:flex md:space-x-8 items-center">
                <div class="md:w-1/2 mb-6 md:mb-0">
                    <h3 class="text-xl font-semibold mb-2">Alamat Warung:</h3>
                    <p class="text-gray-600 mb-4">samping alfamart, Jl. Raya Negeri Sakti, Negeri Sakti, Kec. Gedong
                        Tataan, Kabupaten Pesawaran, Lampung 35366</p>
                    <h3 class="text-xl font-semibold mb-2">Jam Buka:</h3>
                    <p class="text-gray-600">Setiap Hari, 09:00 - 22:00 WIB</p>
                </div>
                <div class="md:w-1/2 text-center">
                    <a href="https://wa.me/6283169605731?text=Halo%20Pak%20Er'Te,%20saya%20mau%20pesan" target="_blank"
                        class="inline-block bg-green-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-600 transition-colors mb-4">
                        <i class="fab fa-whatsapp mr-2"></i>Pesan via WhatsApp
                    </a>
                    <a href="https://maps.app.goo.gl/RokQ1CX7Vy61SfRZA" target="_blank"
                        class="inline-block bg-blue-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-map-marker-alt mr-2"></i>Lihat di Google Maps
                    </a>
                </div>
            </div>
        </section>

        <!-- Sales Report Section -->
        <!-- <section id="laporan" class="bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-center mb-4">Laporan Penjualan</h2>
            <div class="flex justify-center space-x-2 mb-6">
                <button data-filter="daily"
                    class="filter-btn bg-orange-600 text-white py-2 px-4 rounded-lg">Harian</button>
                <button data-filter="weekly"
                    class="filter-btn bg-gray-200 text-gray-700 py-2 px-4 rounded-lg">Mingguan</button>
                <button data-filter="monthly"
                    class="filter-btn bg-gray-200 text-gray-700 py-2 px-4 rounded-lg">Bulanan</button>
            </div>
            <div class="text-center">
                <h3 class="text-xl font-semibold mb-2">Laporan <span id="report-period">Harian</span></h3>
                <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p class="text-gray-600">Total Pesanan</p>
                        <p id="total-orders" class="text-2xl font-bold">0</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <p class="text-gray-600">Total Penjualan</p>
                        <p id="total-sales" class="text-2xl font-bold">Rp 0</p>
                    </div>
                </div>
            </div>
        </section> -->

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="container mx-auto px-6 py-4 text-center">
            <p>&copy; 2025 Warung Pak Er'Te. All Rights Reserved.</p>
        </div>
    </footer>



    <script>
        const menuData = @json($menus);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
</body>

</html>