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
                        <!-- Document Image/File Display -->
                        @if($document->image_path)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">📄 Document Image</h4>
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <img src="{{ asset('storage/' . str_replace('storage/', '', $document->file_path)) }}"
                                         alt="{{ $document->title }}"
                                         class="max-w-full h-auto rounded-md shadow-sm">
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
                                        @foreach($document->detected_objects as $object)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $object }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Document Numbers -->
                        @if($document->document_numbers && count($document->document_numbers) > 0)
                            <div>
                                <h4 class="text-lg font-medium text-gray-900 mb-3">🔢 Extracted Numbers</h4>
                                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($document->document_numbers as $number)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $number }}
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
</x-app-layout>
