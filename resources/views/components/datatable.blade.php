@props([
    'id' => 'datatable',
    'url' => '',
    'columns' => [],
    'actions' => null,
    'filters' => null,
    'exportable' => true
])

<x-card>
    @if($filters)
        <x-slot name="title">
            {{ $attributes->get('title', 'Data Table') }}
        </x-slot>
        
        <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{ $filters }}
        </div>
    @endif

    @if($actions)
        <div class="mb-4 flex justify-between items-center">
            <div>
                @if($exportable)
                    <button type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" onclick="exportTable()">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                        </svg>
                        Export Excel
                    </button>
                @endif
            </div>
            <div class="flex space-x-2">
                {{ $actions }}
            </div>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table id="{{ $id }}" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                {{ $slot }}
            </thead>
        </table>
    </div>
</x-card>

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#{{ $id }}').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ $url }}',
            data: function(d) {
                // Add custom filters here
                @if($filters)
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                @endif
            }
        },
        columns: {!! json_encode($columns) !!},
        dom: 'Bfrtip',
        buttons: [
            @if($exportable)
            {
                extend: 'excel',
                text: 'Export Excel',
                className: 'btn btn-success'
            }
            @endif
        ],
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'desc']],
        language: {
            processing: "Processing...",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _TOTAL_ total entries)",
            loadingRecords: "Loading...",
            zeroRecords: "No matching records found",
            emptyTable: "No data available in table"
        }
    });

    @if($filters)
        // Filter event handlers
        $('#date_from, #date_to').change(function() {
            table.draw();
        });
    @endif
});

@if($exportable)
function exportTable() {
    $('#{{ $id }}').DataTable().button('.buttons-excel').trigger();
}
@endif
</script>
@endpush