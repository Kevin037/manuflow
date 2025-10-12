<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data" x-data="profilePhotoForm()">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Profile Photo (similar to product photo uploader) -->
        <div>
            <x-input-label for="photo" :value="__('Profile Photo')" />
            <div class="mt-2">
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors duration-200" id="photoDropzone">
                    <div class="space-y-2 text-center" x-show="!photoPreview">
                        @php $currentPhoto = $user->photo ? asset('storage/' . $user->photo) : null; @endphp
                        @if($currentPhoto)
                            <img src="{{ $currentPhoto }}" alt="Current Photo" class="mx-auto h-24 w-24 rounded-full object-cover shadow"/>
                            <p class="text-xs text-gray-500">Current photo</p>
                        @endif
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                <span>{{ __('Upload a file') }}</span>
                                <input id="photo" name="photo" type="file" class="sr-only" accept="image/*" @change="handlePhotoUpload($event)">
                            </label>
                            <p class="pl-1">{{ __('or drag and drop') }}</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                    </div>

                    <!-- New Photo Preview -->
                    <div x-show="photoPreview" class="relative">
                        <img :src="photoPreview" alt="Preview" class="mx-auto h-24 w-24 rounded-full object-cover shadow"/>
                        <div class="mt-3">
                            <button type="button" @click="removePhoto()" class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                {{ __('Remove New Photo') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>

    @push('scripts')
    <script>
    function profilePhotoForm() {
        return {
            photoPreview: null,
            handlePhotoUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                if (file.size > 2048 * 1024) {
                    Swal.fire({ title: 'File Too Large', text: 'Please select an image smaller than 2MB.', icon: 'error', confirmButtonColor: '#6366f1' });
                    event.target.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => { this.photoPreview = e.target.result; };
                reader.readAsDataURL(file);
            },
            removePhoto() {
                this.photoPreview = null;
                const input = document.getElementById('photo');
                if (input) input.value = '';
            }
        }
    }
    </script>
    @endpush
</section>
