<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Documents') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">📬 My Documents</h3>
                            <p class="text-gray-600 mt-1">View and manage documents assigned to you.</p>
                        </div>
                        <a href="{{ route('dashboard') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                            ← Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Tabs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button onclick="filterByStatus('all')"
                                    class="status-tab active py-2 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                                All Documents
                            </button>
                            <button onclick="filterByStatus('sent_to_employee')"
                                    class="status-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                New
                            </button>
                            <button onclick="filterByStatus('seen_by_employee')"
                                    class="status-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Seen
                            </button>
                            <button onclick="filterByStatus('actioned_by_employee')"
                                    class="status-tab py-2 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Actioned
                            </button>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Documents List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">📄 Document List</h3>
                        <div class="text-sm text-gray-600" id="document-count">
                            Showing {{ $documents->count() }} documents
                        </div>
                    </div>

                    @if($documents->count() > 0)
                        <div class="space-y-6" id="documents-container">
                            @foreach($documents as $document)
                                <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 document-item"
                                     data-status="{{ $document->status }}">

                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-gray-900 mb-2">{{ $document->title }}</h4>
                                            <p class="text-sm text-gray-600 mb-3">{{ $document->description }}</p>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                                <div>
                                                    <span class="text-xs text-gray-500">Status:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                                        @if($document->status === 'sent_to_employee') bg-orange-100 text-orange-800
                                                        @elseif($document->status === 'seen_by_employee') bg-yellow-100 text-yellow-800
                                                        @elseif($document->status === 'actioned_by_employee') bg-green-100 text-green-800
                                                        @endif">
                                                        @if($document->status === 'sent_to_employee') New
                                                        @elseif($document->status === 'seen_by_employee') Seen
                                                        @elseif($document->status === 'actioned_by_employee') Actioned
                                                        @else {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                        @endif
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">Received:</span>
                                                    <span class="text-sm text-gray-700 ml-2">{{ $document->sent_at ? $document->sent_at->format('M d, Y h:i A') : $document->created_at->format('M d, Y h:i A') }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">Priority:</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2 bg-blue-100 text-blue-800">
                                                        Normal
                                                    </span>
                                                </div>
                                            </div>

                                            @if($document->extracted_text)
                                                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                                    <h5 class="text-sm font-medium text-gray-800 mb-2">📝 Document Preview:</h5>
                                                    <p class="text-sm text-gray-600">{{ Str::limit($document->extracted_text, 200) }}</p>
                                                </div>
                                            @endif

                                            @if($document->employee_response)
                                                <div class="mb-4 p-3 bg-green-50 rounded-lg">
                                                    <h5 class="text-sm font-medium text-green-800 mb-2">✅ My Response:</h5>
                                                    <p class="text-sm text-green-700">{{ $document->employee_response }}</p>
                                                    <p class="text-xs text-green-600 mt-1">Actioned on {{ $document->actioned_at->format('M d, Y h:i A') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('documents.show', $document) }}"
                                           class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-md text-sm font-medium">
                                            👁️ View Details
                                        </a>

                                        @if($document->status === 'sent_to_employee')
                                            <form action="{{ route('documents.mark.seen', $document) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-2 rounded-md text-sm font-medium">
                                                    👁️ Mark as Seen
                                                </button>
                                            </form>
                                        @endif

                                        @if($document->status === 'seen_by_employee')
                                            <button onclick="showActionModal({{ $document->id }}, '{{ $document->title }}')"
                                                    class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded-md text-sm font-medium">
                                                ✅ Take Action
                                            </button>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m-4 4v4a2 2 0 002 2h2m0-6V9a2 2 0 012-2h2"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No documents assigned</h3>
                            <p class="text-gray-600">Documents assigned to you will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal -->
    <div id="actionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Take Action</h3>
                <p class="text-sm text-gray-600 mb-4">Provide your response or action taken on this document:</p>

                <form id="actionForm" method="POST">
                    @csrf
                    <textarea name="employee_response"
                              id="employee_response"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4"
                              placeholder="Describe the action taken or your response..."
                              required></textarea>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeActionModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Submit Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function filterByStatus(status) {
            const documents = document.querySelectorAll('.document-item');
            const tabs = document.querySelectorAll('.status-tab');

            // Update tab styles
            tabs.forEach(tab => {
                tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });

            event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
            event.target.classList.remove('border-transparent', 'text-gray-500');

            // Filter documents
            let visibleCount = 0;
            documents.forEach(document => {
                const documentStatus = document.getAttribute('data-status');

                if (status === 'all' || documentStatus === status) {
                    document.style.display = 'block';
                    visibleCount++;
                } else {
                    document.style.display = 'none';
                }
            });

            // Update count
            document.getElementById('document-count').textContent = `Showing ${visibleCount} documents`;
        }

        function showActionModal(documentId, documentTitle) {
            const modal = document.getElementById('actionModal');
            const form = document.getElementById('actionForm');

            // Set form action
            form.action = `/documents/${documentId}/mark-actioned`;

            modal.classList.remove('hidden');
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('employee_response').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('actionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeActionModal();
            }
        });
    </script>
</x-app-layout>
