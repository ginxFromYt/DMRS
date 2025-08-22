<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Details - Employee') }}
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Assigned: {{ $document->sent_at ? $document->sent_at->format('M d, Y H:i') : 'N/A' }}
                            </span>
                            <span class="text-sm text-gray-500">
                                From: {{ $document->uploader->full_name ?? 'Unknown' }}
                            </span>
                        </div>
                    </div>

                    <!-- Authority Notes -->
                    @if($document->authority_notes)
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-3">📋 Authority Instructions</h4>
                            <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                                <p class="text-sm text-gray-700">{{ $document->authority_notes }}</p>
                            </div>
                        </div>
                    @endif

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
                                <h4 class="text-lg font-medium text-gray-900 mb-3">📝 Document Content</h4>
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <pre class="whitespace-pre-wrap text-sm text-gray-700 font-mono">{{ $document->extracted_text }}</pre>
                                </div>
                            </div>
                        @endif

                        <!-- Detected Elements (if helpful for the employee) -->
                        @if($document->detected_objects && count($document->detected_objects) > 0)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">🔍 Document Elements</h4>
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

                        <!-- Important Numbers -->
                        @if($document->document_numbers && is_array($document->document_numbers) && count($document->document_numbers) > 0)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">🔢 Important Numbers</h4>
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

                    <!-- Employee Actions -->
                    @if($document->assigned_to === auth()->id())
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">✅ Employee Actions</h4>

                            <div class="space-y-4">
                                <!-- Mark as Seen -->
                                @if($document->status === 'sent_to_employee')
                                    <div>
                                        <p class="text-sm text-gray-600 mb-3">
                                            Mark this document as seen to acknowledge receipt.
                                        </p>
                                        <form action="{{ route('documents.mark.seen', $document) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition duration-200">
                                                👁️ Mark as Seen
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <!-- Mark as Actioned -->
                                @if($document->status === 'seen_by_employee')
                                    <div>
                                        <p class="text-sm text-gray-600 mb-3">
                                            When you have completed the required actions, mark this document as actioned.
                                        </p>
                                        <form action="{{ route('documents.mark.actioned', $document) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md text-sm font-medium transition duration-200">
                                                ✅ Mark as Actioned
                                            </button>
                                        </form>
                                    </div>
                                @endif

                                <!-- Completed Status -->
                                @if($document->status === 'actioned_by_employee')
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-green-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <p class="text-sm font-medium text-green-800">
                                                Document has been actioned and completed.
                                            </p>
                                        </div>
                                        <p class="text-xs text-green-600 mt-1">
                                            Actioned: {{ $document->actioned_at ? $document->actioned_at->format('M d, Y H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Document Timeline -->
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">📊 Document Timeline</h4>
                        <div class="space-y-3">
                            @if($document->received_at)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-3 h-3 bg-blue-400 rounded-full mr-3"></div>
                                    <span>Received: {{ $document->received_at->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
                            @if($document->forwarded_at)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-3 h-3 bg-orange-400 rounded-full mr-3"></div>
                                    <span>Forwarded for Review: {{ $document->forwarded_at->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
                            @if($document->reviewed_at)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-3 h-3 bg-purple-400 rounded-full mr-3"></div>
                                    <span>Reviewed by Authority: {{ $document->reviewed_at->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
                            @if($document->sent_at)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                                    <span>Assigned to You: {{ $document->sent_at->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
                            @if($document->seen_at)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-3 h-3 bg-blue-600 rounded-full mr-3"></div>
                                    <span>Marked as Seen: {{ $document->seen_at->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
                            @if($document->actioned_at)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-3 h-3 bg-green-600 rounded-full mr-3"></div>
                                    <span>Marked as Actioned: {{ $document->actioned_at->format('M d, Y H:i') }}</span>
                                </div>
                            @endif
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
