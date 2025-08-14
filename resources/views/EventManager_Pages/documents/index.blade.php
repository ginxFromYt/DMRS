<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event Documentation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">📅 Event Documentation Center</h3>
                            <p class="text-gray-600 mt-1">View documents related to events and activities.</p>
                        </div>
                        <a href="{{ route('dashboard') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                            ← Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Event Documents -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">📄 Event-Related Documents</h3>
                        <div class="text-sm text-gray-600">
                            Showing {{ $documents->count() }} documents
                        </div>
                    </div>

                    @if($documents->count() > 0)
                        <div class="space-y-6">
                            @foreach($documents as $document)
                                <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-gray-900 mb-2">{{ $document->title }}</h4>
                                            <p class="text-sm text-gray-600 mb-3">{{ $document->description }}</p>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                <div>
                                                    <span class="text-xs text-gray-500">Status:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2 bg-blue-100 text-blue-800">
                                                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">Created:</span>
                                                    <span class="text-sm text-gray-700 ml-2">{{ $document->created_at->format('M d, Y h:i A') }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">By:</span>
                                                    <span class="text-sm text-gray-700 ml-2">{{ $document->user->first_name }} {{ $document->user->last_name }}</span>
                                                </div>
                                            </div>

                                            @if($document->extracted_text)
                                                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                                    <h5 class="text-sm font-medium text-gray-800 mb-2">📝 Document Content:</h5>
                                                    <p class="text-sm text-gray-600">{{ Str::limit($document->extracted_text, 300) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('documents.show', $document) }}"
                                           class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-md text-sm font-medium">
                                            👁️ View Document
                                        </a>

                                        @if($document->image_path)
                                            <a href="{{ Storage::url($document->image_path) }}"
                                               target="_blank"
                                               class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                                                🖼️ View Image
                                            </a>
                                        @endif

                                        @if($document->file_path)
                                            <a href="{{ Storage::url($document->file_path) }}"
                                               target="_blank"
                                               class="bg-purple-100 hover:bg-purple-200 text-purple-800 px-4 py-2 rounded-md text-sm font-medium">
                                                📎 Download File
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="h-16 w-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
                            <p class="text-gray-600">Event-related documents will appear here as they are created.</p>
                            <a href="{{ route('events.index') }}"
                               class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium">
                                Manage Events
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('events.index') }}"
                           class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-3 rounded-lg text-center font-medium">
                            📅 Manage Events
                        </a>
                        <a href="{{ route('events.create') }}"
                           class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-3 rounded-lg text-center font-medium">
                            ➕ Create Event
                        </a>
                        <a href="{{ route('dashboard') }}"
                           class="bg-purple-100 hover:bg-purple-200 text-purple-800 px-4 py-3 rounded-lg text-center font-medium">
                            📊 Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
