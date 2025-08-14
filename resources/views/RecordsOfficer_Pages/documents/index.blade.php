<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">📋 Document Management</h3>
                            <p class="text-gray-600 mt-1">Manage and track all documents in the workflow system.</p>
                        </div>
                        <a href="{{ route('dashboard') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                            ← Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">Total Documents</p>
                                <p class="text-2xl font-semibold text-blue-900">{{ $stats['total_documents'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-orange-600">Pending</p>
                                <p class="text-2xl font-semibold text-orange-900">{{ $stats['pending_documents'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-600">Forwarded</p>
                                <p class="text-2xl font-semibold text-yellow-900">{{ $stats['forwarded_documents'] ?? 0 }}</p>
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
                                <p class="text-sm font-medium text-green-600">Completed</p>
                                <p class="text-2xl font-semibold text-green-900">{{ $stats['completed_documents'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter and Search -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Documents</label>
                            <input type="text"
                                   id="search"
                                   name="search"
                                   placeholder="Search by title, description, or content..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:w-48">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Filter by Status</label>
                            <select id="status"
                                    name="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Statuses</option>
                                <option value="received">Received</option>
                                <option value="forwarded_to_authority">Forwarded to Authority</option>
                                <option value="reviewed_by_authority">Reviewed by Authority</option>
                                <option value="released">Released</option>
                                <option value="sent_to_employee">Sent to Employee</option>
                                <option value="seen_by_employee">Seen by Employee</option>
                                <option value="actioned_by_employee">Actioned by Employee</option>
                            </select>
                        </div>
                        <div class="md:w-32 flex items-end">
                            <button type="button"
                                    onclick="filterDocuments()"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">📄 All Documents</h3>
                        <div class="text-sm text-gray-600">
                            Showing {{ $documents->count() }} documents
                        </div>
                    </div>

                    @if($documents->count() > 0)
                        <div class="space-y-6" id="documents-container">
                            @foreach($documents as $document)
                                <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 document-item"
                                     data-title="{{ strtolower($document->title) }}"
                                     data-description="{{ strtolower($document->description) }}"
                                     data-status="{{ $document->status }}">

                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-gray-900 mb-2">{{ $document->title }}</h4>
                                            <p class="text-sm text-gray-600 mb-3">{{ $document->description }}</p>

                                            <!-- Document Info -->
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                <div>
                                                    <span class="text-xs text-gray-500">Status:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                                        @if($document->status === 'received') bg-blue-100 text-blue-800
                                                        @elseif($document->status === 'forwarded_to_authority') bg-yellow-100 text-yellow-800
                                                        @elseif($document->status === 'reviewed_by_authority') bg-purple-100 text-purple-800
                                                        @elseif($document->status === 'released') bg-indigo-100 text-indigo-800
                                                        @elseif($document->status === 'sent_to_employee') bg-orange-100 text-orange-800
                                                        @elseif($document->status === 'seen_by_employee') bg-teal-100 text-teal-800
                                                        @elseif($document->status === 'actioned_by_employee') bg-green-100 text-green-800
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">Created:</span>
                                                    <span class="text-sm text-gray-700 ml-2">{{ $document->created_at->format('M d, Y h:i A') }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">Uploaded by:</span>
                                                    <span class="text-sm text-gray-700 ml-2">{{ $document->user->first_name ?? "" }} {{ $document->user->last_name ?? "" }}</span>
                                                </div>
                                            </div>

                                            <!-- Extracted Text Preview -->
                                            @if($document->extracted_text)
                                                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                                    <h5 class="text-sm font-medium text-gray-800 mb-2">📝 Extracted Text Preview:</h5>
                                                    <p class="text-sm text-gray-600 line-clamp-3">{{ Str::limit($document->extracted_text, 200) }}</p>
                                                </div>
                                            @endif

                                            <!-- Workflow Timeline -->
                                            <div class="mb-4">
                                                <h5 class="text-sm font-medium text-gray-800 mb-2">📊 Workflow Progress:</h5>
                                                <div class="flex items-center space-x-2">
                                                    <!-- Received -->
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                                        <span class="ml-2 text-xs text-gray-600">Received</span>
                                                    </div>

                                                    <!-- Forwarded -->
                                                    <div class="flex-1 h-px bg-gray-300"></div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 rounded-full {{ in_array($document->status, ['forwarded_to_authority', 'reviewed_by_authority', 'released', 'sent_to_employee', 'seen_by_employee', 'actioned_by_employee']) ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                                        <span class="ml-2 text-xs text-gray-600">Forwarded</span>
                                                    </div>

                                                    <!-- Reviewed -->
                                                    <div class="flex-1 h-px bg-gray-300"></div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 rounded-full {{ in_array($document->status, ['reviewed_by_authority', 'released', 'sent_to_employee', 'seen_by_employee', 'actioned_by_employee']) ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                                        <span class="ml-2 text-xs text-gray-600">Reviewed</span>
                                                    </div>

                                                    <!-- Released -->
                                                    <div class="flex-1 h-px bg-gray-300"></div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 rounded-full {{ in_array($document->status, ['released', 'sent_to_employee', 'seen_by_employee', 'actioned_by_employee']) ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                                        <span class="ml-2 text-xs text-gray-600">Released</span>
                                                    </div>

                                                    <!-- Completed -->
                                                    <div class="flex-1 h-px bg-gray-300"></div>
                                                    <div class="flex items-center">
                                                        <div class="w-3 h-3 rounded-full {{ $document->status === 'actioned_by_employee' ? 'bg-green-500' : 'bg-gray-300' }}"></div>
                                                        <span class="ml-2 text-xs text-gray-600">Completed</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('documents.show', $document) }}"
                                           class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-md text-sm font-medium">
                                            👁️ View Details
                                        </a>

                                        @if($document->status === 'received')
                                            <form action="{{ route('documents.forward.authority', $document) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-2 rounded-md text-sm font-medium"
                                                        onclick="return confirm('Forward this document to the approving authority?')">
                                                    📤 Forward to Authority
                                                </button>
                                            </form>
                                        @endif

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
                                               class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
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
                            <p class="text-gray-600 mb-4">Get started by uploading your first document.</p>
                            <a href="{{ route('dashboard') }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium">
                                Upload Document
                            </a>
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

    <script>
        function filterDocuments() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const statusFilter = document.getElementById('status').value;
            const documents = document.querySelectorAll('.document-item');

            documents.forEach(document => {
                const title = document.getAttribute('data-title');
                const description = document.getAttribute('data-description');
                const status = document.getAttribute('data-status');

                const matchesSearch = !searchTerm || title.includes(searchTerm) || description.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;

                if (matchesSearch && matchesStatus) {
                    document.style.display = 'block';
                } else {
                    document.style.display = 'none';
                }
            });

            // Update count
            const visibleCount = Array.from(documents).filter(doc => doc.style.display !== 'none').length;
            document.querySelector('.text-sm.text-gray-600').textContent = `Showing ${visibleCount} documents`;
        }

        // Real-time search
        document.getElementById('search').addEventListener('input', filterDocuments);
        document.getElementById('status').addEventListener('change', filterDocuments);
    </script>
</x-app-layout>
