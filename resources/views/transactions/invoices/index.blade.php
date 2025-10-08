@extends('layouts.admin')

@section('title','Invoices')

@section('content')
<div class="space-y-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex flex-col space-y-6 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div class="flex-1">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Invoices</h1>
                <div class="mt-3 flex items-center gap-x-4 text-sm text-gray-600">
                    <span class="flex items-center gap-x-1.5">
                        <svg class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="3"/></svg>
                        <span id="invoices-count">0</span> invoices
                    </span>
                    <span class="hidden sm:block">•</span>
                    <span>Manage customer billing</span>
                </div>
            </div>
            <div class="flex items-center gap-x-3">
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-x-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-600 transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Invoice
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table id="dataTable" class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="py-4 pl-6 pr-3 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">#</th>
                        <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Invoice No.</th>
                        <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Date</th>
                        <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Order No.</th>
                        <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Total</th>
                        <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Status</th>
                        <th class="px-3 py-4 text-left text-xs font-semibold text-gray-900 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.tailwindcss.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){
    var table = $('#dataTable').DataTable({
        processing:true,
        serverSide:true,
        ajax:"{{ route('invoices.index') }}",
        columns:[
            {data:'DT_RowIndex',name:'DT_RowIndex',orderable:false,searchable:false},
            {data:'no',name:'no'},
            {data:'dt',name:'dt'},
            {data:'order_no',name:'order.no'},
            {data:'total_formatted',name:'order.total'},
            {data:'status',name:'status',orderable:false,searchable:false},
            {data:'action',name:'action',orderable:false,searchable:false},
        ],
        order:[[2,'desc']],
        drawCallback:function(settings){
            $('#invoices-count').text(settings._iRecordsTotal);
        }
    });

    $(document).on('click','.delete-btn',function(e){
        e.preventDefault();
        var url = $(this).data('url');
        Swal.fire({
            title:'Delete Invoice',
            text:'Are you sure you want to delete this invoice? This action cannot be undone.',
            icon:'warning',
            showCancelButton:true,
            confirmButtonColor:'#ef4444',
            cancelButtonColor:'#6b7280',
            confirmButtonText:'Yes, delete it!'})
        .then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                    url:url,
                    type:'DELETE',
                    data:{_token:$('meta[name="csrf-token"]').attr('content')},
                    success:function(resp){
                        if(resp.success){
                            Swal.fire('Deleted!',resp.message,'success');
                            table.ajax.reload(null,false);
                        } else {
                            Swal.fire('Error',resp.message,'error');
                        }
                    },
                    error:function(xhr){
                        Swal.fire('Error','Failed to delete invoice','error');
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
@endpush