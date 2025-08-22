<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Details - Approving Authority') }}
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($document->status === 'forwarded_to_authority') bg-orange-100 text-orange-800
                                @elseif($document->status === 'reviewed_by_authority') bg-blue-100 text-blue-800
                                @elseif($document->status === 'released') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                            </span>
                            <span class="text-sm text-gray-500">
                                Uploaded by: {{ $document->uploader->full_name ?? 'Unknown' }}
                            </span>
                            <span class="text-sm text-gray-500">
                                File: {{ $document->filename }}
                            </span>
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
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                            </svg>
                                            ↺ Rotate Left
                                        </button>
                                        <button onclick="rotateImage(90)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md text-sm font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                            </svg>
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

                        <!-- Previous Authority Notes -->
                        @if($document->authority_notes)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">📋 Previous Authority Notes</h4>
                                <div class="border border-gray-200 rounded-lg p-4 bg-yellow-50">
                                    <p class="text-sm text-gray-700">{{ $document->authority_notes }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Review Decision if already reviewed -->
                        @if($document->review_decision)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">✅ Review Decision</h4>
                                <div class="border border-gray-200 rounded-lg p-4
                                    @if($document->review_decision === 'approved') bg-green-50
                                    @else bg-red-50
                                    @endif">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($document->review_decision === 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($document->review_decision) }}
                                    </span>
                                    <p class="text-sm text-gray-700 mt-2">Reviewed: {{ $document->reviewed_at ? $document->reviewed_at->format('M d, Y H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    @if($document->status === 'forwarded_to_authority')
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">🎯 Review Actions</h4>

                            <!-- Review Form -->
                            <form action="{{ route('documents.review', $document) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Authority Notes & Comments
                                    </label>
                                    <textarea id="notes"
                                              name="notes"
                                              rows="4"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Add your review notes, approval conditions, or required actions...">{{ $document->authority_notes }}</textarea>
                                </div>

                                <div class="flex space-x-3">
                                    <button type="submit"
                                            name="decision"
                                            value="approve"
                                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md text-sm font-medium transition duration-200">
                                        ✅ Approve & Release Document
                                    </button>
                                    <button type="submit"
                                            name="decision"
                                            value="reject"
                                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md text-sm font-medium transition duration-200">
                                        ❌ Reject Document
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Release Actions for Approved Documents -->
                    @if($document->status === 'reviewed_by_authority' && $document->review_decision === 'approved')
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">📤 Release Document</h4>

                            <form action="{{ route('documents.send.employee', $document) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Assign to Employee
                                    </label>
                                    <select id="employee_id"
                                            name="employee_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            required>
                                        <option value="">Select an employee...</option>
                                        @foreach(\App\Models\User::whereHas('roles', function($q) { $q->where('name', 'Employee'); })->get() as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->designation }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md text-sm font-medium transition duration-200">
                                    📤 Send to Employee
                                </button>
                            </form>
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

    <!-- Image Rotation JavaScript -->
    <script>
        let currentRotation = 0;

        function rotateImage(degrees) {
            const image = document.getElementById('documentImage');
            if (image) {
                currentRotation += degrees;
                image.style.transform = `rotate(${currentRotation}deg)`;
                
                // Store rotation preference in localStorage for this document
                localStorage.setItem('documentRotation_{{ $document->id }}', currentRotation);
            }
        }

        function resetRotation() {
            const image = document.getElementById('documentImage');
            if (image) {
                currentRotation = 0;
                image.style.transform = 'rotate(0deg)';
                
                // Remove stored rotation preference
                localStorage.removeItem('documentRotation_{{ $document->id }}');
            }
        }

        // Restore saved rotation when page loads
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

        // Keyboard shortcuts for rotation
        document.addEventListener('keydown', function(event) {
            // Only activate if not typing in an input field
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
