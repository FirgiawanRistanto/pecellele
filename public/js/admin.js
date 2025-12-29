document.addEventListener('DOMContentLoaded', () => {
    // --- STATE ---
    let menuData = []; // Menu data will be populated from the API
    let salesData = []; // Sales data will be populated from the API
    let promoData = JSON.parse(localStorage.getItem('promoData')) || []; // Promo data will be populated from the API

    // --- DOM ELEMENTS ---
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    const pageSections = document.querySelectorAll('.page-section');
    const totalMenuEl = document.getElementById('total-menu');
    const totalPesananEl = document.getElementById('total-pesanan');
    const totalPendapatanEl = document.getElementById('total-pendapatan');
    const recentOrdersTable = document.getElementById('recent-orders-table');
    const reportTableBody = document.getElementById('report-table-body');
    const reportPeriodText = document.getElementById('report-period-text');
    const reportTotalSales = document.getElementById('report-total-sales');
    const chartFilterBtns = document.querySelectorAll('.chart-filter-btn');
    const reportFilterBtns = document.querySelectorAll('.report-filter-btn');
    const salesChartCanvas = document.getElementById('salesChart').getContext('2d');
    let salesChart;

    // Menu Page Elements
    const menuManagementTable = document.getElementById('menu-management-table');
    const addMenuBtn = document.getElementById('add-menu-btn');
    const menuModal = document.getElementById('menu-modal');
    const menuModalTitle = document.getElementById('menu-modal-title');
    const cancelMenuBtn = document.getElementById('cancel-menu-btn');
    const menuForm = document.getElementById('menu-form');
    const menuIdInput = document.getElementById('menu-id');
    const menuNameInput = document = document.getElementById('menu-name');
    const menuPriceInput = document.getElementById('menu-price');
    const menuStockInput = document.getElementById('menu-stock');
    const menuDescriptionInput = document.getElementById('menu-description');
    const menuImageUploadInput = document.getElementById('menu-image-upload'); // New file input
    const menuImageCurrentInput = document.getElementById('menu-image-current'); // Hidden input for current image

    // Slider Page Elements (Unaffected by this refactor)
    const sliderManagementTable = document.getElementById('slider-management-table');
    const addSliderBtn = document.getElementById('add-slider-btn');
    const sliderModal = document.getElementById('slider-modal');
    const sliderModalTitle = document.getElementById('slider-modal-title');
    const cancelSliderBtn = document.getElementById('cancel-slider-btn');
    const sliderForm = document.getElementById('slider-form');
    const sliderIdInput = document.getElementById('slider-id');
    const sliderTitleInput = document.getElementById('slider-title');
    const sliderDescriptionInput = document.getElementById('slider-description');

    // Pemesanan Page Elements
    const pemesananTableBody = document.getElementById('pemesanan-table-body');
    const pemesananFilterBtns = document.querySelectorAll('.pemesanan-filter-btn');
    const pemesananPeriodText = document.getElementById('pemesanan-period-text');
    const pemesananTotalSales = document.getElementById('pemesanan-total-sales');

    // Notification Elements
    const simulateOrderBtn = document.getElementById('simulate-order-btn');
    const notificationBell = document.getElementById('notification-bell');
    const notificationBadge = document.getElementById('notification-badge');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const newOrdersListEl = document.getElementById('new-orders-list');
    const noNewOrdersEl = document.getElementById('no-new-orders');
    let newOrderCount = 0;
    let newOrdersList = [];

    // User Dropdown Elements
    const userDropdownButton = document.getElementById('user-dropdown-button');
    const userDropdownMenu = document.getElementById('user-dropdown-menu');

    // Add event listeners for user dropdown
    if (userDropdownButton && userDropdownMenu) {
        userDropdownButton.addEventListener('click', (event) => {
            event.stopPropagation(); // Prevent document click from closing it immediately
            userDropdownMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!userDropdownMenu.contains(event.target) && !userDropdownButton.contains(event.target)) {
                userDropdownMenu.classList.add('hidden');
            }
        });
    }

    // --- HELPER FUNCTIONS ---
    const formatCurrency = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    const formatDate = (dateString) => new Date(dateString).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

    // --- API FUNCTIONS for MENU ---
    const getMenus = async () => {
        try {
            const response = await fetch('/api/menus');
            if (!response.ok) throw new Error('Network response was not ok');
            menuData = await response.json();
            renderMenuManagementTable();
            renderDashboard(); // Re-render dashboard to update menu count
        } catch (error) {
            console.error('Failed to fetch menus:', error);
            Swal.fire('Error', 'Gagal memuat data menu.', 'error');
        }
    };

    const getOrders = async () => {
        try {
            const response = await fetch('/api/orders');
            if (!response.ok) throw new Error('Network response was not ok');
            salesData = await response.json();
            renderDashboard();
            renderReportTable('daily', 'report');
            renderReportTable('daily', 'pemesanan');
            renderSalesChart('daily');
        } catch (error) {
            console.error('Failed to fetch orders:', error);
            Swal.fire('Error', 'Gagal memuat data pesanan.', 'error');
        }
    };

    const createMenu = async (formData) => {
        try {
            const response = await fetch('/api/menus', {
                method: 'POST',
                // No 'Content-Type' header needed for FormData
                body: formData
            });
            if (!response.ok) {
                const errors = await response.json();
                throw new Error(errors.message || 'Gagal membuat menu.');
            }
            await getMenus(); // Refresh data
            closeMenuModal();
            Swal.fire('Berhasil!', 'Menu baru telah ditambahkan.', 'success');
        } catch (error) {
            console.error('Failed to create menu:', error);
            Swal.fire('Error', `Gagal menyimpan menu: ${error.message}`, 'error');
        }
    };

    const updateMenu = async (id, formData) => {
        try {
            const response = await fetch(`/api/menus/${id}`, {
                method: 'POST', // Use POST for FormData with _method=PUT
                // No 'Content-Type' header needed for FormData
                body: formData
            });
            if (!response.ok) {
                const errors = await response.json();
                throw new Error(errors.message || 'Gagal memperbarui menu.');
            }
            await getMenus(); // Refresh data
            closeMenuModal();
            Swal.fire('Berhasil!', 'Menu telah diperbarui.', 'success');
        } catch (error) {
            console.error('Failed to update menu:', error);
            Swal.fire('Error', `Gagal memperbarui menu: ${error.message}`, 'error');
        }
    };

    const deleteMenu = async (id) => {
        try {
            const response = await fetch(`/api/menus/${id}`, {
                method: 'DELETE'
            });
            if (!response.ok) throw new Error('Gagal menghapus menu.');
            await getMenus(); // Refresh data
            Swal.fire('Dihapus!', 'Menu berhasil dihapus.', 'success');
        } catch (error) {
            console.error('Failed to delete menu:', error);
            Swal.fire('Error', 'Gagal menghapus menu.', 'error');
        }
    };

    // --- MENU MANAGEMENT (Refactored) ---
    const renderMenuManagementTable = () => {
        menuManagementTable.innerHTML = '';
        if (menuData.length === 0) {
            menuManagementTable.innerHTML = `<tr><td colspan="4" class="text-center py-4">Belum ada data menu.</td></tr>`;
            return;
        }
        menuData.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-2 px-4 border-b">${item.name}</td>
                <td class="py-2 px-4 border-b">${formatCurrency(item.price)}</td>
                <td class="py-2 px-4 border-b">${item.stock}</td>
                <td class="py-2 px-4 border-b text-center">
                    <button data-id="${item.id}" class="edit-menu-btn text-blue-500 hover:text-blue-700 mr-4"><i class="fas fa-edit"></i></button>
                    <button data-id="${item.id}" class="delete-menu-btn text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                </td>
            `;
            menuManagementTable.appendChild(row);
        });
    };

    const openMenuModal = (item) => {
        menuForm.reset();
        menuImageUploadInput.value = ''; // Clear file input
        if (item) {
            menuModalTitle.textContent = 'Edit Menu';
            menuIdInput.value = item.id;
            menuNameInput.value = item.name;
            menuPriceInput.value = item.price;
            menuStockInput.value = item.stock;
            menuDescriptionInput.value = item.description;
            menuImageCurrentInput.value = item.image; // Set current image name
        } else {
            menuModalTitle.textContent = 'Tambah Menu Baru';
            menuIdInput.value = '';
            menuImageCurrentInput.value = ''; // Also clear current image name for new items
        }
        menuModal.classList.remove('hidden');
    };

    const closeMenuModal = () => menuModal.classList.add('hidden');

    const handleMenuFormSubmit = (e) => {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('name', menuNameInput.value);
        formData.append('price', menuPriceInput.value);
        formData.append('stock', menuStockInput.value);
        formData.append('description', menuDescriptionInput.value);
        
        if (menuImageUploadInput.files.length > 0) {
            formData.append('image_file', menuImageUploadInput.files[0]);
        } else if (menuImageCurrentInput.value) {
            // If no new file is uploaded but there's an existing image, send its name
            formData.append('image', menuImageCurrentInput.value);
        }

        const id = menuIdInput.value;
        if (id) {
            formData.append('_method', 'PUT'); // Spoof PUT method for FormData
            updateMenu(id, formData);
        } else {
            createMenu(formData);
        }
    };

    const handleMenuDelete = (id) => {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Anda tidak akan bisa mengembalikan ini!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteMenu(id);
            }
        });
    };

    addMenuBtn.addEventListener('click', () => openMenuModal());
    cancelMenuBtn.addEventListener('click', closeMenuModal);
    menuForm.addEventListener('submit', handleMenuFormSubmit);
    menuManagementTable.addEventListener('click', (e) => {
        const target = e.target.closest('button');
        if (!target) return;
        const id = parseInt(target.dataset.id);
        if (target.classList.contains('edit-menu-btn')) {
            const item = menuData.find(m => m.id === id);
            openMenuModal(item);
        } else if (target.classList.contains('delete-menu-btn')) {
            handleMenuDelete(id);
        }
    });

    // --- UNCHANGED SECTIONS (Dashboard, Slider, Reports, etc. still use localStorage) ---
    const renderDashboard = () => {
        totalMenuEl.textContent = menuData.length; // Uses menuData from API now
        totalPesananEl.textContent = salesData.length;
        const totalPendapatan = salesData.reduce((sum, sale) => sum + sale.total, 0);
        totalPendapatanEl.textContent = formatCurrency(totalPendapatan);
        recentOrdersTable.innerHTML = '';
        const recentSales = [...salesData].reverse().slice(0, 5);
        if (recentSales.length === 0) {
            recentOrdersTable.innerHTML = `<tr><td colspan="4" class="text-center py-4">Tidak ada data pesanan.</td></tr>`;
            return;
        }
        recentSales.forEach(sale => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-2 px-4 border-b">#${sale.id.toString().slice(-5)}</td>
                <td class="py-2 px-4 border-b">${formatDate(sale.date)}</td>
                <td class="py-2 px-4 border-b">${formatCurrency(sale.total)}</td>
                <td class="py-2 px-4 border-b">${sale.paymentMethod}</td>
            `;
            recentOrdersTable.appendChild(row);
        });
    };
    
    const navigateTo = (targetId) => {
        pageSections.forEach(section => section.classList.add('hidden'));
        document.getElementById(`${targetId}-page`).classList.remove('hidden');
        sidebarLinks.forEach(link => {
            link.classList.remove('active');
            if (link.dataset.target === targetId) link.classList.add('active');
        });
    };

    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            navigateTo(link.dataset.target);
        });
    });

    // All other functions (slider, reports, notifications, etc.) remain the same
    // and continue to use localStorage for promoData and salesData.
    const renderSliderManagementTable = () => {
        sliderManagementTable.innerHTML = '';
        promoData.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-2 px-4 border-b">${item.title}</td>
                <td class="py-2 px-4 border-b">${item.description}</td>
                <td class="py-2 px-4 border-b text-center">
                    <button data-id="${item.id}" class="edit-slider-btn text-blue-500 hover:text-blue-700 mr-4"><i class="fas fa-edit"></i></button>
                    <button data-id="${item.id}" class="delete-slider-btn text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                </td>
            `;
            sliderManagementTable.appendChild(row);
        });
    };

    const openSliderModal = (item) => {
        sliderForm.reset();
        if (item) {
            sliderModalTitle.textContent = 'Edit Slide';
            sliderIdInput.value = item.id;
            sliderTitleInput.value = item.title;
            sliderDescriptionInput.value = item.description;
        } else {
            sliderModalTitle.textContent = 'Tambah Slide Baru';
            sliderIdInput.value = '';
        }
        sliderModal.classList.remove('hidden');
    };

    const closeSliderModal = () => sliderModal.classList.add('hidden');

    const handleSliderFormSubmit = (e) => {
        e.preventDefault();
        const slideItem = {
            id: sliderIdInput.value ? parseInt(sliderIdInput.value) : Date.now(),
            title: sliderTitleInput.value,
            description: sliderDescriptionInput.value,
        };

        if (sliderIdInput.value) { // Editing
            const index = promoData.findIndex(item => item.id === slideItem.id);
            promoData[index] = slideItem;
        } else { // Adding
            promoData.push(slideItem);
        }

        localStorage.setItem('promoData', JSON.stringify(promoData));
        renderSliderManagementTable();
        closeSliderModal();
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Slide berhasil disimpan.', timer: 1500, showConfirmButton: false });
    };

    const handleSliderDelete = (id) => {
        Swal.fire({
            title: 'Apakah Anda yakin?', text: 'Anda tidak akan bisa mengembalikan ini!', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                promoData = promoData.filter(item => item.id !== id);
                localStorage.setItem('promoData', JSON.stringify(promoData));
                renderSliderManagementTable();
                Swal.fire('Dihapus!', 'Slide berhasil dihapus.', 'success');
            }
        });
    };

    addSliderBtn.addEventListener('click', () => openSliderModal());
    cancelSliderBtn.addEventListener('click', closeSliderModal);
    sliderForm.addEventListener('submit', handleSliderFormSubmit);
    sliderManagementTable.addEventListener('click', (e) => {
        const target = e.target.closest('button');
        if (!target) return;
        const id = parseInt(target.dataset.id);
        if (target.classList.contains('edit-slider-btn')) {
            const item = promoData.find(p => p.id === id);
            openSliderModal(item);
        } else if (target.classList.contains('delete-slider-btn')) {
            handleSliderDelete(id);
        }
    });

     const renderSalesChart = (filter = 'daily') => {
        const now = new Date();
        let labels = [];
        let data = [];
        if (filter === 'daily') {
            labels = Array.from({ length: 24 }, (_, i) => `${i.toString().padStart(2, '0')}:00`);
            data = Array(24).fill(0);
            salesData.filter(sale => new Date(sale.date).toDateString() === now.toDateString()).forEach(sale => {
                const hour = new Date(sale.date).getHours();
                data[hour] += sale.total;
            });
        } else if (filter === 'weekly') {
            labels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            data = Array(7).fill(0);
            const firstDayOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
            salesData.filter(sale => new Date(sale.date) >= firstDayOfWeek).forEach(sale => {
                const day = new Date(sale.date).getDay();
                data[day] += sale.total;
            });
        } else if (filter === 'monthly') {
            const daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
            labels = Array.from({ length: daysInMonth }, (_, i) => i + 1);
            data = Array(daysInMonth).fill(0);
            salesData.filter(sale => new Date(sale.date).getMonth() === now.getMonth() && new Date(sale.date).getFullYear() === now.getFullYear()).forEach(sale => {
                const dayOfMonth = new Date(sale.date).getDate();
                data[dayOfMonth - 1] += sale.total;
            });
        }
        if (salesChart) salesChart.destroy();
        salesChart = new Chart(salesChartCanvas, {
            type: 'line',
            data: { labels, datasets: [{ label: 'Penjualan', data, backgroundColor: 'rgba(251, 146, 60, 0.2)', borderColor: '#fb923c', borderWidth: 2, tension: 0.3, fill: true }] },
            options: { scales: { y: { beginAtZero: true } }, responsive: true, maintainAspectRatio: false }
        });
        chartFilterBtns.forEach(btn => {
            btn.classList.toggle('bg-orange-500', btn.dataset.filter === filter);
            btn.classList.toggle('text-white', btn.dataset.filter === filter);
            btn.classList.toggle('bg-gray-200', btn.dataset.filter !== filter);
            btn.classList.toggle('text-gray-700', btn.dataset.filter !== filter);
        });
    };

    const renderReportTable = (filter = 'daily', type = 'report') => {
        const now = new Date();
        let filteredSales = [];
        let periodText = 'Harian';
        if (filter === 'daily') {
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            filteredSales = salesData.filter(sale => new Date(sale.date) >= today);
        } else if (filter === 'weekly') {
            periodText = 'Mingguan';
            const firstDayOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
            firstDayOfWeek.setHours(0, 0, 0, 0);
            filteredSales = salesData.filter(sale => new Date(sale.date) >= firstDayOfWeek);
        } else if (filter === 'monthly') {
            periodText = 'Bulanan';
            const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
            filteredSales = salesData.filter(sale => new Date(sale.date) >= firstDayOfMonth);
        }
        const tableBody = type === 'report' ? reportTableBody : pemesananTableBody;
        tableBody.innerHTML = '';
        if (filteredSales.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4">Tidak ada data untuk periode ini.</td></tr>`;
        }
        [...filteredSales].reverse().forEach(sale => {
            const itemsString = sale.items.map(item => `${item.name} (x${item.quantity})`).join(', ');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-2 px-4 border-b">#${sale.id.toString().slice(-5)}</td>
                <td class="py-2 px-4 border-b">${formatDate(sale.date)}</td>
                <td class="py-2 px-4 border-b">${itemsString}</td>
                <td class="py-2 px-4 border-b">${formatCurrency(sale.total)}</td>
                <td class="py-2 px-4 border-b text-sm">${sale.address}</td>
                <td class="py-2 px-4 border-b">${sale.paymentMethod}</td>
            `;
            tableBody.appendChild(row);
        });
        const total = filteredSales.reduce((sum, sale) => sum + sale.total, 0);
        if (type === 'report') {
            reportPeriodText.textContent = periodText;
            reportTotalSales.textContent = formatCurrency(total);
        } else {
            pemesananPeriodText.textContent = periodText;
            pemesananTotalSales.textContent = formatCurrency(total);
        }
        const filterBtns = type === 'report' ? reportFilterBtns : pemesananFilterBtns;
        filterBtns.forEach(btn => {
            btn.classList.toggle('bg-orange-500', btn.dataset.filter === filter);
            btn.classList.toggle('text-white', btn.dataset.filter === filter);
            btn.classList.toggle('bg-gray-200', btn.dataset.filter !== filter);
            btn.classList.toggle('text-gray-700', btn.dataset.filter !== filter);
        });
    };

    chartFilterBtns.forEach(btn => btn.addEventListener('click', () => renderSalesChart(btn.dataset.filter)));
    reportFilterBtns.forEach(btn => btn.addEventListener('click', () => renderReportTable(btn.dataset.filter, 'report')));
    pemesananFilterBtns.forEach(btn => btn.addEventListener('click', () => renderReportTable(btn.dataset.filter, 'pemesanan')));

    const updateNotificationBadge = () => {
        if (newOrderCount > 0) {
            notificationBadge.textContent = newOrderCount;
            notificationBadge.classList.remove('hidden');
        } else {
            notificationBadge.classList.add('hidden');
        }
    };

    const renderNewOrdersDropdown = () => {
        newOrdersListEl.innerHTML = '';
        if (newOrdersList.length === 0) {
            noNewOrdersEl.classList.remove('hidden');
        } else {
            noNewOrdersEl.classList.add('hidden');
            newOrdersList.forEach(order => {
                const li = document.createElement('li');
                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer text-gray-700 text-sm';
                li.innerHTML = `Pesanan #${order.id.toString().slice(-5)} - ${formatCurrency(order.total)}`;
                newOrdersListEl.appendChild(li);
            });
        }
    };
    
    // --- INITIALIZATION ---
    getMenus(); // Fetch initial menu data
    getOrders(); // Fetch initial order data
    renderSliderManagementTable();
    updateNotificationBadge();

    // Add event listener for exportPdfButton
    const exportPdfButton = document.getElementById('exportPdfButton');
    if (exportPdfButton) {
        exportPdfButton.addEventListener('click', () => {
            const currentFilterBtn = document.querySelector('.report-filter-btn.bg-orange-500');
            const filter = currentFilterBtn ? currentFilterBtn.dataset.filter : 'daily';
            window.open(`/api/reports/pdf?filter=${filter}`, '_blank');
        });
    }
});