<!-- Sidebar backdrop for mobile (placed outside aside to avoid transform containment) -->
<div 
    x-show="$store.sidebar.open" 
    @click="$store.sidebar.close()"
    class="fixed inset-0 bg-gray-900 bg-opacity-50 z-30 lg:hidden"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;"
></div>

<aside 
    class="fixed top-0 left-0 z-40 w-sidebar h-screen pt-topbar transition-transform duration-300 ease-in-out bg-white border-r border-gray-200 shadow-lg lg:translate-x-0"
    :class="$store.sidebar.open ? 'translate-x-0' : '-translate-x-full'"
    x-data="{
        masterDataOpen: {{ request()->is('users*') || request()->is('materials*') || request()->is('customers*') || request()->is('suppliers*') || request()->is('formulas*') || request()->is('products*') ? 'true' : 'false' }},
        productionOpen: {{ request()->is('productions*') ? 'true' : 'false' }},
        purchasingOpen: {{ request()->is('purchasing*') || request()->is('purchase*') ? 'true' : 'false' }},
        salesOpen: {{ request()->is('sales*') || request()->is('orders*') || request()->is('invoices*') ? 'true' : 'false' }},
    accountingOpen: {{ request()->is('accounting*') || request()->is('journal*') || request()->is('payments*') || request()->is('trial-balance*') || request()->is('reports*') ? 'true' : 'false' }},
        reportsOpen: {{ request()->is('reports*') ? 'true' : 'false' }},
        
        toggleMenu(menu) {
            // Close all other menus
            if (menu !== 'masterData') this.masterDataOpen = false;
            if (menu !== 'production') this.productionOpen = false;
            if (menu !== 'purchasing') this.purchasingOpen = false;
            if (menu !== 'sales') this.salesOpen = false;
            if (menu !== 'accounting') this.accountingOpen = false;
            if (menu !== 'reports') this.reportsOpen = false;
            
            // Toggle the selected menu
            this[menu + 'Open'] = !this[menu + 'Open'];
        }
    }"
