<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approving Authority Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Welcome, Sir Odz!</h3>
                    <p class="text-gray-600">Review and approve documents forwarded by the Records Officer.</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-orange-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-orange-600">Pending Review</p>
                                <p class="text-2xl font-semibold text-orange-900">{{ $stats['pending_review'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600">Approved Documents</p>
                                <p class="text-2xl font-semibold text-green-900">{{ $stats['approved_documents'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600">Rejected Documents</p>
                                <p class="text-2xl font-semibold text-red-900">{{ $stats['rejected_documents'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Awaiting Review -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">📋 Documents Awaiting Review</h3>
                        <a href="{{ route('documents.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All Documents →
                        </a>
                    </div>

                    @if(isset($documents) && $documents->count() > 0)
                        <div class="space-y-6">
                            @foreach($documents as $document)
                                <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-gray-900">{{ $document->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $document->description }}</p>
                                            <div class="flex items-center mt-2 space-x-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    Forwarded {{ $document->forwarded_at ? $document->forwarded_at->diffForHumans() : 'N/A' }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    From: {{ $document->uploader->full_name ?? 'Unknown' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Document Info -->
                                    @if($document->extracted_text)
                                        <div class="mb-4 p-3 bg-gray-50 rounded">
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">📄 Extracted Content Preview:</h5>
                                            <p class="text-sm text-gray-600">{{ Str::limit($document->extracted_text, 200) }}</p>
                                        </div>
                                    @endif

                                    <!-- Review Form -->
                                    <form action="{{ route('documents.review', $document) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label for="notes_{{ $document->id }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                Authority Notes & Comments
                                            </label>
                                            <textarea id="notes_{{ $document->id }}"
                                                      name="notes"
                                                      rows="3"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                      placeholder="Add your review notes, approval conditions, or required actions...">{{ $document->authority_notes }}</textarea>
                                        </div>

                                        <div class="flex space-x-3">
                                            <button type="submit"
                                                    name="decision"
                                                    value="approve"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                                ✅ Approve & Forward to Releaser
                                            </button>
                                            <button type="submit"
                                                    name="decision"
                                                    value="reject"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                                ❌ Reject Document
                                            </button>
                                            <a href="{{ route('documents.show', $document) }}"
                                               class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-md text-sm font-medium">
                                                📄 View Full Document
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p>No documents pending review at this time.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mt-6 bg-green-50 border border-green-200 rounded-md p-4">
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
                <div class="mt-6 bg-red-50 border border-red-200 rounded-md p-4">
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
