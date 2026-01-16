const API_URL = '/api';

// --- Router Application ---
const router = {
    currentTab: 'dashboard',

    init() {
        this.load('dashboard');
    },

    async load(tab) {
        this.currentTab = tab;
        this.updateNav();
        this.updateHeader(tab);

        const content = document.getElementById('content-area');
        content.innerHTML = this.loadingTemplate();

        try {
            switch (tab) {
                case 'dashboard': await views.dashboard(); break;
                case 'orders': await views.orders(); break;
                case 'products': await views.products(); break;
                case 'customers': await views.customers(); break;
                case 'tests': await views.tests(); break;
            }
        } catch (err) {
            content.innerHTML = this.errorTemplate(err.message);
        }
    },

    refresh() {
        this.load(this.currentTab);
    },

    updateNav() {
        document.querySelectorAll('.nav-item').forEach(el => {
            if (el.dataset.tab === this.currentTab) {
                el.classList.add('bg-gray-700', 'text-white');
                el.classList.remove('text-gray-400');
            } else {
                el.classList.remove('bg-gray-700', 'text-white');
                el.classList.add('text-gray-400');
            }
        });
    },

    updateHeader(tab) {
        const titles = {
            'dashboard': 'Genel Bakış',
            'orders': 'Sipariş Yönetimi',
            'products': 'Ürün Kataloğu',
            'customers': 'Müşteri Listesi',
            'tests': 'Test Merkezi'
        };
        document.getElementById('page-title').textContent = titles[tab];

        const createBtn = document.getElementById('create-btn');
        if (['orders', 'products', 'customers'].includes(tab)) {
            createBtn.classList.remove('hidden');
        } else {
            createBtn.classList.add('hidden');
        }
    },

    loadingTemplate() {
        return `
            <div class="flex items-center justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
            </div>
        `;
    },

    errorTemplate(msg) {
        return `
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-6 rounded-xl">
                <h3 class="font-bold text-lg mb-2">Hata Oluştu</h3>
                <p>${msg}</p>
            </div>
        `;
    }
};

// --- API Client ---
const api = {
    async get(endpoint) {
        const res = await fetch(`${API_URL}${endpoint}/api`);
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'API Hatası');
        return data;
    },

    async post(endpoint, body) {
        const res = await fetch(`${API_URL}${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'API Hatası');
        return data;
    },

    async patch(endpoint, body) {
        const res = await fetch(`${API_URL}${endpoint}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'API Hatası');
        return data;
    }
};

