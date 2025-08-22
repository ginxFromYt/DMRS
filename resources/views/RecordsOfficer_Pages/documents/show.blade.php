<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Details - Records Officer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-sm font-medium transition duration-200">
                    ← Back to Dashboard
                </a>
            </div>

            <!-- Document Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <!-- Document Header -->
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <h3 class="text-2xl font-bold text-gray-900">{{ $document->title }}</h3>
                        <p class="text-gray-600 mt-2">{{ $document->description }}</p>

                        <!-- Status and Metadata -->
                        <div class="flex items-center mt-4 space-x-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Uploaded: {{ $document->created_at->format('M d, Y H:i') }}
                            </span>
                            <span class="text-sm text-gray-500">
                                File: {{ $document->filename }}
                            </span>
                        </div>
                    </div>

                    <!-- Document Content -->
                    <div class="space-y-6">
                        <!-- Document Files Display -->
                        @if($document->image_path || $document->image_files || $document->pdf_files)
                            <div>
                                <!-- Document Type Indicator -->
                                @if($document->document_type)
                                    <div class="mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                            @if($document->document_type === 'image') bg-green-100 text-green-800
                                            @elseif($document->document_type === 'pdf') bg-red-100 text-red-800
                                            @else bg-purple-100 text-purple-800 @endif">
                                            📁 {{ ucfirst($document->document_type) }} Document
                                            @if($document->hasMultipleFiles()) ({{ count($document->all_files) }} files) @endif
                                        </span>
                                    </div>
                                @endif

                                <!-- Multiple Images Display -->
                                @if($document->image_files && count($document->image_files) > 0)
                                    <div class="mb-6">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="text-lg font-medium text-gray-900">
                                                📄 Document Images 
                                                @if(count($document->image_files) > 1)
                                                    <span class="text-sm text-gray-500">({{ count($document->image_files) }} images)</span>
                                                @endif
                                            </h4>
                                            <div class="flex space-x-2">
                                                <button onclick="rotateCurrentImage(-90)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md text-sm font-medium flex items-center">
                                                    ↺ Rotate Left
                                                </button>
                                                <button onclick="rotateCurrentImage(90)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md text-sm font-medium flex items-center">
                                                    ↻ Rotate Right
                                                </button>
                                                <button onclick="resetCurrentRotation()" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-md text-sm font-medium">
                                                    🔄 Reset
                                                </button>
                                                @if(count($document->image_files) > 1)
                                                    <select onchange="switchImage(this.value)" class="bg-white border border-gray-300 rounded-md px-3 py-1 text-sm">
                                                        @foreach($document->image_files as $index => $imagePath)
                                                            <option value="{{ $index }}">Image {{ $index + 1 }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Main Image Display -->
                                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 text-center mb-4">
                                            @foreach($document->image_files as $index => $imagePath)
                                                <img id="documentImage{{ $index }}" 
                                                     src="{{ asset('storage/' . str_replace('storage/', '', $imagePath)) }}"
                                                     alt="{{ $document->title }} - Image {{ $index + 1 }}"
                                                     class="max-w-full h-auto rounded-md shadow-sm mx-auto transition-transform duration-300 {{ $index === 0 ? '' : 'hidden' }}"
                                                     style="transform: rotate(0deg);"
                                                     data-rotation="0">
                                            @endforeach
                                        </div>

                                        <!-- Image Thumbnails (if multiple) -->
                                        @if(count($document->image_files) > 1)
                                            <div class="flex flex-wrap gap-2 justify-center">
                                                @foreach($document->image_files as $index => $imagePath)
                                                    <button onclick="switchImage({{ $index }})" 
                                                            class="thumbnail-btn border-2 rounded-lg overflow-hidden transition-all duration-200 {{ $index === 0 ? 'border-blue-500' : 'border-gray-300 hover:border-gray-400' }}"
                                                            data-index="{{ $index }}">
                                                        <img src="{{ asset('storage/' . str_replace('storage/', '', $imagePath)) }}"
                                                             alt="Thumbnail {{ $index + 1 }}"
                                                             class="w-16 h-16 object-cover">
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- PDF Files Display -->
                                @if($document->pdf_files && count($document->pdf_files) > 0)
                                    <div class="mb-6">
                                        <h4 class="text-lg font-medium text-gray-900 mb-3">
                                            📋 PDF Documents 
                                            @if(count($document->pdf_files) > 1)
                                                <span class="text-sm text-gray-500">({{ count($document->pdf_files) }} files)</span>
                                            @endif
                                        </h4>
                                        <div class="space-y-4">
                                            @foreach($document->pdf_files as $index => $pdfPath)
                                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                                    <div class="flex items-center justify-between mb-3">
                                                        <span class="text-sm font-medium text-gray-700">
                                                            📄 {{ basename($pdfPath) }}
                                                        </span>
                                                        <div class="flex space-x-2">
                                                            <a href="{{ asset('storage/' . str_replace('storage/', '', $pdfPath)) }}" 
                                                               target="_blank"
                                                               class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-md text-sm font-medium">
                                                                👁️ View
                                                            </a>
                                                            <a href="{{ asset('storage/' . str_replace('storage/', '', $pdfPath)) }}" 
                                                               download
                                                               class="bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-md text-sm font-medium">
                                                                💾 Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <!-- PDF Embed -->
                                                    <div class="bg-white rounded border" style="height: 500px;">
                                                        <iframe src="{{ asset('storage/' . str_replace('storage/', '', $pdfPath)) }}" 
                                                                class="w-full h-full rounded"
                                                                title="PDF Viewer - {{ basename($pdfPath) }}">
                                                            <p>Your browser doesn't support PDF viewing. 
                                                               <a href="{{ asset('storage/' . str_replace('storage/', '', $pdfPath)) }}" target="_blank">
                                                                   Click here to view the PDF.
                                                               </a>
                                                            </p>
                                                        </iframe>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Legacy single image fallback -->
                                @if(!($document->image_files || $document->pdf_files) && $document->image_path)
                                    <div class="mb-6">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="text-lg font-medium text-gray-900">📄 Document Image</h4>
                                            <div class="flex space-x-2">
                                                <button onclick="rotateImage(-90)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md text-sm font-medium flex items-center">
                                                    ↺ Rotate Left
                                                </button>
                                                <button onclick="rotateImage(90)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md text-sm font-medium flex items-center">
                                                    ↻ Rotate Right
                                                </button>
                                                <button onclick="resetRotation()" class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-md text-sm font-medium">
                                                    🔄 Reset
                                                </button>
                                            </div>
                                        </div>
                                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 text-center">
                                            <img id="documentImage" 
                                                 src="{{ asset('storage/' . str_replace('storage/', '', $document->file_path)) }}"
                                                 alt="{{ $document->title }}"
                                                 class="max-w-full h-auto rounded-md shadow-sm mx-auto transition-transform duration-300"
                                                 style="transform: rotate(0deg);">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Extracted Text -->
                        @if($document->extracted_text)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">📝 Extracted Text Content</h4>
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <pre class="whitespace-pre-wrap text-sm text-gray-700 font-mono">{{ $document->extracted_text }}</pre>
                                </div>
                            </div>
                        @endif

                        <!-- Detected Objects -->
                        @if($document->detected_objects && count($document->detected_objects) > 0)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">🔍 Detected Elements</h4>
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                        @if(is_array($document->detected_objects))
                                            @foreach($document->detected_objects as $object)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ is_string($object) ? $object : (is_array($object) ? json_encode($object) : strval($object)) }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-500">No objects detected</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Document Numbers -->
                        @if($document->document_numbers && is_array($document->document_numbers) && count($document->document_numbers) > 0)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">🔢 Extracted Numbers</h4>
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($document->document_numbers as $number)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ is_string($number) ? $number : (is_array($number) ? json_encode($number) : strval($number)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons for Records Officer -->
                    @if($document->status === 'received')
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">📤 Forward Document</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                Review the document content and forward it to the Approving Authority for review.
                            </p>

                            <form action="{{ route('documents.forward.authority', $document) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition duration-200">
                                    📤 Forward to Approving Authority
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Status Information -->
                    @if($document->status !== 'received')
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">📊 Document Status</h4>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="space-y-2">
                                    @if($document->forwarded_at)
                                        <p class="text-sm text-blue-800">
                                            ✅ Forwarded to Authority: {{ $document->forwarded_at->format('M d, Y H:i') }}
                                        </p>
                                    @endif
                                    @if($document->reviewed_at)
                                        <p class="text-sm text-blue-800">
                                            ✅ Reviewed by Authority: {{ $document->reviewed_at->format('M d, Y H:i') }}
                                        </p>
                                    @endif
                                    @if($document->released_at)
                                        <p class="text-sm text-blue-800">
                                            ✅ Released to Employee: {{ $document->released_at->format('M d, Y H:i') }}
                                        </p>
                                    @endif
                                    @if($document->sent_at)
                                        <p class="text-sm text-blue-800">
                                            ✅ Sent to Employee: {{ $document->sent_at->format('M d, Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Image Rotation and Navigation JavaScript -->
    <script>
        let currentRotation = 0;
        let currentImageIndex = 0;
        let rotations = {}; // Store rotation for each image

        // For multiple images
        function rotateCurrentImage(degrees) {
            const image = document.getElementById(`documentImage${currentImageIndex}`);
            if (image) {
                if (!rotations[currentImageIndex]) {
                    rotations[currentImageIndex] = 0;
                }
                rotations[currentImageIndex] += degrees;
                image.style.transform = `rotate(${rotations[currentImageIndex]}deg)`;
                image.setAttribute('data-rotation', rotations[currentImageIndex]);
                localStorage.setItem(`documentRotations_{{ $document->id }}`, JSON.stringify(rotations));
            }
        }

        function resetCurrentRotation() {
            const image = document.getElementById(`documentImage${currentImageIndex}`);
            if (image) {
                rotations[currentImageIndex] = 0;
                image.style.transform = 'rotate(0deg)';
                image.setAttribute('data-rotation', '0');
                localStorage.setItem(`documentRotations_{{ $document->id }}`, JSON.stringify(rotations));
            }
        }

        function switchImage(index) {
            // Hide current image
            const currentImage = document.getElementById(`documentImage${currentImageIndex}`);
            if (currentImage) {
                currentImage.classList.add('hidden');
            }

            // Update thumbnail selection
            const currentThumbnail = document.querySelector(`.thumbnail-btn[data-index="${currentImageIndex}"]`);
            if (currentThumbnail) {
                currentThumbnail.classList.remove('border-blue-500');
                currentThumbnail.classList.add('border-gray-300', 'hover:border-gray-400');
            }

            // Show new image
            currentImageIndex = parseInt(index);
            const newImage = document.getElementById(`documentImage${currentImageIndex}`);
            if (newImage) {
                newImage.classList.remove('hidden');
            }

            // Update thumbnail selection
            const newThumbnail = document.querySelector(`.thumbnail-btn[data-index="${currentImageIndex}"]`);
            if (newThumbnail) {
                newThumbnail.classList.add('border-blue-500');
                newThumbnail.classList.remove('border-gray-300', 'hover:border-gray-400');
            }

            // Update select dropdown if it exists
            const select = document.querySelector('select[onchange="switchImage(this.value)"]');
            if (select) {
                select.value = currentImageIndex;
            }
        }

        // Legacy single image functions
        function rotateImage(degrees) {
            const image = document.getElementById('documentImage');
            if (image) {
                currentRotation += degrees;
                image.style.transform = `rotate(${currentRotation}deg)`;
                localStorage.setItem('documentRotation_{{ $document->id }}', currentRotation);
            }
        }

        function resetRotation() {
            const image = document.getElementById('documentImage');
            if (image) {
                currentRotation = 0;
                image.style.transform = 'rotate(0deg)';
                localStorage.removeItem('documentRotation_{{ $document->id }}');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Load saved rotations for multiple images
            const savedRotations = localStorage.getItem(`documentRotations_{{ $document->id }}`);
            if (savedRotations) {
                try {
                    rotations = JSON.parse(savedRotations);
                    Object.keys(rotations).forEach(index => {
                        const image = document.getElementById(`documentImage${index}`);
                        if (image) {
                            image.style.transform = `rotate(${rotations[index]}deg)`;
                            image.setAttribute('data-rotation', rotations[index]);
                        }
                    });
                } catch (e) {
                    console.log('Error loading saved rotations:', e);
                }
            }

            // Load saved rotation for single image (legacy)
            const savedRotation = localStorage.getItem('documentRotation_{{ $document->id }}');
            if (savedRotation) {
                currentRotation = parseInt(savedRotation);
                const image = document.getElementById('documentImage');
                if (image) {
                    image.style.transform = `rotate(${currentRotation}deg)`;
                }
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.target.tagName !== 'INPUT' && event.target.tagName !== 'TEXTAREA') {
                if (event.key === 'ArrowLeft' && event.ctrlKey) {
                    event.preventDefault();
                    const hasMultipleImages = document.getElementById(`documentImage${currentImageIndex}`);
                    if (hasMultipleImages) {
                        rotateCurrentImage(-90);
                    } else {
                        rotateImage(-90);
                    }
                } else if (event.key === 'ArrowRight' && event.ctrlKey) {
                    event.preventDefault();
                    const hasMultipleImages = document.getElementById(`documentImage${currentImageIndex}`);
                    if (hasMultipleImages) {
                        rotateCurrentImage(90);
                    } else {
                        rotateImage(90);
                    }
                } else if (event.key === 'r' && event.ctrlKey) {
                    event.preventDefault();
                    const hasMultipleImages = document.getElementById(`documentImage${currentImageIndex}`);
                    if (hasMultipleImages) {
                        resetCurrentRotation();
                    } else {
                        resetRotation();
                    }
                } else if (event.key === 'ArrowUp' && event.ctrlKey) {
                    // Navigate to previous image
                    event.preventDefault();
                    const totalImages = document.querySelectorAll('[id^="documentImage"]').length;
                    if (totalImages > 1) {
                        const prevIndex = currentImageIndex > 0 ? currentImageIndex - 1 : totalImages - 1;
                        switchImage(prevIndex);
                    }
                } else if (event.key === 'ArrowDown' && event.ctrlKey) {
                    // Navigate to next image
                    event.preventDefault();
                    const totalImages = document.querySelectorAll('[id^="documentImage"]').length;
                    if (totalImages > 1) {
                        const nextIndex = currentImageIndex < totalImages - 1 ? currentImageIndex + 1 : 0;
                        switchImage(nextIndex);
                    }
                }
            }
        });
    </script>
</x-app-layout>
