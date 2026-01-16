<!DOCTYPE html>
<html lang="tr" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/dashboard.js'])
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside
        class="w-64 bg-gray-800 border-r border-gray-700 flex flex-col fixed h-full transition-all duration-300 z-20">
        <div class="p-6 border-b border-gray-700">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-orange-500 bg-clip-text text-transparent">
                API Dashboard</h1>
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <button onclick="router.load('dashboard')"
                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-gray-700 text-gray-400 hover:text-white"
                data-tab="dashboard">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                <span>Genel Bakış</span>
            </button>

            <button onclick="router.load('orders')"
                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-gray-700 text-gray-400 hover:text-white"
                data-tab="orders">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span>Siparişler</span>
            </button>

            <button onclick="router.load('products')"
                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-gray-700 text-gray-400 hover:text-white"
                data-tab="products">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                    </path>
                </svg>
                <span>Ürünler</span>
            </button>

            <button onclick="router.load('customers')"
                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-gray-700 text-gray-400 hover:text-white"
                data-tab="customers">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <span>Müşteriler</span>
            </button>

            <button onclick="router.load('tests')"
                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-gray-700 text-gray-400 hover:text-white mt-8 border-t border-gray-700 pt-6"
                data-tab="tests">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Test Merkezi</span>
            </button>

            <a href="/docs/api" target="_blank"
                class="nav-item w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-gray-700 text-gray-400 hover:text-white border-t border-gray-700 pt-6 mt-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>API Dokümantasyonu</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 id="page-title" class="text-3xl font-bold text-white mb-2">Genel Bakış</h2>
                <p class="text-gray-400 text-sm">Sistem durumu ve istatistikler</p>
            </div>

            <div class="flex gap-3">
                <button onclick="actions.resetDatabase()"
                    class="px-4 py-2 text-sm font-medium text-red-500 bg-red-500/10 border border-red-500/20 rounded-lg hover:bg-red-500 hover:text-white transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Sıfırla & Doldur
                </button>
                <button onclick="router.refresh()"
                    class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 hover:text-white transition-colors">
                    Yenile
                </button>
                <button id="create-btn" onclick="modal.open()"
                    class="hidden px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20">
                    + Yeni Oluştur
                </button>
            </div>
        </header>

        <!-- Content Area -->
        <div id="content-area" class="space-y-6">
            <!-- Dynamic Content -->
        </div>
    </main>

    <!-- Modal -->
    <div id="modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="modal.close()"></div>
        <div
            class="relative w-full max-w-lg bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl p-6 transform transition-all scale-100">
            <div class="flex justify-between items-center mb-6">
                <h3 id="modal-title" class="text-xl font-bold text-white">İşlem</h3>
                <button onclick="modal.close()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div id="modal-body"></div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="toast" class="fixed bottom-6 right-6 translate-y-24 transition-transform duration-300 z-50">
        <div
            class="bg-gray-800 border border-gray-700 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3">
            <div id="toast-icon"></div>
            <div id="toast-message">İşlem başarılı</div>
        </div>
    </div>
</body>

</html>