// --- Views & Logic ---
const views = {
    async dashboard() {
        const [orders, products, customers] = await Promise.all([
            api.get('/orders'),
            api.get('/products'),
            api.get('/customers')
        ]);

        document.getElementById('content-area').innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat Card -->
                <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path></svg>
                    </div>
                    <p class="text-gray-400 font-medium mb-1">Toplam Sipariş</p>
                    <h3 class="text-3xl font-bold text-white">${orders.meta.total}</h3>
                    <div class="mt-4 text-sm text-green-400 flex items-center gap-1">
                        <span>●</span> Canlı Veri
                    </div>
                </div>

                 <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                         <svg class="w-24 h-24 text-purple-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 001-1l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path></svg>
                    </div>
                    <p class="text-gray-400 font-medium mb-1">Aktif Ürünler</p>
                    <h3 class="text-3xl font-bold text-white">${products.meta.total}</h3>
                </div>

                 <div class="bg-gray-800 border border-gray-700 p-6 rounded-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-teal-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                    </div>
                    <p class="text-gray-400 font-medium mb-1">Müşteriler</p>
                    <h3 class="text-3xl font-bold text-white">${customers.meta.total}</h3>
                </div>
            </div>
        `;
    },

    async customers() {
        const res = await api.get('/customers');
        this.renderTable(
            ['Müşteri Ismi', 'Email', 'Kayıt Tarihi'],
            res.data.map(c => `
                <td class="p-4 text-white font-medium">${c.name}</td>
                <td class="p-4 text-gray-400">${c.email}</td>
                <td class="p-4 text-gray-400 font-mono text-sm">${new Date(c.created_at).toLocaleDateString()}</td>
            `)
        );
    },

    async products() {
        const res = await api.get('/products');
        this.renderTable(
            ['Ürün Adı', 'Fiyat', 'Stok', 'Durum', 'İşlem'],
            res.data.map(p => `
                <td class="p-4 text-white font-medium">${p.name}</td>
                <td class="p-4 text-blue-400 font-mono">${p.price}</td>
                <td class="p-4 text-gray-400">${p.stock_quantity}</td>
                <td class="p-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${p.is_active ? 'bg-green-500/10 text-green-500' : 'bg-red-500/10 text-red-500'}">
                        ${p.is_active ? 'Satışta' : 'Pasif'}
                    </span>
                </td>
                <td class="p-4">
                     <button onclick='modal.editProduct(${JSON.stringify(p).replace(/'/g, "&#39;")})' class="text-gray-500 hover:text-white transition-colors">Düzenle</button>
                </td>
            `)
        );
    },

    async orders() {
        const res = await api.get('/orders');
        this.renderTable(
            ['Sipariş ID', 'Müşteri', 'Tutar', 'Durum', 'İçerik', 'İşlem'],
            res.data.map(o => `
                 <td class="p-4 text-gray-500 font-mono text-xs">#${o.id.substring(0, 8)}</td>
                 <td class="p-4 text-white font-medium">${o.customer.name}</td>
                 <td class="p-4 text-white font-mono">${o.total_amount}</td>
                 <td class="p-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-${o.status.color === 'warning' ? 'yellow-500/10 text-yellow-500' : (o.status.color === 'success' ? 'green-500/10 text-green-500' : 'red-500/10 text-red-500')}">
                        ${o.status.label}
                    </span>
                 </td>
                 <td class="p-4 text-gray-400 text-sm">${o.items_count} Parça</td>
                 <td class="p-4">
                    ${o.status.value === 'pending' ?
                    `<button onclick="actions.cancelOrder('${o.id}')" class="text-red-400 hover:text-red-300 text-sm font-medium transition-colors">İptal Et</button>`
                    : '<span class="text-gray-600 text-sm">-</span>'}
                 </td>
            `)
        );
    },

    async tests() {
        document.getElementById('content-area').innerHTML = `
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-8 text-center">
                <svg class="w-16 h-16 text-blue-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                <h3 class="text-2xl font-bold text-white mb-2">Sistem Sağlık Testi</h3>
                <p class="text-gray-400 mb-8 max-w-md mx-auto">Verilen iş kurallarına göre tüm uçtan uca test senaryolarını çalıştırır ve sonuçları görselleştirir.</p>
                
                <button onclick="actions.runTests()" id="run-test-btn" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center gap-2 mx-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Testleri Başlat
                </button>
            </div>
            
            <div id="test-results" class="mt-6 hidden">
                <!-- Results will act here -->
            </div>
        `;
    },

    renderTable(headers, rows) {
        document.getElementById('content-area').innerHTML = `
            <div class="bg-gray-800 border border-gray-700 rounded-2xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900 border-b border-gray-700 text-gray-200 text-sm uppercase tracking-wider">
                            ${headers.map(h => `<th class="p-4 font-bold text-left">${h}</th>`).join('')}
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        ${rows.length ? rows.join('') : `<tr><td colspan="${headers.length}" class="p-8 text-center text-gray-500">Kayıt bulunamadı.</td></tr>`}
                    </tbody>
                </table>
            </div>
        `;
    }
};

// --- Actions ---
const actions = {
    async cancelOrder(id) {
        if (!confirm('Siparişi iptal etmek istediğinize emin misiniz?')) return;
        try {
            await api.patch(`/orders/${id}/status`, { status: 'cancelled' });
            toast.show('Sipariş iptal edildi.', 'success');
            router.refresh();
        } catch (e) {
            toast.show(e.message, 'error');
        }
    },

    async runTests() {
        const btn = document.getElementById('run-test-btn');
        const results = document.getElementById('test-results');

        btn.disabled = true;
        btn.innerHTML = '<div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div> Testler Çalışıyor...';
        results.classList.remove('hidden');
        results.innerHTML = `
            <div class="bg-gray-900 border border-gray-700 rounded-xl p-4 font-mono text-sm text-gray-400">
                <div class="flex items-center gap-2 mb-2 text-blue-400">
                    <span class="animate-pulse">●</span> Test ortamı hazırlanıyor...
                </div>
            </div>
        `;

        try {
            const data = await api.get('/system-test/run');

            let outputHtml = '';
            if (data.success) {
                outputHtml = data.output.split('\n').map(line => {
                    if (line.includes('PASS')) return `<div class="text-green-400 font-bold bg-green-500/10 p-2 rounded mb-1 border border-green-500/20">${line}</div>`;
                    if (line.includes('FAIL')) return `<div class="text-red-400 font-bold bg-red-500/10 p-2 rounded mb-1 border border-red-500/20">${line}</div>`;
                    if (line.trim() === '') return '';
                    return `<div class="text-gray-400 py-0.5">${line}</div>`;
                }).join('');
            } else {
                outputHtml = `<div class="text-red-500 bg-red-500/10 p-4 rounded">${data.message || 'Test çalıştırılamadı'}</div>`;
            }

            results.innerHTML = `<div class="bg-gray-900 border border-gray-700 rounded-xl p-6 font-mono text-sm overflow-x-auto">${outputHtml}</div>`;

        } catch (e) {
            results.innerHTML = `<div class="text-red-500">Hata: ${e.message}</div>`;
        } finally {
            btn.disabled = false;
            btn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Tekrar Çalıştır
            `;
        }
    }
};

