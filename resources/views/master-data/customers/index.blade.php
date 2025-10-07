@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex flex-col space-y-6 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="flex-1">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Customers</h1>
                <div class="mt-3 flex items-center gap-x-4 text-sm text-gray-600">
                    <span class="flex items-center gap-x-1.5">
                        <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        <span id="customers-count">0</span> active customers
                    </span>
                    <span class="hidden sm:block">•</span>
                    <span>Manage your customer database</span>
                </div>
            </div>
            <div class="flex items-center gap-x-3">
                <a href="{{ route('customers.create') }}" 
                   class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Customer
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Data Table Card -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
        <div class="relative">
            <!-- Table -->
            <div class="overflow-x-auto">
                <table id="customers-table" class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Customer
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Contact
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Joined
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#customers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('customers.index') }}',
        columns: [
            {
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="flex items-center">
                                   <div class="flex-shrink-0">
                                       <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-sm">
                                           <span class="text-sm font-semibold text-white">${data.charAt(0).toUpperCase()}</span>
                                       </div>
                                   </div>
                                   <div class="ml-3 min-w-0 flex-1">
                                       <p class="text-sm font-semibold text-gray-900 truncate">${data}</p>
                                       <p class="text-xs text-gray-500 mt-1">Customer ID: ${row.id}</p>
                                   </div>
                               </div>
                            </div>`;
                }
            },
            {
                data: 'phone', 
                name: 'phone',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm text-gray-900">
                                   <div class="flex items-center gap-x-2">
                                       <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                       </svg>
                                       <span>${data}</span>
                                   </div>
                               </div>
                            </div>`;
                }
            },
            {
                data: 'created_at', 
                name: 'created_at',
                render: function(data, type, row) {
                    const date = new Date(data);
                    return `<div class="py-4 px-6">
                               <div class="text-sm text-gray-500">
                                   ${date.toLocaleDateString('id-ID', { 
                                       year: 'numeric', 
                                       month: 'short', 
                                       day: 'numeric' 
                                   })}
                               </div>
                            </div>`;
                }
            },
            {
                data: 'id', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="flex justify-end gap-x-2">
                                   <a href="/customers/${data}" 
                                      class="inline-flex items-center gap-x-1.5 rounded-lg bg-gray-50 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                       </svg>
                                       View
                                   </a>
                                   <a href="/customers/${data}/edit" 
                                      class="inline-flex items-center gap-x-1.5 rounded-lg bg-primary-50 px-3 py-2 text-xs font-medium text-primary-600 hover:bg-primary-100 transition-colors duration-200">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                       </svg>
                                       Edit
                                   </a>
                                   <button onclick="deleteCustomer(${data})" 
                                           class="inline-flex items-center gap-x-1.5 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors duration-200">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                       </svg>
                                       Delete
                                   </button>
                               </div>
                            </div>`;
                }
            }
        ],
        responsive: true,
        order: [[0, 'asc']],
        pageLength: 25,
        dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-b border-gray-200"<"flex items-center gap-x-4"l><"flex items-center gap-x-4"f>>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-t border-gray-200"<"text-sm text-gray-700"i><"ml-auto"p>>',
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: 'Search:',
            processing: '<div class="flex items-center gap-x-2"><svg class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading customers...</div>',
            emptyTable: '<div class="text-center py-12"><div class="mx-auto h-12 w-12 text-gray-400"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div><h3 class="mt-2 text-sm font-semibold text-gray-900">No customers found</h3><p class="mt-1 text-sm text-gray-500">Get started by creating a new customer.</p></div>',
            info: 'Showing _START_ to _END_ of _TOTAL_ customers',
            infoEmpty: 'No customers to show',
            infoFiltered: '(filtered from _MAX_ total customers)'
        },
        drawCallback: function(settings) {
            // Update the customers count
            $('#customers-count').text(settings._iRecordsTotal);
        }
    });

    // Delete customer function with modern alert
    window.deleteCustomer = function(id) {
        Swal.fire({
            title: 'Delete Customer',
            text: 'Are you sure you want to delete this customer? This action cannot be undone.',
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
                    text: 'Please wait while we delete the customer.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Use jQuery AJAX for more reliable handling
                $.ajax({
                    url: `/customers/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    beforeSend: function() {
                        console.log('Sending DELETE request for customer ID:', id);
                        console.log('URL:', `/customers/${id}`);
                        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
                    },
                    success: function(response, textStatus, xhr) {
                        console.log('AJAX Success!');
                        console.log('Response:', response);
                        console.log('Status:', textStatus);
                        console.log('XHR Status:', xhr.status);
                        
                        // Reload the table data
                        table.ajax.reload(null, false);
                        
                        // Show success message
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message || 'Customer has been deleted successfully.',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.log('AJAX Error!');
                        console.log('XHR object:', xhr);
                        console.log('Status Code:', xhr.status);
                        console.log('Status Text:', xhr.statusText);
                        console.log('Response Text:', xhr.responseText);
                        console.log('Text Status:', textStatus);
                        console.log('Error Thrown:', errorThrown);
                        console.log('Ready State:', xhr.readyState);
                        
                        let errorMessage = 'An error occurred while deleting the customer.';
                        
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse && errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                        } catch (e) {
                            console.log('Could not parse error response as JSON:', e);
                        }
                        
                        if (xhr.status === 404) {
                            errorMessage = 'Customer not found.';
                        } else if (xhr.status === 403) {
                            errorMessage = 'You do not have permission to delete this customer.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error occurred while deleting the customer.';
                        } else if (xhr.status === 419) {
                            errorMessage = 'CSRF token mismatch. Please refresh the page and try again.';
                        } else if (xhr.status === 0) {
                            errorMessage = 'Network error. Please check your connection and try again.';
                        }
                        
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#6366f1',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    };

    // Debug function - you can call this in browser console to test
    window.debugDelete = function(id) {
        console.log('Testing delete for customer ID:', id);
        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: `/customers/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Debug Success:', response);
            },
            error: function(xhr, status, error) {
                console.log('Debug Error:', xhr.responseText);
            }
        });
    };

});
</script>
@endpush