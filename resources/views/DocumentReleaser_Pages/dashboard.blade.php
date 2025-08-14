<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Releaser Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Welcome, Jasmin!</h3>
                    <p class="text-gray-600">Release approved documents and send them to designated employees.</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">Pending Release</p>
                                <p class="text-2xl font-semibold text-blue-900">{{ $stats['pending_release'] ?? 0 }}</p>
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
                                <p class="text-sm font-medium text-green-600">Released Documents</p>
                                <p class="text-2xl font-semibold text-green-900">{{ $stats['released_documents'] ?? 0 }}</p>
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
                                <p class="text-sm font-medium text-yellow-600">Sent Today</p>
                                <p class="text-2xl font-semibold text-yellow-900">{{ $stats['sent_today'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-purple-600">Total Processed</p>
                                <p class="text-2xl font-semibold text-purple-900">{{ $stats['total_processed'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Ready for Release -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">📋 Documents Ready for Release</h3>
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
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($document->status === 'forwarded_to_releaser') bg-blue-100 text-blue-800
                                                    @elseif($document->status === 'released') bg-green-100 text-green-800
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    @if($document->status === 'forwarded_to_releaser')
                                                        Forwarded {{ $document->forwarded_to_releaser_at ? $document->forwarded_to_releaser_at->diffForHumans() : 'N/A' }}
                                                    @else
                                                        Reviewed {{ $document->reviewed_at ? $document->reviewed_at->diffForHumans() : 'N/A' }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Authority Notes -->
                                    @if($document->authority_notes)
                                        <div class="mb-4 p-3 bg-yellow-50 rounded">
                                            <h5 class="text-sm font-medium text-yellow-800 mb-2">📝 Authority Notes:</h5>
                                            <p class="text-sm text-yellow-700">{{ $document->authority_notes }}</p>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="flex space-x-3">
                                        @if($document->status === 'forwarded_to_releaser')
                                            <form action="{{ route('documents.release', $document) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200"
                                                        onclick="return confirm('Release this document for distribution?')">
                                                    🚀 Release Document
                                                </button>
                                            </form>
                                        @elseif($document->status === 'released')
                                            <button type="button"
                                                    onclick="showEmployeeModal({{ $document->id }}, '{{ $document->title }}')"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                                📤 Send to Employee
                                            </button>
                                        @endif

                                        <a href="{{ route('documents.show', $document) }}"
                                           class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                                            📄 View Document
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>No documents ready for release at this time.</p>
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
                        <!-- Will be populated by JavaScript -->
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