// --- Modal System ---
const modal = {
    el: document.getElementById('modal'),
    body: document.getElementById('modal-body'),
    title: document.getElementById('modal-title'),

    open() {
        const tab = router.currentTab;
        if (tab === 'customers') this.customerForm();
        else if (tab === 'products') this.productForm();
        else if (tab === 'orders') this.orderForm();

        this.el.classList.remove('hidden');
    },

    close() {
        this.el.classList.add('hidden');
    },

    customerForm() {
        this.title.textContent = 'Yeni Müşteri';
        this.body.innerHTML = `
            <form onsubmit="modal.submitCustomer(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">İsim Soyisim</label>
                    <input type="text" name="name" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors">Kaydet</button>
            </form>
        `;
    },

    productForm() {
        this.title.textContent = 'Yeni Ürün';
        this.body.innerHTML = `
            <form onsubmit="modal.submitProduct(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Ürün Adı</label>
                    <input type="text" name="name" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                     <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Fiyat (Kuruş)</label>
                        <input type="number" name="price" required min="1" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Stok</label>
                        <input type="number" name="stock_quantity" required min="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors">Kaydet</button>
            </form>
        `;
    },

    async orderForm() {
        this.title.textContent = 'Yükleniyor...';
        const [customers, products] = await Promise.all([
            api.get('/customers'),
            api.get('/products')
        ]);

        this.title.textContent = 'Yeni Sipariş';
        this.body.innerHTML = `
            <form onsubmit="modal.submitOrder(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Müşteri</label>
                    <select name="customer_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                        ${customers.data.map(c => `<option value="${c.id}">${c.name}</option>`).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Ürün</label>
                    <div class="flex gap-2">
                         <select id="prod-select" class="flex-1 bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                            ${products.data.map(p => `<option value="${p.id}">${p.name} (${p.price})</option>`).join('')}
                        </select>
                        <input type="number" id="qty-input" value="1" min="1" class="w-20 bg-gray-900 border border-gray-700 rounded-lg px-2 py-2 text-white text-center">
                        <button type="button" onclick="modal.addToCart()" class="px-4 bg-gray-700 hover:bg-gray-600 text-white rounded-lg">Ekle</button>
                    </div>
                </div>
                
                <div id="cart-preview" class="bg-gray-900 border border-gray-700 rounded-lg p-3 text-sm text-gray-400 min-h-[60px]">
                    Sepet boş
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors">Siparişi Tamamla</button>
            </form>
        `;

        this.cart = [];
    },

    cart: [],

    addToCart() {
        const select = document.getElementById('prod-select');
        const qty = document.getElementById('qty-input');

        this.cart.push({
            product_id: select.value,
            quantity: parseInt(qty.value)
        });

        document.getElementById('cart-preview').innerHTML = this.cart.map(i =>
            `<div class="flex justify-between py-1 border-b border-gray-700 last:border-0"><span>Ürün ID: ${i.product_id.substring(0, 5)}...</span> <span>x${i.quantity}</span></div>`
        ).join('');
    },

    async submitCustomer(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target));
        try {
            await api.post('/customers', data);
            toast.show('Müşteri oluşturuldu', 'success');
            this.close();
            router.refresh();
        } catch (err) { toast.show(err.message, 'error'); }
    },

    async submitProduct(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target));
        data.is_active = true;
        try {
            await api.post('/products', data);
            toast.show('Ürün oluşturuldu', 'success');
            this.close();
            router.refresh();
        } catch (err) { toast.show(err.message, 'error'); }
    },

    async submitOrder(e) {
        e.preventDefault();
        const customer_id = new FormData(e.target).get('customer_id');
        if (!this.cart.length) return toast.show('Sepete ürün ekleyin', 'error');

        try {
            await api.post('/orders', { customer_id, items: this.cart });
            toast.show('Sipariş alındı', 'success');
            this.close();
            router.refresh();
        } catch (err) { toast.show(err.message, 'error'); }
    },

    editProduct(product) {
        this.title.textContent = 'Ürün Düzenle';
        this.body.innerHTML = `
            <form onsubmit="modal.submitEditProduct(event, '${product.id}')" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Ürün Adı</label>
                    <input type="text" name="name" value="${product.name}" required class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                     <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Fiyat (Kuruş)</label>
                        <input type="number" name="price" value="${product.price}" required min="1" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Stok</label>
                        <input type="number" name="stock_quantity" value="${product.stock_quantity}" required min="0" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                <div>
                     <label class="block text-sm font-medium text-gray-400 mb-1">Durum</label>
                     <select name="is_active" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500">
                        <option value="1" ${product.is_active ? 'selected' : ''}>Satışta</option>
                        <option value="0" ${!product.is_active ? 'selected' : ''}>Pasif</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition-colors">Güncelle</button>
            </form>
        `;
        this.openModalDirectly();
    },

    async submitEditProduct(e, id) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);

        // Convert 'is_active' to boolean for API if needed, or stick to what API expects.
        // Based on DTO, boolean or 0/1 might be fine. Let's send boolean to be safe.
        data.is_active = data.is_active === '1';
        data.price = parseInt(data.price);
        data.stock_quantity = parseInt(data.stock_quantity);

        try {
            await api.patch(`/products/${id}`, data);
            toast.show('Ürün güncellendi', 'success');
            this.close();
            router.refresh();
        } catch (err) { toast.show(err.message, 'error'); }
    },

    openModalDirectly() {
        this.el.classList.remove('hidden');
    }
},
};
// --- Toast ---
const toast = {
    el: document.getElementById('toast'),
    msg: document.getElementById('toast-message'),
    icon: document.getElementById('toast-icon'),

    show(message, type = 'success') {
        this.msg.textContent = message;
        this.el.classList.remove('translate-y-24');

        if (type === 'success') {
            this.icon.innerHTML = '<svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            this.el.style.borderColor = '#22c55e33';
        } else {
            this.icon.innerHTML = '<svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            this.el.style.borderColor = '#ef444433';
        }

        setTimeout(() => this.el.classList.add('translate-y-24'), 3000);
    }
};

// Init
router.init();
