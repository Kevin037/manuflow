@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Product Details</h2>
        <p class="text-gray-600">View product information and formula details</p>
    </div>
    <div class="flex space-x-3">
        <a href="{{ route('products.edit', $product) }}" 
           class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Product
        </a>
        <a href="{{ route('products.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Products
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product Information -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Product Information</h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- Product Photo -->
                <div class="text-center">
                    @if($product->photo && Storage::disk('public')->exists($product->photo))
                        <img src="{{ Storage::url($product->photo) }}" alt="{{ $product->name }}" class="mx-auto h-48 w-48 object-cover rounded-lg border border-gray-200">
                    @else
                        <div class="mx-auto h-48 w-48 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                    <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $product->name }}</div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <div class="text-sm font-mono text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $product->sku }}</div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <div class="text-lg font-bold text-green-600 bg-green-50 px-3 py-2 rounded-lg">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                    <div class="text-sm font-medium px-3 py-2 rounded-lg {{ $product->qty > 10 ? 'text-green-600 bg-green-50' : ($product->qty > 0 ? 'text-yellow-600 bg-yellow-50' : 'text-red-600 bg-red-50') }}">
                        {{ number_format($product->qty, 0) }} units
                        @if($product->qty <= 5)
                            <span class="text-xs">(Low Stock)</span>
                        @elseif($product->qty == 0)
                            <span class="text-xs">(Out of Stock)</span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Inventory Value</label>
                    <div class="text-lg font-bold text-blue-600 bg-blue-50 px-3 py-2 rounded-lg">
                        Rp {{ number_format($product->price * $product->qty, 0, ',', '.') }}
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Created Date</label>
                    <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $product->created_at->format('d M Y H:i') }}</div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Updated</label>
                    <div class="text-sm text-gray-900 bg-gray-50 px-3 py-2 rounded-lg">{{ $product->updated_at->format('d M Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formula Details -->
    <div class="lg:col-span-2">
        @if($product->formula)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Formula Details</h3>
                        <p class="text-sm text-gray-600 mt-1">Materials and costs for this product</p>
                    </div>
                    <a href="{{ route('formulas.show', $product->formula) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                        View Full Formula →
                    </a>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Formula Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm font-medium text-gray-700">Formula Code</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $product->formula->no }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm font-medium text-gray-700">Formula Name</div>
                        <div class="text-lg font-semibold text-gray-900">{{ $product->formula->name }}</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-sm font-medium text-gray-700">Formula Cost</div>
                        <div class="text-lg font-semibold text-green-600">{{ $product->formula->total_formatted }}</div>
                    </div>
                </div>

                <!-- Materials List -->
                @if($product->formula->formulaDetails && $product->formula->formulaDetails->count() > 0)
                <div>
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Materials Breakdown</h4>
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($product->formula->formulaDetails as $detail)
                                <tr class="hover:bg-gray-50">
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
                                            {{ number_format($detail->qty, 2) }} {{ $detail->material->unit ?? 'pcs' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $detail->subtotal_formatted }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total Formula Cost:</td>
                                    <td class="px-6 py-4 text-sm font-bold text-green-600">{{ $product->formula->total_formatted }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Cost Analysis -->
                <div>
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Cost Analysis</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-700">Production Cost per Unit</div>
                            <div class="text-2xl font-bold text-blue-900">{{ $product->formula->total_formatted }}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-700">Selling Price per Unit</div>
                            <div class="text-2xl font-bold text-green-900">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="bg-{{ $product->price > $product->formula->total ? 'green' : 'red' }}-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-{{ $product->price > $product->formula->total ? 'green' : 'red' }}-700">Profit per Unit</div>
                            <div class="text-2xl font-bold text-{{ $product->price > $product->formula->total ? 'green' : 'red' }}-900">
                                Rp {{ number_format($product->price - $product->formula->total, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-purple-700">Profit Margin</div>
                            <div class="text-2xl font-bold text-purple-900">
                                {{ $product->formula->total > 0 ? number_format((($product->price - $product->formula->total) / $product->price) * 100, 1) : '0' }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-12 text-center">
                <div class="text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-900">No formula assigned</p>
                    <p class="text-sm text-gray-500 mt-1">This product doesn't have a formula assigned yet.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection