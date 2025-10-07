@extends('layouts.admin')

@section('title', 'Edit Formula')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Edit Formula</h2>
        <p class="text-gray-600">Update formula: {{ $formula->name }}</p>
    </div>
    <a href="{{ route('formulas.index') }}" 
       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Formulas
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <form action="{{ route('formulas.update', $formula) }}" method="POST" id="formula-form" x-data="formulaForm()">
        @csrf
        @method('PUT')
        
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Formula Information</h3>
            <p class="text-sm text-gray-600 mt-1">Update the formula details</p>
        </div>

        <div class="p-6 space-y-6">
            <!-- Formula Code -->
            <div>
                <label for="no" class="block text-sm font-medium text-gray-700 mb-2">
                    Formula Code <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="no" 
                       name="no" 
                       value="{{ old('no', $formula->no) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('no') border-red-500 ring-red-500 @enderror"
                       placeholder="Enter formula code (e.g., F001)">
                @error('no')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Formula Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Formula Name <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $formula->name) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors duration-200 @error('name') border-red-500 ring-red-500 @enderror"
                       placeholder="Enter formula name">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Materials Section -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Materials <span class="text-red-500">*</span>
                        </label>
                        <p class="text-sm text-gray-600 mt-1">Update materials and quantities for this formula</p>
                    </div>
                    <button type="button" 
                            @click="addMaterial()"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Material
                    </button>
                </div>

                @error('materials')
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    </div>
                @enderror

                <!-- Materials Table -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="materials-table-body">
                            <template x-for="(material, index) in materials" :key="index">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="index + 1"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select :name="`materials[${index}][material_id]`" 
                                                x-model="material.material_id"
                                                @change="updateSubtotal(index)"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                                                required>
                                            <option value="">Select Material</option>
                                            @foreach($materials as $mat)
                                                <option value="{{ $mat->id }}" data-price="{{ $mat->price }}">{{ $mat->name }} ({{ $mat->formatted_price }})</option>
                                            @endforeach
                                        </select>
                                        <div x-show="errors.materials && errors.materials[index] && errors.materials[index].material_id" class="mt-1 text-sm text-red-600" x-text="errors.materials[index]?.material_id"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatPrice(material.price || 0)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="number" 
                                               :name="`materials[${index}][qty]`"
                                               x-model="material.qty"
                                               @input="updateSubtotal(index)"
                                               step="0.01"
                                               min="0.01"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                                               placeholder="0.00"
                                               required>
                                        <div x-show="errors.materials && errors.materials[index] && errors.materials[index].qty" class="mt-1 text-sm text-red-600" x-text="errors.materials[index]?.qty"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" x-text="formatPrice(material.subtotal || 0)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button type="button" 
                                                @click="removeMaterial(index)"
                                                class="inline-flex items-center px-3 py-2 bg-red-50 border border-red-200 rounded-lg text-xs font-medium text-red-600 hover:bg-red-100 transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="materials.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-400">
                                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-sm font-medium text-gray-900">No materials added</p>
                                        <p class="text-sm text-gray-500 mt-1">Click "Add Material" to start building your formula</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total Cost:</td>
                                <td class="px-6 py-4 text-sm font-bold text-green-600" x-text="formatPrice(totalCost)"></td>
                                <td class="px-6 py-4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('formulas.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="isSubmitting"
                    x-text="isSubmitting ? 'Updating...' : 'Update Formula'">
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function formulaForm() {
    return {
        materials: [],
        isSubmitting: false,
        errors: @json($errors->toArray()),
        
        get totalCost() {
            return this.materials.reduce((total, material) => {
                return total + (material.subtotal || 0);
            }, 0);
        },

        addMaterial() {
            this.materials.push({
                material_id: '',
                qty: '',
                price: 0,
                subtotal: 0
            });
        },

        removeMaterial(index) {
            this.materials.splice(index, 1);
        },

        updateSubtotal(index) {
            const material = this.materials[index];
            const materialSelect = document.querySelector(`select[name="materials[${index}][material_id]"]`);
            const selectedOption = materialSelect?.options[materialSelect.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                material.price = parseFloat(selectedOption.dataset.price) || 0;
            } else {
                material.price = 0;
            }
            
            const qty = parseFloat(material.qty) || 0;
            material.subtotal = material.price * qty;
        },

        formatPrice(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(amount);
        },

        init() {
            // Pre-populate with existing formula details or old values if validation failed
            @if(old('materials'))
                this.materials = @json(old('materials', [])).map(material => ({
                    material_id: material.material_id || '',
                    qty: material.qty || '',
                    price: 0,
                    subtotal: 0
                }));
            @else
                // Load existing formula details
                const existingMaterials = @json($formula->formulaDetails->map(function($detail) {
                    return [
                        'material_id' => $detail->material_id,
                        'qty' => $detail->qty
                    ];
                }));
                
                this.materials = existingMaterials.map(material => ({
                    material_id: material.material_id || '',
                    qty: material.qty || '',
                    price: 0,
                    subtotal: 0
                }));
            @endif
            
            // If no materials exist, add one empty row
            if (this.materials.length === 0) {
                this.addMaterial();
            }
                
            // Update subtotals for pre-populated materials
            this.$nextTick(() => {
                this.materials.forEach((material, index) => {
                    this.updateSubtotal(index);
                });
            });

            // Handle form submission
            document.getElementById('formula-form').addEventListener('submit', (e) => {
                if (this.materials.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Please add at least one material to the formula.',
                        icon: 'error',
                        confirmButtonColor: '#6366f1',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                this.isSubmitting = true;
                
                // Show loading state
                Swal.fire({
                    title: 'Updating Formula...',
                    text: 'Please wait while we update your formula.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            });
        }
    }
}
</script>
@endpush