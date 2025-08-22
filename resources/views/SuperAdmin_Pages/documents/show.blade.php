<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Details - Super Administrator') }}
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Uploaded: {{ $document->created_at->format('M d, Y H:i') }}
                            </span>
                            <span class="text-sm text-gray-500">
                                By: {{ $document->uploader->full_name ?? 'Unknown' }}
                            </span>
                            <span class="text-sm text-gray-500">
                                File: {{ $document->filename }}
                            </span>
                        </div>
                    </div>

                    <!-- Administrative Information -->
                    <div class="mb-6 bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-purple-800 mb-2">🔧 Administrative Details</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-purple-700">Document ID:</span>
                                <span class="text-purple-600">{{ $document->id }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-purple-700">Current Handler:</span>
                                <span class="text-purple-600">{{ $document->currentHandler->full_name ?? 'None' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-purple-700">Assigned To:</span>
                                <span class="text-purple-600">{{ $document->assignedUser->full_name ?? 'Not assigned' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-purple-700">Review Decision:</span>
                                <span class="text-purple-600">{{ $document->review_decision ?? 'Pending' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Document Content -->
                    <div class="space-y-6">
                        <!-- Document Image/File Display -->
                        @if($document->image_path)
                            <div>
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
                                <h4 class="text-lg font-medium text-gray-900 mb-3">🔍 ML Detection Results</h4>
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
                                <h4 class="text-lg font-medium text-gray-900 mb-3">🔢 Extracted Numbers/IDs</h4>
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

                        <!-- Authority Notes -->
                        @if($document->authority_notes)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">📋 Authority Notes</h4>
                                <div class="border border-gray-200 rounded-lg p-4 bg-yellow-50">
                                    <p class="text-sm text-gray-700">{{ $document->authority_notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Complete Document Timeline -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">📊 Complete Workflow Timeline</h4>
                        <div class="space-y-3">
                            @if($document->received_at)
                                <div class="flex items-center text-sm">
                                    <div class="w-3 h-3 bg-blue-400 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Received:</span>
                                    <span class="ml-2 font-medium">{{ $document->received_at->format('M d, Y H:i:s') }}</span>
                                </div>
                            @endif
                            @if($document->forwarded_at)
                                <div class="flex items-center text-sm">
                                    <div class="w-3 h-3 bg-orange-400 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Forwarded to Authority:</span>
                                    <span class="ml-2 font-medium">{{ $document->forwarded_at->format('M d, Y H:i:s') }}</span>
                                </div>
                            @endif
                            @if($document->reviewed_at)
                                <div class="flex items-center text-sm">
                                    <div class="w-3 h-3 bg-purple-400 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Reviewed by Authority:</span>
                                    <span class="ml-2 font-medium">{{ $document->reviewed_at->format('M d, Y H:i:s') }}</span>
                                    @if($document->review_decision)
                                        <span class="ml-2 px-2 py-1 rounded text-xs
                                            @if($document->review_decision === 'approved') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($document->review_decision) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            @if($document->released_at)
                                <div class="flex items-center text-sm">
                                    <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Released:</span>
                                    <span class="ml-2 font-medium">{{ $document->released_at->format('M d, Y H:i:s') }}</span>
                                </div>
                            @endif
                            @if($document->sent_at)
                                <div class="flex items-center text-sm">
                                    <div class="w-3 h-3 bg-green-600 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Sent to Employee:</span>
                                    <span class="ml-2 font-medium">{{ $document->sent_at->format('M d, Y H:i:s') }}</span>
                                </div>
                            @endif
                            @if($document->seen_at)
                                <div class="flex items-center text-sm">
                                    <div class="w-3 h-3 bg-blue-600 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Seen by Employee:</span>
                                    <span class="ml-2 font-medium">{{ $document->seen_at->format('M d, Y H:i:s') }}</span>
                                </div>
                            @endif
                            @if($document->actioned_at)
                                <div class="flex items-center text-sm">
                                    <div class="w-3 h-3 bg-green-700 rounded-full mr-3"></div>
                                    <span class="text-gray-600">Actioned by Employee:</span>
                                    <span class="ml-2 font-medium">{{ $document->actioned_at->format('M d, Y H:i:s') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Administrative Actions -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">🔧 Administrative Actions</h4>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-sm text-red-700 mb-3">
                                <strong>Warning:</strong> These actions should only be used for system administration purposes.
                            </p>
                            <form action="{{ route('admin.documents.delete') }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this document? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="document_id" value="{{ $document->id }}">
                                <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                    🗑️ Delete Document
                                </button>
                            </form>
                        </div>
                    </div>
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

    <!-- Image Rotation JavaScript -->
    <script>
        let currentRotation = 0;

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
                    rotateImage(-90);
                } else if (event.key === 'ArrowRight' && event.ctrlKey) {
                    event.preventDefault();
                    rotateImage(90);
                } else if (event.key === 'r' && event.ctrlKey) {
                    event.preventDefault();
                    resetRotation();
                }
            }
        });
    </script>
</x-app-layout>
