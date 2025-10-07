@extends('layouts.admin')

@section('title', 'Formula Details')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Formula Details</h2>
        <p class="text-gray-600">View formula information and materials</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('formulas.edit', $formula) }}" 
           class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Formula
        </a>
        <a href="{{ route('formulas.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Formulas
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Formula Information -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Formula Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Formula Code</label>
                    <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $formula->no }}</div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Formula Name</label>
                    <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $formula->name }}</div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Materials</label>
                    <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $formula->formulaDetails->count() }} material(s)</div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Cost</label>
                    <div class="text-lg font-bold text-green-600 bg-green-50 px-3 py-2 rounded-lg">{{ $formula->total_formatted }}</div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Created Date</label>
                    <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $formula->created_at->format('d M Y H:i') }}</div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Updated</label>
                    <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $formula->updated_at->format('d M Y H:i') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Products using this formula -->
        @if($formula->products->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Products Using This Formula</h3>
                <p class="text-sm text-gray-600 mt-1">{{ $formula->products->count() }} product(s)</p>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($formula->products as $product)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $product->no }}</div>
                        </div>
                        <a href="{{ route('products.show', $product) }}" class="text-primary-600 hover:text-primary-700 text-xs font-medium">
                            View →
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Materials List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Materials Breakdown</h3>
                <p class="text-sm text-gray-600 mt-1">Detailed list of materials and their quantities</p>
            </div>
            
            @if($formula->formulaDetails->count() > 0)
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($formula->formulaDetails as $index => $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $detail->material->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $detail->material->no }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail->material->formatted_price }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ number_format($detail->qty, 2) }} {{ $detail->material->unit }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $detail->subtotal_formatted }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $formula->total > 0 ? ($detail->subtotal / $formula->total * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-900">{{ $formula->total > 0 ? number_format($detail->subtotal / $formula->total * 100, 1) : 0 }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total Cost:</td>
                            <td class="px-6 py-4 text-sm font-bold text-green-600">{{ $formula->total_formatted }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="p-12 text-center">
                <div class="text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-900">No materials found</p>
                    <p class="text-sm text-gray-500 mt-1">This formula doesn't have any materials yet.</p>
                </div>
            </div>
            @endif
        </div>
        
        <!-- Cost Breakdown Chart -->
        @if($formula->formulaDetails->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mt-6">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Cost Distribution</h3>
                <p class="text-sm text-gray-600 mt-1">Visual breakdown of material costs</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($formula->formulaDetails->sortByDesc('subtotal') as $detail)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ ['#6366f1', '#8b5cf6', '#ec4899', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'][$loop->index % 7] }}"></div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $detail->material->name }}</div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="h-2 rounded-full" 
                                         style="width: {{ $formula->total > 0 ? ($detail->subtotal / $formula->total * 100) : 0 }}%; background-color: {{ ['#6366f1', '#8b5cf6', '#ec4899', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'][$loop->index % 7] }}"></div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 text-right">
                            <div class="text-sm font-medium text-gray-900">{{ $detail->subtotal_formatted }}</div>
                            <div class="text-xs text-gray-500">{{ $formula->total > 0 ? number_format($detail->subtotal / $formula->total * 100, 1) : 0 }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection