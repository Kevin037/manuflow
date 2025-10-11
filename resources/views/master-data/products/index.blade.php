@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Products</h2>
        <p class="text-gray-600">Manage your product inventory</p>
    </div>
    <a href="{{ route('products.create') }}" 
       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Product
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    Total: <span id="products-count" class="font-medium text-gray-900">Loading...</span> products
                </div>
            </div>
        </div>

        <!-- Filters -->
        <form id="filter-form" class="flex flex-col sm:flex-row items-start sm:items-end gap-4 mb-6">
            <div class="flex-1 min-w-0">
                <input type="date" id="start_date" name="start_date" value="{{ date('Y-m-d', strtotime('-1 month')) }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div class="flex-1 min-w-0">
                <input type="date" id="end_date" name="end_date" value="{{ date('Y-m-d') }}"
                       class="w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
            </div>
            <div class="flex-1 min-w-0">
                <select id="formula_id" name="formula_id" class="select2 w-full rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">All Formulas</option>
                    @if(isset($formulas))
                        @foreach($formulas as $formula)
                            <option value="{{ $formula->id }}">{{ $formula->name }}</option>
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
                    Apply
                </button>
                <a id="export-excel" href="{{ route('products.export.excel') }}" 
                   class="inline-flex items-center gap-x-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export Excel
                </a>
            </div>
        </form>

        <div class="overflow-hidden">
            <table id="products-table" class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Photo</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock (qty)</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formula</th>
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
    const table = $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('products.index') }}',
            data: function (d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d.formula_id = $('#formula_id').val();
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
                               <div class="text-sm text-gray-900">${data}</div>
                            </div>`;
                }
            },
            {
                data: 'sku', 
                name: 'sku',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm font-mono font-medium text-gray-900">${data}</div>
                            </div>`;
                }
            },
            {
                data: 'photo', 
                name: 'photo',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               ${data}
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
                data: 'qty', 
                name: 'qty',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               ${data}
                            </div>`;
                }
            },
            {
                data: 'formatted_price', 
                name: 'price',
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm font-semibold text-green-600">${data}</div>
                            </div>`;
                }
            },
            {
                data: 'formula_name', 
                name: 'formula_name',
                orderable: false,
                render: function(data, type, row) {
                    return `<div class="py-4 px-6">
                               <div class="text-sm text-gray-900">${data}</div>
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
                               ${data}
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
            processing: '<div class="flex items-center gap-x-2"><svg class="animate-spin h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Loading products...</div>',
            emptyTable: '<div class="text-center py-12"><div class="mx-auto h-12 w-12 text-gray-400"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></div><h3 class="mt-2 text-sm font-semibold text-gray-900">No products found</h3><p class="mt-1 text-sm text-gray-500">Get started by creating a new product.</p></div>',
            info: 'Showing _START_ to _END_ of _TOTAL_ products',
            infoEmpty: 'No products to show',
            infoFiltered: '(filtered from _MAX_ total products)'
        },
        drawCallback: function(settings) {
            // Update the products count
            $('#products-count').text(settings._iRecordsTotal);
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
        if ($('#formula_id').val()) params.append('formula_id', $('#formula_id').val());
        
        // Add search term
        const searchValue = table.search();
        if (searchValue) params.append('search', searchValue);
        
        // Update export link
        const baseUrl = '{{ route('products.export.excel') }}';
        const newUrl = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
        $('#export-excel').attr('href', newUrl);
    }

    // Initialize export link with default filters on page load
    updateExportLink();

    // Delete product function with modern alert
    window.deleteProduct = function(id) {
        Swal.fire({
            title: 'Delete Product',
            text: 'Are you sure you want to delete this product? This action cannot be undone.',
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
                    text: 'Please wait while we delete the product.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/products/${id}`, {
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
                            text: data.message || 'Product has been deleted successfully.',
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
                            text: data.message || 'Failed to delete product.',
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
                        text: 'An error occurred while deleting the product. Please try again.',
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
        $('#products-count').text('Loading...');
    });
});
</script>
@endpush