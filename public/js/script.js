document.addEventListener('DOMContentLoaded', () => {

    // --- DATA INITIALIZATION ---
    // promoData is static. menuData is provided globally by the Blade template.
    const promoData = [
        { id: 1, title: 'Diskon Spesial Hari Ini!', description: 'Dapatkan diskon 15% untuk semua menu Ayam Bakar. Pesan sekarang jangan sampai kehabisan!' },
        { id: 2, title: 'Paket Hemat Berdua', description: '2 Porsi Pecel Lele + 2 Es Teh Manis hanya Rp 35.000!' },
        { id: 3, title: 'Gratis Es Teh', description: 'Pesan menu Nasi Goreng Spesial atau Soto Ayam dan dapatkan gratis Es Teh Manis.' },
    ];
    
    // --- DOM ELEMENTS ---
    const menuList = document.getElementById('menu-list');
    const cartItems = document.getElementById('cart-items');
    const cartEmpty = document.getElementById('cart-empty');
    const cartTotal = document.getElementById('cart-total');
    const orderForm = document.getElementById('order-form');
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const reportPeriod = document.getElementById('report-period');
    const totalOrdersEl = document.getElementById('total-orders');
    const totalSalesEl = document.getElementById('total-sales');
    const paymentSelect = document.getElementById('payment');
    const addressField = document.getElementById('address-field');

    // --- STATE ---
    let cart = [];

    // --- FUNCTIONS ---

    // Toggle Address Field based on Payment Method
    const toggleAddressField = () => {
        if (paymentSelect.value === 'COD' || paymentSelect.value === 'Transfer Dana') {
            addressField.classList.remove('hidden');
        } else {
            addressField.classList.add('hidden');
        }
    };


    // Format currency
    const formatCurrency = (number) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);

    // Render Menu
    const renderMenu = () => {
        if (!menuList) return;
        menuList.innerHTML = '';
        // menuData is a global variable from the Blade file
        menuData.forEach(item => {
            const menuItem = document.createElement('div');
            menuItem.className = 'bg-white rounded-lg shadow-md overflow-hidden flex flex-col transform hover:scale-105 transition-transform duration-300';
            menuItem.innerHTML = `
                <img src="images/${item.image}" alt="${item.name}" class="w-full h-40 object-cover">
                <div class="p-4 flex flex-col flex-grow">
                    <h4 class="text-xl font-bold mb-1">${item.name}</h4>
                    <p class="text-sm text-gray-500 mb-2 flex-grow">${item.description}</p>
                    <p class="text-lg font-semibold text-gray-800 mb-2">${formatCurrency(item.price)}</p>
                    <p class="text-sm text-gray-600 mb-4">Stok: ${item.stock}</p>
                    <button data-id="${item.id}" class="add-to-cart-btn mt-auto w-full bg-orange-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-orange-600 transition-colors ${item.stock === 0 ? 'opacity-50 cursor-not-allowed' : ''}" ${item.stock === 0 ? 'disabled' : ''}>
                        <i class="fas fa-cart-plus mr-2"></i>Tambah
                    </button>
                </div>
            `;
            menuList.appendChild(menuItem);
        });
    };

    // Render Slider
    const renderSlider = () => {
        const swiperWrapper = document.querySelector('.promo-slider .swiper-wrapper');
        if (!swiperWrapper) return;
        swiperWrapper.innerHTML = '';
        promoData.forEach(promo => {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide promo-card text-white p-8 text-center';
            slide.innerHTML = `
                <h2 class="text-3xl font-bold mb-2">${promo.title}</h2>
                <p class="text-lg">${promo.description}</p>
            `;
            swiperWrapper.appendChild(slide);
        });
    };


    // Update Cart
    const updateCart = () => {
        if (!cartItems) return;
        cartItems.innerHTML = '';
        if (cart.length === 0) {
            cartEmpty.classList.remove('hidden');
        } else {
            cartEmpty.classList.add('hidden');
            cart.forEach(item => {
                const cartItem = document.createElement('div');
                cartItem.className = 'flex flex-col border-b pb-2 mb-2'; // Changed to flex-col for notes input
                cartItem.innerHTML = `
                    <div class="flex justify-between items-center text-sm mb-1">
                        <div>
                            <p class="font-bold">${item.name}</p>
                            <p class="text-gray-500">${formatCurrency(item.price)} x ${item.quantity}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button data-id="${item.id}" class="decrease-qty bg-gray-200 px-2 rounded">-</button>
                            <span>${item.quantity}</span>
                            <button data-id="${item.id}" class="increase-qty bg-gray-200 px-2 rounded">+</button>
                            <button data-id="${item.id}" class="remove-item text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    `;
                cartItems.appendChild(cartItem);
            });

            // Add event listener for notes textarea after rendering
            cartItems.querySelectorAll('.cart-item-notes').forEach(textarea => {
                textarea.addEventListener('input', (e) => {
                    const itemId = parseInt(e.target.dataset.id);
                    const itemInCart = cart.find(i => i.id === itemId);
                    if (itemInCart) {
                        itemInCart.notes = e.target.value;
                    }
                });
            });
        }
        const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        cartTotal.textContent = formatCurrency(total);
    };

    // Add to cart
    const addToCart = (itemId) => {
        const itemToAdd = menuData.find(item => item.id === itemId);

        if (!itemToAdd || itemToAdd.stock <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Stok Habis!',
                text: `${itemToAdd ? itemToAdd.name : 'Item'} sedang tidak tersedia.`,
            });
            return;
        }

        const itemInCart = cart.find(item => item.id === itemId);
        if (itemInCart) {
            if (itemInCart.quantity < itemToAdd.stock) {
                itemInCart.quantity++;
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Cukup!',
                    text: `Anda telah mencapai batas stok untuk ${itemToAdd.name}.`,
                });
                return;
            }
        } else {
            cart.push({ ...itemToAdd, quantity: 1 }); // Initialize notes
        }
        
        // The stock is now managed by the server, but we simulate the decrease on the client for responsiveness for now.
        itemToAdd.stock--; 

        updateCart();
        renderMenu(); // Re-render menu to show updated stock
    };

    // Handle cart quantity changes
    const handleCartAction = (e) => {
        const target = e.target;
        const itemId = parseInt(target.dataset.id);
        let itemInCart = cart.find(item => item.id === itemId);
        const originalMenuItem = menuData.find(item => item.id === itemId);

        if (!itemInCart || !originalMenuItem) return;

        if (target.classList.contains('increase-qty')) {
            if (originalMenuItem.stock > 0) {
                itemInCart.quantity++;
                originalMenuItem.stock--;
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stok Tidak Cukup!',
                    text: `Stok ${originalMenuItem.name} telah habis.`,
                });
            }
        } else if (target.classList.contains('decrease-qty')) {
            if (itemInCart.quantity > 1) {
                itemInCart.quantity--;
                originalMenuItem.stock++;
            } else {
                originalMenuItem.stock += itemInCart.quantity;
                cart = cart.filter(item => item.id !== itemId);
            }
        } else if (target.classList.contains('remove-item') || target.parentElement.classList.contains('remove-item')) {
            originalMenuItem.stock += itemInCart.quantity;
            cart = cart.filter(item => item.id !== itemId);
        }

        updateCart();
        renderMenu(); // Re-render menu to show updated stock
    };

    // Handle Order Submission
    const handleOrder = async (e) => {
        e.preventDefault();
        if (cart.length === 0) {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Keranjang Anda masih kosong.' });
            return;
        }

        const paymentMethod = document.getElementById('payment').value;
        const address = document.getElementById('address').value;
        const orderNotes = document.getElementById('order-notes').value; // Get global order notes

        if (paymentMethod === 'COD' && address.trim() === '') {
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Alamat pengiriman tidak boleh kosong untuk metode COD.' });
            return;
        }

        // Map cart to the format expected by the API
        const orderItems = cart.map(item => ({
            id: item.id,
            quantity: item.quantity
        }));

        const orderData = {
            payment_method: paymentMethod,
            address: address,
            notes: orderNotes, // Include global order notes
            items: orderItems
        };

        try {
            const response = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();

            if (!response.ok) {
                // Handle validation errors (like stock issues) or other server errors
                const errorMessage = result.message || 'Terjadi kesalahan.';
                const stockError = result.errors && result.errors.stock ? result.errors.stock[0] : null;
                Swal.fire({ icon: 'error', title: 'Gagal Membuat Pesanan', text: stockError || errorMessage });
                // Note: To be fully robust, we should re-fetch menu data here to get the actual current stock
                return;
            }

            Swal.fire({ icon: 'success', title: 'Pesanan Berhasil!', text: 'Pesanan Anda telah berhasil dibuat.', timer: 2000, showConfirmButton: false });
            
            // Reset cart and form
            cart = [];
            document.getElementById('address').value = '';
            document.getElementById('order-notes').value = '';
            // paymentProofInput.value = ''; // Clear payment proof input - REMOVED
            // toggleAddressField(); // Hide payment proof field if payment method changes - REMOVED
            updateCart();
            renderMenu();

        } catch (error) {
            console.error('Order submission error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Tidak dapat terhubung ke server untuk membuat pesanan.' });
        }
    };

    // Render Sales Report (still uses localStorage for now)
    const renderReports = (filter = 'daily') => {
        const sales = JSON.parse(localStorage.getItem('salesData')) || [];
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const firstDayOfWeek = new Date(today);
        firstDayOfWeek.setDate(today.getDate() - today.getDay());
        const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);

        let filteredSales = [];
        let periodText = 'Harian';

        if (filter === 'daily') filteredSales = sales.filter(sale => new Date(sale.date) >= today);
        else if (filter === 'weekly') {
            periodText = 'Mingguan';
            filteredSales = sales.filter(sale => new Date(sale.date) >= firstDayOfWeek);
        } else if (filter === 'monthly') {
            periodText = 'Bulanan';
            filteredSales = sales.filter(sale => new Date(sale.date) >= firstDayOfMonth);
        }

        const totalSales = filteredSales.reduce((sum, sale) => sum + sale.total, 0);
        const totalOrders = filteredSales.length;

        if (reportPeriod && totalOrdersEl && totalSalesEl) {
            reportPeriod.textContent = periodText;
            totalOrdersEl.textContent = totalOrders;
            totalSalesEl.textContent = formatCurrency(totalSales);
        }

        if(filterButtons) {
            filterButtons.forEach(button => {
                if (button.dataset.filter === filter) {
                    button.classList.remove('bg-gray-200', 'text-gray-700');
                    button.classList.add('bg-orange-600', 'text-white');
                } else {
                    button.classList.add('bg-gray-200', 'text-gray-700');
                    button.classList.remove('bg-orange-600', 'text-white');
                }
            });
        }
    };


    // --- EVENT LISTENERS ---
    if (paymentSelect) paymentSelect.addEventListener('change', toggleAddressField);
    if (menuList) menuList.addEventListener('click', (e) => {
        if (e.target.classList.contains('add-to-cart-btn')) {
            addToCart(parseInt(e.target.dataset.id));
        }
    });
    if (cartItems) cartItems.addEventListener('click', handleCartAction);
    if (orderForm) orderForm.addEventListener('submit', handleOrder);
    if (mobileMenuButton) mobileMenuButton.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
    if (filterButtons) filterButtons.forEach(button => button.addEventListener('click', () => renderReports(button.dataset.filter)));

    // --- INITIALIZATION ---
    renderSlider();
    renderMenu();
    updateCart();
    renderReports();

    // Initialize Swiper
    const swiper = new Swiper('.promo-slider', {
        loop: true,
        autoplay: { delay: 3000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    });
    
    if (swiper) swiper.update();
});