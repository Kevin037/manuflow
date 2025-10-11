@extends('layouts.admin')

@section('title', 'Materials')

@section('content')
<div class="space-y-8">
    <!-- Enhanced Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex flex-col space-y-6 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="flex-1">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Materials</h1>
                <div class="mt-3 flex items-center gap-x-4 text-sm text-gray-600">
                    <span class="flex items-center gap-x-1.5">
                        <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="10" cy="10" r="3"/>
                        </svg>
                        <span id="materials-count">0</span> materials in inventory
                    </span>
                    <span class="hidden sm:block">•</span>
                    <span>Manage your material inventory</span>
                </div>
            </div>
            <div class="flex items-center gap-x-3">
                <a href="{{ route('materials.create') }}" 
                   class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Material
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
                        <select id="supplier_id" name="supplier_id" class="select2 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">All Suppliers</option>
                            @if(isset($suppliers))
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            @endif
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
                        <a id="export-excel" href="{{ route('materials.export.excel') }}" 
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
                <table id="materials-table" class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Material
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Stock & Price
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Supplier
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">
                                Added
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
    const table = $('#materials-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('materials.index') }}',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.supplier_id = $('#supplier_id').val();
            }
        },
        columns: [
            {
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="flex items-center">
                                   <div class="flex-shrink-0">
                                       <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center">
                                           <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                           </svg>
                                       </div>
                                   </div>
                                   <div class="ml-3 min-w-0 flex-1">
                                       <p class="text-sm font-semibold text-gray-900">${data}</p>
                                       <p class="text-sm text-gray-500 truncate">Material ID: ${row.id}</p>
                                   </div>
                               </div>
                            </div>`;
                }
            },
            {
                data: 'qty', 
                name: 'qty',
                render: function(data, type, row) {
                    const stockLevel = data < 10 ? 'text-red-600 bg-red-50' : data < 50 ? 'text-yellow-600 bg-yellow-50' : 'text-green-600 bg-green-50';
                    const stockIcon = data < 10 ? 'text-red-500' : data < 50 ? 'text-yellow-500' : 'text-green-500';
                    
                    return `<div class="py-4 px-6">
                               <div class="space-y-1">
                                   <div class="flex items-center gap-x-2">
                                       <span class="inline-flex items-center gap-x-1 px-2 py-1 text-xs font-medium rounded-full ${stockLevel}">
                                           <svg class="h-3 w-3 ${stockIcon}" fill="currentColor" viewBox="0 0 20 20">
                                               <circle cx="10" cy="10" r="3"/>
                                           </svg>
                                           ${data} units
                                       </span>
                                   </div>
                                   <div class="text-sm font-semibold text-gray-900">
                                       Rp ${new Intl.NumberFormat('id-ID').format(parseFloat(row.price))}
                                   </div>
                               </div>
                            </div>`;
                }
            },
            {
                data: 'supplier_name', 
                name: 'supplier_name',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm">
                                   <div class="flex items-center gap-x-1.5">
                                       <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                       </svg>
                                       <span class="text-gray-900 font-medium">${data || 'No supplier'}</span>
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
                                   <a href="/materials/${data}" 
                                      class="inline-flex items-center gap-x-1.5 rounded-lg bg-gray-50 px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors duration-200">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                       </svg>
                                       View
                                   </a>
                                   <a href="/materials/${data}/edit" 
                                      class="inline-flex items-center gap-x-1.5 rounded-lg bg-primary-50 px-2.5 py-1.5 text-xs font-medium text-primary-600 hover:bg-primary-100 transition-colors duration-200">
                                       <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                       </svg>
                                       Edit
                                   </a>
                                   <button onclick="deleteMaterial(${data})" 
                                           class="inline-flex items-center gap-x-1.5 rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 transition-colors duration-200">
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
            processing: '<div class="flex items-center gap-x-2"><svg class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading materials...</div>',
            emptyTable: '<div class="text-center py-12"><div class="mx-auto h-12 w-12 text-gray-400"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></div><h3 class="mt-2 text-sm font-semibold text-gray-900">No materials found</h3><p class="mt-1 text-sm text-gray-500">Get started by adding a new material to your inventory.</p></div>',
            info: 'Showing _START_ to _END_ of _TOTAL_ materials',
            infoEmpty: 'No materials to show',
            infoFiltered: '(filtered from _MAX_ total materials)'
        },
        drawCallback: function(settings) {
            // Update the materials count
            $('#materials-count').text(settings._iRecordsTotal);
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
        if ($('#supplier_id').val()) params.append('supplier_id', $('#supplier_id').val());
        
        // Add search term
        const searchValue = table.search();
        if (searchValue) params.append('search', searchValue);
        
        // Update export link
        const baseUrl = '{{ route('materials.export.excel') }}';
        const newUrl = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
        $('#export-excel').attr('href', newUrl);
    }

    // Initialize export link with default filters on page load
    updateExportLink();

    // Delete material function with modern alert
    window.deleteMaterial = function(id) {
        Swal.fire({
            title: 'Delete Material',
            text: 'Are you sure you want to delete this material? This action cannot be undone.',
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
                    text: 'Please wait while we delete the material.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/materials/${id}`, {
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
                            text: data.message || 'Material has been deleted successfully.',
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
                            text: data.message || 'Failed to delete material.',
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
                        text: 'An error occurred while deleting the material. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#6366f1',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    };

});
</script>
@endpush