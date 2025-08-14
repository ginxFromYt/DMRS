<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Welcome, {{ auth()->user()->first_name }}!</h3>
                    <p class="text-gray-600">View and manage your assigned documents and tasks.</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-orange-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m-4 4v4a2 2 0 002 2h2m0-6V9a2 2 0 012-2h2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-orange-600">Assigned Documents</p>
                                <p class="text-2xl font-semibold text-orange-900">{{ $stats['assigned_documents'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-yellow-600">Seen Documents</p>
                                <p class="text-2xl font-semibold text-yellow-900">{{ $stats['seen_documents'] ?? 0 }}</p>
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
                                <p class="text-sm font-medium text-green-600">Completed Tasks</p>
                                <p class="text-2xl font-semibold text-green-900">{{ $stats['completed_tasks'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-red-600">Pending Action</p>
                                <p class="text-2xl font-semibold text-red-900">{{ $stats['pending_action'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Documents Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">📬 Assigned Documents</h3>
                        <a href="{{ route('documents.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All Documents →
                        </a>
                    </div>

                    @if(isset($documents) && $documents->count() > 0)
                        <div class="space-y-4">
                            @foreach($documents->where('status', 'sent_to_employee')->take(5) as $document)
                                <div class="border border-orange-200 rounded-lg p-4 bg-orange-50 hover:bg-orange-100">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-gray-900">{{ $document->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $document->description }}</p>
                                            <div class="flex items-center mt-2 space-x-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    New Assignment
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    Sent {{ $document->sent_at ? $document->sent_at->diffForHumans() : 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <form action="{{ route('documents.mark.seen', $document) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm">
                                                    👁️ Mark Seen
                                                </button>
                                            </form>
                                            <a href="{{ route('documents.show', $document) }}"
                                               class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-sm">
                                                📄 View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-500">
                            <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m-4 4v4a2 2 0 002 2h2m0-6V9a2 2 0 012-2h2"></path>
                            </svg>
                            <p>No assigned documents at this time.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Seen Documents -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">👀 Recently Seen</h3>
                        @if(isset($documents) && $documents->where('status', 'seen_by_employee')->count() > 0)
                            <div class="space-y-3">
                                @foreach($documents->where('status', 'seen_by_employee')->take(5) as $document)
                                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $document->title }}</h4>
                                            <p class="text-sm text-gray-600">Seen {{ $document->seen_at ? $document->seen_at->diffForHumans() : 'N/A' }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="showActionModal({{ $document->id }}, '{{ $document->title }}')"
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-sm">
                                                ✅ Action
                                            </button>
                                            <a href="{{ route('documents.show', $document) }}"
                                               class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-sm">
                                                📄 View
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No seen documents.</p>
                        @endif
                    </div>
                </div>

                <!-- Actioned Documents -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">✅ Recently Actioned</h3>
                        @if(isset($documents) && $documents->where('status', 'actioned_by_employee')->count() > 0)
                            <div class="space-y-3">
                                @foreach($documents->where('status', 'actioned_by_employee')->take(5) as $document)
                                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $document->title }}</h4>
                                            <p class="text-sm text-gray-600">Actioned {{ $document->actioned_at ? $document->actioned_at->diffForHumans() : 'N/A' }}</p>
                                            @if($document->employee_response)
                                                <p class="text-xs text-green-700 mt-1">{{ Str::limit($document->employee_response, 50) }}</p>
                                            @endif
                                        </div>
                                        <a href="{{ route('documents.show', $document) }}"
                                           class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-sm">
                                            📄 View
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No actioned documents.</p>
                        @endif
                    </div>
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
