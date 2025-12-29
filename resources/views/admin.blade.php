<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Warung Pak Er'Te</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { background-color: #fb923c; color: white; }
        .sidebar-link:hover { background-color: #fdba74; }
    </style>
</head>
<body class="bg-gray-100">

    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <aside class="w-64 bg-orange-500 text-white flex-shrink-0">
            <div class="p-6 text-2xl font-bold border-b border-orange-400">
                <a href="#">Admin Panel</a>
            </div>
            <nav class="mt-6">
                <a href="#" data-target="dashboard" class="sidebar-link active flex items-center py-3 px-6 transition duration-200">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <a href="#" data-target="menu" class="sidebar-link flex items-center py-3 px-6 transition duration-200">
                    <i class="fas fa-utensils mr-3"></i> Menu
                </a>
                <a href="#" data-target="slider" class="sidebar-link flex items-center py-3 px-6 transition duration-200">
                    <i class="fas fa-images mr-3"></i> Slider
                </a>
                <a href="#" data-target="pemesanan" class="sidebar-link flex items-center py-3 px-6 transition duration-200">
                    <i class="fas fa-receipt mr-3"></i> Pemesanan
                </a>
                <a href="#" data-target="laporan" class="sidebar-link flex items-center py-3 px-6 transition duration-200">
                    <i class="fas fa-chart-line mr-3"></i> Laporan
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow p-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <div class="flex items-center space-x-4">

                    <div class="relative">
                        <i id="user-dropdown-button" class="fas fa-user text-xl text-gray-600 cursor-pointer"></i>
                        <div id="user-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 hidden">
                            <div class="px-4 py-2 text-sm text-gray-700 border-b">{{ Auth::user()->name }}</div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Logout
                                </a>
                            </form>
                        </div>
                    </div>
                    <div class="relative">
                        <i id="notification-bell" class="fas fa-bell text-xl text-gray-600 cursor-pointer"></i>
                        <span id="notification-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                        <div id="notification-dropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg py-2 z-50 hidden">
                            <h3 class="text-gray-800 font-bold px-4 py-2 border-b">Pesanan Baru</h3>
                            <ul id="new-orders-list" class="max-h-60 overflow-y-auto">
                                <!-- New orders will be injected here -->
                            </ul>
                            <div id="no-new-orders" class="text-gray-500 text-center py-2 hidden">Tidak ada pesanan baru.</div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                
                <!-- Dashboard Page -->
                <section id="dashboard-page" class="page-section">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                            <i class="fas fa-utensils text-4xl text-orange-500 mr-4"></i>
                            <div>
                                <p class="text-gray-500">Total Menu</p>
                                <p id="total-menu" class="text-3xl font-bold">0</p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                            <i class="fas fa-receipt text-4xl text-blue-500 mr-4"></i>
                            <div>
                                <p class="text-gray-500">Total Pesanan</p>
                                <p id="total-pesanan" class="text-3xl font-bold">0</p>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md flex items-center">
                            <i class="fas fa-dollar-sign text-4xl text-green-500 mr-4"></i>
                            <div>
                                <p class="text-gray-500">Total Pendapatan</p>
                                <p id="total-pendapatan" class="text-3xl font-bold">Rp 0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Chart -->
                    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold">Grafik Penjualan</h2>
                            <div>
                                <button data-filter="daily" class="chart-filter-btn bg-orange-500 text-white py-1 px-3 rounded-md text-sm">Hari Ini</button>
                                <button data-filter="weekly" class="chart-filter-btn bg-gray-200 text-gray-700 py-1 px-3 rounded-md text-sm">Minggu Ini</button>
                                <button data-filter="monthly" class="chart-filter-btn bg-gray-200 text-gray-700 py-1 px-3 rounded-md text-sm">Bulan Ini</button>
                            </div>
                        </div>
                        <div class="relative h-96">
                            <canvas id="salesChart"></canvas>
                        </div>

                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-xl font-bold mb-4">5 Pesanan Terbaru</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-4 text-left">ID Pesanan</th>
                                        <th class="py-2 px-4 text-left">Tanggal</th>
                                        <th class="py-2 px-4 text-left">Total</th>
                                        <th class="py-2 px-4 text-left">Metode</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-orders-table">
                                    <!-- Data will be injected by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Laporan Page -->
                <section id="laporan-page" class="page-section hidden">
            <div id="report-section" class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold mb-4">Laporan Penjualan</h2>
                <button id="exportPdfButton" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700 mb-3">Export to PDF</button>
                <div class="flex space-x-2 mb-4">
                    <button data-filter="daily" class="report-filter-btn bg-orange-500 text-white px-4 py-2 rounded">Harian</button>
                    <button data-filter="weekly" class="report-filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded">Mingguan</button>
                    <button data-filter="monthly" class="report-filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded">Bulanan</button>
                </div>
                <p class="text-gray-600 mb-2">Periode: <span id="report-period-text">Harian</span></p>
                <p class="text-gray-600 mb-4">Total Penjualan: <span id="report-total-sales">Rp 0</span></p>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-left">ID Pesanan</th>
                                <th class="py-2 px-4 border-b text-left">Tanggal</th>
                                <th class="py-2 px-4 border-b text-left">Item</th>
                                <th class="py-2 px-4 border-b text-left">Total</th>
                                <th class="py-2 px-4 border-b text-left">Alamat</th>
                                <th class="py-2 px-4 border-b text-left">Metode Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody id="report-table-body">
                            <!-- Report rows will be rendered here -->
                        </tbody>
                    </table>
                </div>
            </div>
                </section>

                <!-- Placeholder Pages -->
                <!-- Menu Page -->
                <section id="menu-page" class="page-section hidden">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold">Kelola Menu</h2>
                            <button id="add-menu-btn" class="bg-orange-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-orange-600">
                                <i class="fas fa-plus mr-2"></i>Tambah Menu
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Nama Menu</th>
                                        <th class="py-2 px-4 text-left">Harga</th>
                                        <th class="py-2 px-4 text-left">Stok</th>
                                        <th class="py-2 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="menu-management-table">
                                    <!-- Menu items will be injected by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Menu Modal -->
                <div id="menu-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
                        <h2 id="menu-modal-title" class="text-2xl font-bold mb-6">Tambah Menu Baru</h2>
                        <form id="menu-form">
                            <input type="hidden" id="menu-id">
                            <div class="mb-4">
                                <label for="menu-name" class="block text-gray-700 mb-2">Nama Menu</label>
                                <input type="text" id="menu-name" class="w-full p-2 border rounded-md" required>
                            </div>
                            <div class="mb-4">
                                <label for="menu-price" class="block text-gray-700 mb-2">Harga</label>
                                <input type="number" id="menu-price" class="w-full p-2 border rounded-md" required>
                            </div>
                            <div class="mb-4">
                                <label for="menu-stock" class="block text-gray-700 mb-2">Stok</label>
                                <input type="number" id="menu-stock" class="w-full p-2 border rounded-md" required>
                            </div>
                            <div class="mb-4">
                                <label for="menu-description" class="block text-gray-700 mb-2">Deskripsi</label>
                                <textarea id="menu-description" rows="3" class="w-full p-2 border rounded-md" required></textarea>
                            </div>
                            <div class="mb-6">
                                <label for="menu-image-upload" class="block text-gray-700 mb-2">Unggah Gambar (Opsional)</label>
                                <input type="file" id="menu-image-upload" class="w-full p-2 border rounded-md" accept="image/*">
                                <p class="text-gray-500 text-sm mt-1">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                                <input type="hidden" id="menu-image-current">
                            </div>
                            <div class="flex justify-end space-x-4">
                                <button type="button" id="cancel-menu-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg hover:bg-gray-400">Batal</button>
                                <button type="submit" class="bg-orange-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-orange-600">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Slider Page -->
                <section id="slider-page" class="page-section hidden">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold">Kelola Slider Promosi</h2>
                            <button id="add-slider-btn" class="bg-orange-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-orange-600">
                                <i class="fas fa-plus mr-2"></i>Tambah Slide
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Judul</th>
                                        <th class="py-2 px-4 text-left">Deskripsi</th>
                                        <th class="py-2 px-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="slider-management-table">
                                    <!-- Slider items will be injected by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Slider Modal -->
                <div id="slider-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
                        <h2 id="slider-modal-title" class="text-2xl font-bold mb-6">Tambah Slide Baru</h2>
                        <form id="slider-form">
                            <input type="hidden" id="slider-id">
                            <div class="mb-4">
                                <label for="slider-title" class="block text-gray-700 mb-2">Judul</label>
                                <input type="text" id="slider-title" class="w-full p-2 border rounded-md" required>
                            </div>
                            <div class="mb-6">
                                <label for="slider-description" class="block text-gray-700 mb-2">Deskripsi</label>
                                <textarea id="slider-description" rows="3" class="w-full p-2 border rounded-md" required></textarea>
                            </div>
                            <div class="flex justify-end space-x-4">
                                <button type="button" id="cancel-slider-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg hover:bg-gray-400">Batal</button>
                                <button type="submit" class="bg-orange-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-orange-600">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Pemesanan Page -->
                <section id="pemesanan-page" class="page-section hidden">
                     <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold">Semua Pesanan</h2>
                            <div>
                                <button data-filter="daily" class="pemesanan-filter-btn bg-orange-500 text-white py-1 px-3 rounded-md text-sm">Harian</button>
                                <button data-filter="weekly" class="pemesanan-filter-btn bg-gray-200 text-gray-700 py-1 px-3 rounded-md text-sm">Mingguan</button>
                                <button data-filter="monthly" class="pemesanan-filter-btn bg-gray-200 text-gray-700 py-1 px-3 rounded-md text-sm">Bulanan</button>
                            </div>
                        </div>
                        <div class="mb-4 text-right">
                            <p class="text-gray-600">Total Penjualan (<span id="pemesanan-period-text">Harian</span>): <span id="pemesanan-total-sales" class="font-bold text-xl">Rp 0</span></p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="py-2 px-4 text-left">ID Pesanan</th>
                                        <th class="py-2 px-4 text-left">Tanggal</th>
                                        <th class="py-2 px-4 text-left">Item</th>
                                        <th class="py-2 px-4 text-left">Total</th>
                                        <th class="py-2 px-4 text-left">Alamat</th>
                                        <th class="py-2 px-4 text-left">Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody id="pemesanan-table-body">
                                    <!-- Data will be injected by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>


            </main>
        </div>
    </div>

    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
