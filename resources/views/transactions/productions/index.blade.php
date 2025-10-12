@extends('layouts.admin')

@section('title', 'Production Orders')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex flex-col space-y-6 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="flex-1">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Production Orders</h1>
                <div class="mt-3 flex items-center gap-x-4 text-sm text-gray-600">
                    <span class="flex items-center gap-x-1.5">
                        <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        <span id="production-orders-count">0</span> production orders
                    </span>
                    <span class="hidden sm:block">•</span>
                    <span>Manage manufacturing operations</span>
                </div>
            </div>
            <div class="flex items-center gap-x-3">
                <a href="{{ route('productions.create') }}" 
                   class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Production Order
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Data Table Card -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
        <div class="relative">
            <!-- Filters -->
            <div class="px-6 py-4 border-b border-gray-200">
                <form id="filter-form" class="flex flex-col sm:flex-row items-start sm:items-end gap-4">
                    <div class="flex-1 min-w-0">
                        <input type="date" id="start_date" name="start_date" value="{{ date('Y-m-d', strtotime('-1 month')) }}"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="flex-1 min-w-0">
                        <input type="date" id="end_date" name="end_date" value="{{ date('Y-m-d') }}"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div class="flex-1 min-w-0">
                        <select id="status" name="status" class="select2 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="flex gap-x-2 flex-shrink-0">
                        <button type="submit" data-no-spinner
                                class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"/>
                            </svg>
                            Filter
                        </button>
                        <a id="export-excel" href="{{ route('productions.export.excel') }}" 
                           class="inline-flex items-center gap-x-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 transition-all duration-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="dataTable" class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                #
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Production No
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Product
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="relative py-4 pl-3 pr-6">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    var table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('productions.index') }}",
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.status = $('#status').val();
            }
        },
        columns: [
            {
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm font-medium text-gray-900">${data}</div>
                            </div>`;
                }
            },
            {
                data: 'no', 
                name: 'no',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm">
                                   <div class="font-medium text-gray-900">${data}</div>
                                   <div class="text-gray-500">Production Order</div>
                               </div>
                            </div>`;
                }
            },
            {
                data: 'product_name', 
                name: 'product.name',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm">
                                   <div class="flex items-center gap-x-1.5">
                                       <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                       </svg>
                                       <span class="text-gray-900 font-medium">${data || 'No product'}</span>
                                   </div>
                               </div>
                            </div>`;
                }
            },
            {
                data: 'dt', 
                name: 'dt',
                render: function(data, type, row) {
                    const date = new Date(data);
                    const formattedDate = date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    return `<div class="py-4 px-6">
                               <div class="text-sm text-gray-900">${formattedDate}</div>
                            </div>`;
                }
            },
            {
                data: 'qty', 
                name: 'qty',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm">
                                   <div class="font-semibold text-primary-600">${parseFloat(data).toLocaleString('id-ID')} units</div>
                               </div>
                            </div>`;
                }
            },
            {
                data: 'status', 
                name: 'status', 
                orderable: false,
                render: function(data, type, row) {
                    let statusClass = '';
                    let statusText = data;
                    
                    switch(data) {
                        case 'pending':
                            statusClass = 'bg-yellow-100 text-yellow-800';
                            statusText = 'Pending';
                            break;
                        case 'completed':
                            statusClass = 'bg-green-100 text-green-800';
                            statusText = 'Completed';
                            break;
                        default:
                            statusClass = 'bg-gray-100 text-gray-800';
                    }
                    
                    return `<div class="py-4 px-6">
                               <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                   ${statusText}
                               </span>
                            </div>`;
                }
            },
            {
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">${data}</div>`;
                }
            }
        ],
        responsive: true,
        order: [[3, 'desc']],
        pageLength: 25,
    dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-b border-gray-200"<"flex items-center gap-x-4"l><"flex items-center gap-x-4 mt-3 sm:mt-0"f>>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-t border-gray-200"<"text-sm text-gray-700"i><"ml-auto"p>>',
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: 'Search:',
            processing: '<div class="flex items-center gap-x-2"><svg class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading production orders...</div>',
            emptyTable: '<div class="text-center py-12"><div class="mx-auto h-12 w-12 text-gray-400"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg></div><h3 class="mt-2 text-sm font-semibold text-gray-900">No production orders found</h3><p class="mt-1 text-sm text-gray-500">Get started by creating a new production order.</p></div>',
            info: 'Showing _START_ to _END_ of _TOTAL_ production orders',
            infoEmpty: 'No production orders to show',
            infoFiltered: '(filtered from _MAX_ total production orders)'
        },
        drawCallback: function(settings) {
            // Update the production orders count
            $('#production-orders-count').text(settings._iRecordsTotal);
        }
    });

    // Handle filter form submission
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        table.ajax.reload();
        updateExportLink();
    });

    // Handle search to update export link
    table.on('search.dt', function() {
        updateExportLink();
    });

    // Function to update export link with current filters and search
    function updateExportLink() {
        const params = new URLSearchParams();
        
        // Add date filters
        if ($('#start_date').val()) params.append('start_date', $('#start_date').val());
        if ($('#end_date').val()) params.append('end_date', $('#end_date').val());
        if ($('#status').val()) params.append('status', $('#status').val());
        
        // Add search term
        const searchValue = table.search();
        if (searchValue) params.append('search', searchValue);
        
        // Update export link
        const baseUrl = '{{ route('productions.export.excel') }}';
        const newUrl = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
        $('#export-excel').attr('href', newUrl);
    }

    // Initialize export link with default filters on page load
    updateExportLink();

    // Delete handler
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        
        Swal.fire({
            title: 'Delete Production Order',
            text: 'Are you sure you want to delete this production order? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition mr-3',
                cancelButton: 'inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 active:bg-gray-600 disabled:opacity-25 transition'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the production order.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload the table data
                            table.ajax.reload(null, false);
                            
                            // Show success message
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#10b981'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Something went wrong!',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    });

    // Complete handler
    $(document).on('click', '.complete-btn', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        
        Swal.fire({
            title: 'Complete Production Order?',
            text: "This will update inventory and cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, complete it!',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 active:bg-green-600 disabled:opacity-25 transition mr-3',
                cancelButton: 'inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 active:bg-gray-600 disabled:opacity-25 transition'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Completing...',
                    text: 'Please wait while we complete the production order.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reload the table data
                            table.ajax.reload(null, false);
                            
                            // Show success message
                            Swal.fire({
                                title: 'Completed!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#10b981'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON;
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Something went wrong!',
                            icon: 'error',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css" rel="stylesheet">
<style>
/* Custom DataTables Styling */
.dataTables_wrapper {
    padding: 0;
}

table.dataTable tbody td {
    padding: 0 !important;
}

table.dataTable thead th {
    padding: 1rem 1.5rem !important;
}

table.dataTable tbody tr {
    min-height: 64px;
}

table.dataTable tbody tr:hover {
    background-color: #f9fafb !important;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 0;
}

.dataTables_wrapper .dataTables_length select {
    @apply rounded-lg border-gray-300 py-1.5 px-3 text-sm focus:border-primary-500 focus:ring-primary-500;
}

.dataTables_wrapper .dataTables_filter input {
    @apply rounded-lg border-gray-300 py-1.5 px-3 text-sm focus:border-primary-500 focus:ring-primary-500;
    margin-left: 0.5rem;
}

.dataTables_wrapper .dataTables_info {
    @apply text-sm text-gray-700 pt-4;
}

.dataTables_wrapper .dataTables_paginate {
    @apply pt-4;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    @apply px-3 py-1.5 mx-0.5 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200;
    background: white !important;
    border: 1px solid #d1d5db !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    @apply bg-primary-600 border-primary-600 text-white hover:bg-primary-700;
    background: rgb(79 70 229) !important;
    border-color: rgb(79 70 229) !important;
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    @apply bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed;
    background: #f3f4f6 !important;
    border-color: #e5e7eb !important;
}

.dataTables_wrapper .dataTables_processing {
    @apply bg-white bg-opacity-90;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    border: none !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    padding: 1rem 2rem !important;
    border-radius: 0.5rem !important;
}

.dt-buttons {
    @apply mb-4;
}

.dt-button {
    @apply bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors duration-200 mr-2;
}
</style>
@endpush