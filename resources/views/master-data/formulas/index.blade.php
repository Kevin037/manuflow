@extends('layouts.admin')

@section('title', 'Formulas')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Formulas</h2>
        <p class="text-gray-600">Manage your product formulas</p>
    </div>
    <a href="{{ route('formulas.create') }}" 
       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Formula
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    Total: <span id="formulas-count" class="font-medium text-gray-900">Loading...</span> formulas
                </div>
            </div>
        </div>

        <div class="overflow-hidden">
            <table id="formulas-table" class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formula Code</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formula Name</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost (Total)</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materials</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.dataTables_wrapper .dataTables_length select {
    @apply px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500;
}

.dataTables_wrapper .dataTables_filter input {
    @apply px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500;
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
    const table = $('#formulas-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('formulas.index') }}',
        columns: [
            {
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm text-gray-900">${data}</div>
                            </div>`;
                }
            },
            {
                data: 'no', 
                name: 'no',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm font-medium text-gray-900">${data}</div>
                            </div>`;
                }
            },
            {
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm font-medium text-gray-900">${data}</div>
                            </div>`;
                }
            },
            {
                data: 'total_formatted', 
                name: 'total',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm font-semibold text-green-600">${data}</div>
                            </div>`;
                }
            },
            {
                data: 'materials_count', 
                name: 'materials_count',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm text-gray-900">${data} material(s)</div>
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
                                   <a href="/formulas/${row.id}" 
                                      class="inline-flex items-center gap-x-1.5 rounded-lg bg-gray-50 px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                       </svg>
                                       View
                                   </a>
                                   <a href="/formulas/${row.id}/edit" 
                                      class="inline-flex items-center gap-x-1.5 rounded-lg bg-primary-50 px-3 py-2 text-xs font-medium text-primary-600 hover:bg-primary-100 transition-colors duration-200">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                       </svg>
                                       Edit
                                   </a>
                                   <button onclick="deleteFormula(${row.id})" 
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
        order: [[1, 'asc']],
        pageLength: 25,
        dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-b border-gray-200"<"flex items-center gap-x-4"l><"flex items-center gap-x-4"f>>rt<"flex flex-col sm:flex-row sm:items-center sm:justify-between p-6 border-t border-gray-200"<"text-sm text-gray-700"i><"ml-auto"p>>',
        language: {
            lengthMenu: 'Show _MENU_ entries',
            search: 'Search:',
            processing: '<div class="flex items-center gap-x-2"><svg class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading formulas...</div>',
            emptyTable: '<div class="text-center py-12"><div class="mx-auto h-12 w-12 text-gray-400"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div><h3 class="mt-2 text-sm font-semibold text-gray-900">No formulas found</h3><p class="mt-1 text-sm text-gray-500">Get started by creating a new formula.</p></div>',
            info: 'Showing _START_ to _END_ of _TOTAL_ formulas',
            infoEmpty: 'No formulas to show',
            infoFiltered: '(filtered from _MAX_ total formulas)'
        },
        drawCallback: function(settings) {
            // Update the formulas count
            $('#formulas-count').text(settings._iRecordsTotal);
        }
    });

    // Delete formula function with modern alert
    window.deleteFormula = function(id) {
        Swal.fire({
            title: 'Delete Formula',
            text: 'Are you sure you want to delete this formula? This action cannot be undone.',
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
                    text: 'Please wait while we delete the formula.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/formulas/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success === true) {
                        // Reload the table data
                        table.ajax.reload(null, false);
                        
                        // Show success message
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message || 'Formula has been deleted successfully.',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end',
                            background: '#f0fdf4',
                            color: '#15803d'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to delete formula.',
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
                        text: 'An error occurred while deleting the formula. Please try again.',
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
        $('#formulas-count').text('Loading...');
    });
});
</script>
@endpush