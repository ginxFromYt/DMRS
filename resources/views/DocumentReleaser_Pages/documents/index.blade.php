<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Release Center') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">🚀 Document Release Center</h3>
                            <p class="text-gray-600 mt-1">Release approved documents and send them to employees.</p>
                        </div>
                        <a href="{{ route('dashboard') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                            ← Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Documents Ready for Release -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">📋 Documents for Release</h3>

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
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                                        @if($document->status === 'reviewed_by_authority') bg-blue-100 text-blue-800
                                                        @elseif($document->status === 'released') bg-green-100 text-green-800
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">Approved:</span>
                                                    <span class="text-sm text-gray-700 ml-2">{{ $document->reviewed_at ? $document->reviewed_at->format('M d, Y h:i A') : 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-xs text-gray-500">Original:</span>
                                                    <span class="text-sm text-gray-700 ml-2">{{ $document->user->first_name }} {{ $document->user->last_name }}</span>
                                                </div>
                                            </div>

                                            @if($document->authority_notes)
                                                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                                                    <h5 class="text-sm font-medium text-blue-800 mb-2">📝 Authority Notes:</h5>
                                                    <p class="text-sm text-blue-700">{{ $document->authority_notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('documents.show', $document) }}"
                                           class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-md text-sm font-medium">
                                            👁️ View Document
                                        </a>

                                        @if($document->status === 'reviewed_by_authority')
                                            <form action="{{ route('documents.release', $document) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="bg-green-100 hover:bg-green-200 text-green-800 px-4 py-2 rounded-md text-sm font-medium"
                                                        onclick="return confirm('Release this document for distribution?')">
                                                    🚀 Release Document
                                                </button>
                                            </form>
                                        @elseif($document->status === 'released')
                                            <button onclick="showEmployeeModal({{ $document->id }}, '{{ $document->title }}')"
                                                    class="bg-orange-100 hover:bg-orange-200 text-orange-800 px-4 py-2 rounded-md text-sm font-medium">
                                                📤 Send to Employee
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
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No documents ready for release</h3>
                            <p class="text-gray-600">Approved documents ready for release will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Selection Modal -->
    <div id="employeeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Select Employee</h3>
                <p class="text-sm text-gray-600 mb-4">Choose an employee to send the document to:</p>

                <form id="sendEmployeeForm" method="POST">
                    @csrf
                    <select id="employee_id" name="employee_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4" required>
                        <option value="">Select an employee...</option>
                    </select>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeEmployeeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Send Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let employees = [];

        // Fetch employees on page load
        fetch('{{ route("documents.employees") }}')
            .then(response => response.json())
            .then(data => {
                employees = data;
            });

        function showEmployeeModal(documentId, documentTitle) {
            const modal = document.getElementById('employeeModal');
            const form = document.getElementById('sendEmployeeForm');
            const select = document.getElementById('employee_id');

            // Set form action
            form.action = `/documents/${documentId}/send-employee`;

            // Clear and populate select
            select.innerHTML = '<option value="">Select an employee...</option>';
            employees.forEach(employee => {
                const option = document.createElement('option');
                option.value = employee.id;
                option.textContent = `${employee.first_name} ${employee.last_name} (${employee.email})`;
                select.appendChild(option);
            });

            modal.classList.remove('hidden');
        }

        function closeEmployeeModal() {
            document.getElementById('employeeModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('employeeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEmployeeModal();
            }
        });
    </script>
</x-app-layout>
