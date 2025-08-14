<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in as an admin!") }}
                </div>
            </div>

            <!-- Image Upload Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Upload Image</h3>

                    <form action="{{ route('admin.upload.image') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <!-- Image Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Image Title
                            </label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter image title"
                                   value="{{ old('title') }}">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description (Optional)
                            </label>
                            <textarea id="description"
                                      name="description"
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Enter image description">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Image File Upload -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Image
                            </label>
                            <div class="relative">
                                <input type="file"
                                       id="image"
                                       name="image"
                                       accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">

                                <!-- Image Preview -->
                                <div id="imagePreview" class="mt-3 hidden">
                                    <img id="previewImg" src="" alt="Preview" class="max-w-xs h-auto rounded-md shadow-sm border">
                                </div>
                            </div>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Supported formats: JPG, PNG, GIF, WebP. Max size: 2MB</p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                                Upload Image
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Display Success/Error Messages -->
            @if(session('success'))
                <div class="mt-6 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mt-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Processing Results Display -->
            @if(session('processing_results'))
                @php $results = session('processing_results'); @endphp
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-md p-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">Document Processing Results</h3>

                    <!-- Document Numbers -->
                    @if(!empty($results['document_numbers']))
                        <div class="mb-4">
                            <h4 class="font-medium text-blue-700 mb-2">📋 Found Document Numbers:</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($results['document_numbers'] as $docNum)
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ $docNum }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Detected Objects -->
                    @if(!empty($results['detected_objects']))
                        <div class="mb-4">
                            <h4 class="font-medium text-blue-700 mb-2">🎯 Detected Objects:</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($results['detected_objects'] as $object)
                                    <div class="bg-white p-3 rounded border">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $object['class'])) }}</span>
                                            <span class="text-sm text-gray-600">{{ number_format($object['confidence'] * 100, 1) }}%</span>
                                        </div>
                                        @if(isset($object['extracted_text']) && !empty($object['extracted_text']))
                                            <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded">
                                                "{{ Str::limit($object['extracted_text'], 100) }}"
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Extracted Text Preview -->
                    @if(!empty($results['extracted_text']))
                        <div class="mb-4">
                            <h4 class="font-medium text-blue-700 mb-2">📄 Extracted Text Preview:</h4>
                            <div class="bg-white p-4 rounded border max-h-32 overflow-y-auto">
                                <p class="text-sm text-gray-700">{{ Str::limit($results['extracted_text'], 300) }}</p>
                            </div>
                            @if(strlen($results['extracted_text']) > 300)
                                <button onclick="toggleFullText()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm">
                                    Show full text
                                </button>
                                <div id="fullText" class="hidden mt-2 bg-white p-4 rounded border max-h-64 overflow-y-auto">
                                    <p class="text-sm text-gray-700">{{ $results['extracted_text'] }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Processing Info -->
                    <div class="text-sm text-blue-600">
                        <p><strong>File:</strong> {{ $results['title'] }} - Processing completed successfully ✅</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- JavaScript for Image Preview and Text Toggle -->
    <script>
        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        });

        function toggleFullText() {
            const fullText = document.getElementById('fullText');
            const button = event.target;

            if (fullText.classList.contains('hidden')) {
                fullText.classList.remove('hidden');
                button.textContent = 'Hide full text';
            } else {
                fullText.classList.add('hidden');
                button.textContent = 'Show full text';
            }
        }
    </script>
</x-app-layout>