>
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <ul class="space-y-2 font-medium pt-4">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center p-3 text-gray-900 rounded-xl hover:bg-gray-100 hover:text-primary-600 group transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-700 border-r-2 border-primary-600' : '' }}">
                    <svg class="w-5 h-5 text-gray-500 transition duration-200 group-hover:text-primary-600 {{ request()->routeIs('dashboard') ? 'text-primary-600' : '' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                        <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
                    </svg>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>
            
            <!-- Master Data -->
            <li>
                <button 
                    @click="toggleMenu('masterData')" 
                    type="button" 
                    class="flex items-center w-full p-3 text-base text-gray-900 transition duration-200 rounded-xl group hover:bg-gray-100 hover:text-primary-600"
                    :class="masterDataOpen ? 'bg-primary-50 text-primary-600' : ''"
                >
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-200 group-hover:text-primary-600" :class="masterDataOpen ? 'text-primary-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4m16 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Master Data</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="masterDataOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <ul 
                    x-show="masterDataOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="py-2 space-y-1"
                    style="display: none;"
                >
                    <li>
                        <a href="{{ route('users.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('users.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('materials.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('materials.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Materials
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('suppliers.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('suppliers.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Suppliers
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('customers.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('customers.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Customers
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('formulas.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('formulas.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Formulas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('products.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Products
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Production -->
            <li>
                <button 
                    @click="toggleMenu('production')" 
                    type="button" 
                    class="flex items-center w-full p-3 text-base text-gray-900 transition duration-200 rounded-xl group hover:bg-gray-100 hover:text-primary-600"
                    :class="productionOpen ? 'bg-primary-50 text-primary-600' : ''"
                >
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-200 group-hover:text-primary-600" :class="productionOpen ? 'text-primary-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Production</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="productionOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <ul 
                    x-show="productionOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="py-2 space-y-1"
                    style="display: none;"
                >
                    <li>
                        <a href="{{ route('productions.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('productions.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Production Orders
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Production Reports
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Purchasing -->
            <li>
                <button 
                    @click="purchasingOpen = !purchasingOpen" 
                    type="button" 
                    class="flex items-center w-full p-3 text-base text-gray-900 transition duration-200 rounded-xl group hover:bg-gray-100 hover:text-primary-600"
                    :class="purchasingOpen ? 'bg-gray-50 text-primary-600' : ''"
                >
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-200 group-hover:text-primary-600" :class="purchasingOpen ? 'text-primary-600' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                        <path d="M17 5.923A1 1 0 0 0 16 5h-3V4a4 4 0 1 0-8 0v1H2a1 1 0 0 0-1 .923L.086 17.846A2 2 0 0 0 2.08 20h13.84a2 2 0 0 0 1.994-2.153L17 5.923ZM7 9a1 1 0 0 1-2 0V8h2v1Zm0-5a2 2 0 1 1 4 0v1H7V4Zm6 5a1 1 0 1 1-2 0V8h2v1Z"/>
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Purchasing</span>
                    <svg class="w-3 h-3 transition-transform duration-200" :class="purchasingOpen ? 'rotate-180' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <ul 
                    x-show="purchasingOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="py-2 space-y-1"
                    style="display: none;"
                >
                    <li>
                        <a href="{{ route('purchase-orders.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('purchase-orders.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            Purchase Orders
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Purchase Reports
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Sales -->
            <li>
                <button 
                    @click="salesOpen = !salesOpen" 
                    type="button" 
                    class="flex items-center w-full p-3 text-base text-gray-900 transition duration-200 rounded-xl group hover:bg-gray-100 hover:text-primary-600"
                    :class="salesOpen ? 'bg-gray-50 text-primary-600' : ''"
                >
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-200 group-hover:text-primary-600" :class="salesOpen ? 'text-primary-600' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m17.418 3.623-.018-.008a6.713 6.713 0 0 0-2.4-.569V2a1.97 1.97 0 0 0-1.933-2H9.933A1.97 1.97 0 0 0 8 2v1.046a6.672 6.672 0 0 0-2.4.569l-.018.008A6.676 6.676 0 0 0 2 9.5c0 3.747 3.253 6.787 7.067 6.787S16.133 13.247 16.133 9.5a6.676 6.676 0 0 0-3.582-5.877ZM6.803 19.103A9.73 9.73 0 0 1 5.146 17.2a11.124 11.124 0 0 0 3.854 1.8 1.001 1.001 0 1 1-.4 1.96 13.075 13.075 0 0 1-1.797-.857Zm8.394 0a13.075 13.075 0 0 1-1.797.857 1.001 1.001 0 1 1-.4-1.96 11.124 11.124 0 0 0 3.854-1.8 9.73 9.73 0 0 1-1.657 1.903Z"/>
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Sales</span>
                    <svg class="w-3 h-3 transition-transform duration-200" :class="salesOpen ? 'rotate-180' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <ul 
                    x-show="salesOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="py-2 space-y-1"
                    style="display: none;"
                >
                    <li>
                        <a href="{{ route('sales-orders.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('sales-orders.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Sales Orders
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('invoices.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('invoices.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h3m2-14h2a2 2 0 012 2v2m0 4v4a2 2 0 01-2 2h-2m0-8h4M9 9h1m-1 4h1"/>
                            </svg>
                            Invoices
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payments.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('payments.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2m-6-4h8l-3-3m0 6l3-3" />
                            </svg>
                            Payments
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Accounting -->
            <li>
                <button 
                    @click="accountingOpen = !accountingOpen" 
                    type="button" 
                    class="flex items-center w-full p-3 text-base text-gray-900 transition duration-200 rounded-xl group hover:bg-gray-100 hover:text-primary-600"
                    :class="accountingOpen ? 'bg-gray-50 text-primary-600' : ''"
                >
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-200 group-hover:text-primary-600" :class="accountingOpen ? 'text-primary-600' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11.074 4 8.442.408A.95.95 0 0 0 7.014.254L2.926 4h8.148ZM9 13v-1a4 4 0 0 1 4-4h6V6a1 1 0 0 0-1-1H1a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h17a1 1 0 0 0 1-1v-2H9Z"/>
                        <path d="M19 10h-6a2 2 0 0 0-2 2v1a2 2 0 0 0 2 2h6a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1Zm-4.5 3.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2ZM12.62 4h2.78L12.539.41a1.086 1.086 0 1 0-1.7 1.352L12.62 4Z"/>
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Accounting</span>
                    <svg class="w-3 h-3 transition-transform duration-200" :class="accountingOpen ? 'rotate-180' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>
                <ul 
                    x-show="accountingOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-1"
                    class="py-2 space-y-1"
                    style="display: none;"
                >
                    <li>
                        <a href="{{ route('ledger.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('ledger.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Buku Besar
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('chart.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('chart.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 13h3V7H7v6zm5 4h3V7h-3v10zm5-2h3V7h-3v8z"/></svg>
                            Chart of Account
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('journals.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('journals.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3m6 0a3 3 0 11-6 0 3 3 0 016 0zm-9 8h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Journal Entries
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('trial-balance.index') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('trial-balance.*') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm6 0V9a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Trial Balance
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.profit_loss') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('reports.profit_loss') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 13h3V7H7v6zm5 4h3V7h-3v10zm5-2h3V7h-3v8z"/>
                            </svg>
                            Profit &amp; Loss
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reports.balance_sheet') }}" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700 {{ request()->routeIs('reports.balance_sheet') ? 'bg-primary-100 text-primary-700 font-medium' : '' }}">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h10M4 14h16M4 18h10"/>
                            </svg>
                            Balance Sheet
                        </a>
                    </li>
                    
                </ul>
            </li>
            
        </ul>
        
        <!-- Sidebar Footer -->
        <div class="mt-8 pt-4 border-t border-gray-200">
            <div class="p-3 bg-gradient-to-r from-primary-50 to-primary-100 rounded-xl">
                <h4 class="text-sm font-semibold text-primary-800 mb-1">Manuflow v1.0</h4>
                <p class="text-xs text-primary-600">Manufacturing ERP System</p>
            </div>
        </div>
    </div>
</aside>