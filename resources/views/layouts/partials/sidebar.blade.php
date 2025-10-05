<aside 
    class="fixed top-0 left-0 z-40 w-sidebar h-screen pt-topbar transition-transform duration-300 ease-in-out bg-white border-r border-gray-200 shadow-lg lg:translate-x-0"
    :class="$store.sidebar.open ? 'translate-x-0' : '-translate-x-full'"
    x-data="{
        masterDataOpen: false,
        productionOpen: false,
        purchasingOpen: false,
        salesOpen: false,
        accountingOpen: false,
        reportsOpen: false
    }"
>
    <!-- Sidebar backdrop for mobile -->
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
    ></div>

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
                    @click="masterDataOpen = !masterDataOpen" 
                    type="button" 
                    class="flex items-center w-full p-3 text-base text-gray-900 transition duration-200 rounded-xl group hover:bg-gray-100 hover:text-primary-600"
                    :class="masterDataOpen ? 'bg-gray-50 text-primary-600' : ''"
                >
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-200 group-hover:text-primary-600" :class="masterDataOpen ? 'text-primary-600' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 21">
                        <path d="M15 12a1 1 0 0 0 .962-.726l2-7A1 1 0 0 0 17 3H3.77L3.175.745A1 1 0 0 0 2.208 0H1a1 1 0 0 0 0 2h.438l.6 2.255v.019l2 7 .746 2.986A3 3 0 1 0 9 17a2.966 2.966 0 0 0-.184-1h2.368c-.118.32-.18.659-.184 1a3 3 0 1 0 3-3H6.78l-.5-2H15Z"/>
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Master Data</span>
                    <svg class="w-3 h-3 transition-transform duration-200" :class="masterDataOpen ? 'rotate-180' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
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
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Products
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Materials
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Suppliers
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Customers
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Formulas
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Production -->
            <li>
                <button 
                    @click="productionOpen = !productionOpen" 
                    type="button" 
                    class="flex items-center w-full p-3 text-base text-gray-900 transition duration-200 rounded-xl group hover:bg-gray-100 hover:text-primary-600"
                    :class="productionOpen ? 'bg-gray-50 text-primary-600' : ''"
                >
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-200 group-hover:text-primary-600" :class="productionOpen ? 'text-primary-600' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.98 2.98 0 0 0 .13 5H5Z"/>
                        <path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/>
                        <path d="M8.961 16a.93.93 0 0 0 .189-.019l3.4-.679a.961.961 0 0 0 .49-.263l6.118-6.117a2.884 2.884 0 0 0-4.079-4.078l-6.117 6.117a.96.96 0 0 0-.263.491l-.679 3.4A.961.961 0 0 0 8.961 16Zm7.477-9.8a.958.958 0 0 1 .68-.281.961.961 0 0 1 .682 1.644l-.315.315-1.36-1.36.313-.318Zm-5.911 5.911 4.236-4.236 1.359 1.359-4.236 4.237-1.7.339.341-1.699Z"/>
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Production</span>
                    <svg class="w-3 h-3 transition-transform duration-200" :class="productionOpen ? 'rotate-180' : ''" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
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
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Production Orders
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
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
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
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
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Orders
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Invoices
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
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
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Chart of Accounts
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Journal Entries
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center w-full p-2 text-gray-600 transition duration-200 rounded-lg pl-11 group hover:bg-primary-50 hover:text-primary-700">
                            Financial Reports
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Reports -->
            <li>
                <a href="#" class="flex items-center p-3 text-gray-900 rounded-xl hover:bg-gray-100 hover:text-primary-600 group transition-all duration-200">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-200 group-hover:text-primary-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.98 2.98 0 0 0 .13 5H5Z"/>
                        <path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/>
                        <path d="M8.961 16a.93.93 0 0 0 .189-.019l3.4-.679a.961.961 0 0 0 .49-.263l6.118-6.117a2.884 2.884 0 0 0-4.079-4.078l-6.117 6.117a.96.96 0 0 0-.263.491l-.679 3.4A.961.961 0 0 0 8.961 16Zm7.477-9.8a.958.958 0 0 1 .68-.281.961.961 0 0 1 .682 1.644l-.315.315-1.36-1.36.313-.318Zm-5.911 5.911 4.236-4.236 1.359 1.359-4.236 4.237-1.7.339.341-1.699Z"/>
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Reports</span>
                </a>
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