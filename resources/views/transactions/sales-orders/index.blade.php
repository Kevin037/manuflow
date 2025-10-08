@extends('layouts.admin')

@section('title', 'Sales Orders')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex flex-col space-y-6 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="flex-1">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Sales Orders</h1>
                <div class="mt-3 flex items-center gap-x-4 text-sm text-gray-600">
                    <span class="flex items-center gap-x-1.5">
                        <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        <span id="sales-orders-count">0</span> sales orders
                    </span>
                    <span class="hidden sm:block">•</span>
                    <span>Manage customer orders</span>
                </div>
            </div>
            <div class="flex items-center gap-x-3">
                <a href="{{ route('sales-orders.create') }}" 
                   class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Sales Order
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Data Table Card -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
        <div class="relative">
            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="dataTable" class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                #
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Order No
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Customer
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Product Quantity
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
        ajax: "{{ route('sales-orders.index') }}",
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
                                   <div class="text-gray-500">Sales Order</div>
                               </div>
                            </div>`;
                }
            },
            {
                data: 'customer_name', 
                name: 'customer.name',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm">
                                   <div class="flex items-center gap-x-1.5">
                                       <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                       </svg>
                                       <span class="text-gray-900 font-medium">${data || 'No customer'}</span>
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
                data: 'product_quantity', 
                name: 'product_quantity',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm text-gray-900">${data || '-'}</div>
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
        dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-b border-gray-200"<"flex items-center gap-x-4"l><"flex items-center gap-x-4"f>>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-t border-gray-200"<"text-sm text-gray-700"i><"ml-auto"p>>',
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: 'Search:',
            processing: '<div class="flex items-center gap-x-2"><svg class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading sales orders...</div>',
            emptyTable: '<div class="text-center py-12"><div class="mx-auto h-12 w-12 text-gray-400"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1v-7a1 1 0 011-1z"></path></svg></div><h3 class="mt-2 text-sm font-semibold text-gray-900">No sales orders found</h3><p class="mt-1 text-sm text-gray-500">Get started by creating a new sales order.</p></div>',
            info: 'Showing _START_ to _END_ of _TOTAL_ sales orders',
            infoEmpty: 'No sales orders to show',
            infoFiltered: '(filtered from _MAX_ total sales orders)'
        },
        drawCallback: function(settings) {
            // Update the sales orders count
            $('#sales-orders-count').text(settings._iRecordsTotal);
        }
    });

    // Delete handler
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        var url = $(this).data('url');
        
        Swal.fire({
            title: 'Delete Sales Order',
            text: 'Are you sure you want to delete this sales order? This action cannot be undone.',
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
                    text: 'Please wait while we delete the sales order.',
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
            title: 'Complete Sales Order?',
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
                    text: 'Please wait while we complete the sales order.',
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