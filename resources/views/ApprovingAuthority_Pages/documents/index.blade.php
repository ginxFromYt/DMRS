<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Review Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">⚖️ Document Review Center</h3>
                            <p class="text-gray-600 mt-1">Review and approve documents in the workflow system.</p>
                        </div>
                        <a href="{{ route('dashboard') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                            ← Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Documents Awaiting Review -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">📋 Documents Awaiting Review</h3>

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
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2 bg-yellow-100 text-yellow-800">
                                                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">Submitted:</span>
                                                    <span class="text-sm text-gray-700 ml-2">{{ $document->created_at->format('M d, Y h:i A') }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">From:</span>
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
                                            👁️ Review Document
                                        </a>

                                        @if($document->status === 'forwarded_to_authority')
                                            <button onclick="showReviewModal({{ $document->id }}, '{{ $document->title }}', 'approve')"
                                                    class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded-md text-sm font-medium">
                                                ✅ Approve
                                            </button>

                                            <button onclick="showReviewModal({{ $document->id }}, '{{ $document->title }}', 'reject')"
                                                    class="bg-red-100 hover:bg-red-200 text-red-800 px-4 py-2 rounded-md text-sm font-medium">
                                                ❌ Reject
                                            </button>
                                        @endif

                                        @if($document->image_path)
                                            <a href="{{ Storage::url($document->image_path) }}"
                                               target="_blank"
                                               class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                                                🖼️ View Image
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No documents awaiting review</h3>
                            <p class="text-gray-600">Documents requiring your approval will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Review Document</h3>

                <form id="reviewForm" method="POST">
                    @csrf
                    <input type="hidden" name="action" id="reviewAction">

                    <div class="mb-4">
                        <label for="authority_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes:</label>
                        <textarea name="authority_notes"
                                  id="authority_notes"
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Add your review notes here..."
                                  required></textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeReviewModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" id="submitBtn" class="px-4 py-2 text-white rounded-md">
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showReviewModal(documentId, documentTitle, action) {
            const modal = document.getElementById('reviewModal');
            const form = document.getElementById('reviewForm');
            const title = document.getElementById('modalTitle');
            const actionInput = document.getElementById('reviewAction');
            const submitBtn = document.getElementById('submitBtn');

            // Set form action
            form.action = `/documents/${documentId}/review`;
            actionInput.value = action;

            if (action === 'approve') {
                title.textContent = `Approve: ${documentTitle}`;
                submitBtn.textContent = 'Approve Document';
                submitBtn.className = 'px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700';
            } else {
                title.textContent = `Reject: ${documentTitle}`;
                submitBtn.textContent = 'Reject Document';
                submitBtn.className = 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700';
            }

            modal.classList.remove('hidden');
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').classList.add('hidden');
            document.getElementById('authority_notes').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('reviewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });
    </script>
</x-app-layout>
