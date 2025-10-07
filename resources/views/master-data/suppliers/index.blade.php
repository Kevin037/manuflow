@extends('layouts.admin')

@section('title', 'Suppliers')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Suppliers</h1>
            <p class="mt-2 text-sm text-gray-600">Manage your suppliers and their information</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('suppliers.create') }}" 
               class="inline-flex items-center gap-x-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Supplier
            </a>
        </div>
    </div>
</div>

<!-- Suppliers Table Card -->
<div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">All Suppliers</h2>
            <div class="flex items-center gap-x-3">
                <!-- Search will be handled by DataTables -->
                <div class="text-sm text-gray-500">
                    <span id="suppliers-count">Loading...</span> suppliers
                </div>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-hidden">
        <table id="suppliers-table" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <div class="flex items-center gap-x-2">
                            <span>Name</span>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <div class="flex items-center gap-x-2">
                            <span>Phone Number</span>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <div class="flex items-center gap-x-2">
                            <span>Added</span>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- DataTables will populate this -->
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
.dataTables_wrapper {
    padding: 0;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 0;
}

.dataTables_wrapper .dataTables_length select {
    @apply rounded-lg border-gray-300 py-2 px-3 text-sm focus:border-primary-500 focus:ring-primary-500;
}

.dataTables_wrapper .dataTables_filter input {
    @apply rounded-lg border-gray-300 py-2 px-3 text-sm focus:border-primary-500 focus:ring-primary-500;
    margin-left: 0.5rem;
}

.dataTables_wrapper .dataTables_info {
    @apply text-sm text-gray-700;
}

.dataTables_wrapper .dataTables_paginate {
    @apply flex items-center gap-x-1;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    @apply px-3 py-2 mx-0.5 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200;
    background: white !important;
    border: 1px solid #d1d5db !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    @apply bg-primary-600 border-primary-600 text-white hover:bg-primary-700;
    background: rgb(79 70 229) !important;
    border-color: rgb(79 70 229) !important;
    color: white !important;
}

table.dataTable tbody td {
    padding: 0 !important;
    border-top: 1px solid #f3f4f6 !important;
}

table.dataTable thead th {
    padding: 1rem 1.5rem !important;
    border-bottom: 1px solid #e5e7eb !important;
    background-color: #f9fafb !important;
}

table.dataTable tbody tr {
    min-height: 4rem;
}

table.dataTable tbody tr:hover {
    background-color: #f9fafb;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#suppliers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('suppliers.index') }}',
        columns: [
            {
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="min-w-0 flex-1">
                                   <p class="text-sm font-semibold text-gray-900 truncate">${data}</p>
                                   <p class="text-xs text-gray-500 mt-1">ID: ${row.id}</p>
                               </div>
                            </div>`;
                }
            },
            {
                data: 'phone', 
                name: 'phone',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm text-gray-900">${data}</div>
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
                data: 'action', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="flex justify-end gap-x-2">
                                   <a href="/suppliers/${row.id}" 
                                      class="inline-flex items-center gap-x-1.5 rounded-lg bg-gray-50 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                       </svg>
                                       View
                                   </a>
                                   <a href="/suppliers/${row.id}/edit" 
                                      class="inline-flex items-center gap-x-1.5 rounded-lg bg-primary-50 px-3 py-2 text-xs font-medium text-primary-600 hover:bg-primary-100 transition-colors duration-200">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                       </svg>
                                       Edit
                                   </a>
                                   <button onclick="deleteSupplier(${row.id})" 
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
            processing: 'Loading suppliers...',
            emptyTable: 'No suppliers found',
            info: 'Showing _START_ to _END_ of _TOTAL_ suppliers',
            infoEmpty: 'No suppliers to show',
            infoFiltered: '(filtered from _MAX_ total suppliers)'
        },
        drawCallback: function(settings) {
            $('#suppliers-count').text(settings._iRecordsTotal);
        }
    });

    // Delete supplier function with modern alert
    window.deleteSupplier = function(id) {
        Swal.fire({
            title: 'Delete Supplier',
            text: 'Are you sure you want to delete this supplier? This action cannot be undone.',
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
                    text: 'Please wait while we delete the supplier.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/suppliers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    
                    if (data.success === true) {
                        // Reload the table data
                        table.ajax.reload(null, false); // false to keep paging position
                        
                        // Show success message
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message || 'Supplier has been deleted successfully.',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end',
                            background: '#f0fdf4',
                            color: '#15803d'
                        });
                    } else {
                        // Handle server-side error
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to delete supplier.',
                            icon: 'error',
                            confirmButtonColor: '#6366f1',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the supplier. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#6366f1',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    };

    // Show loading state on page load
    $(document).on('DOMContentLoaded', function() {
        $('#suppliers-count').text('Loading...');
    });
});
</script>
@endpush