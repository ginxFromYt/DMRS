<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event Manager Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Welcome, Alice!</h3>
                    <p class="text-gray-600">Manage events across University, Internal Campus, and External Partners categories.</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-purple-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-purple-600">Total Events</p>
                                <p class="text-2xl font-semibold text-purple-900">{{ $stats['total_events'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h4a1 1 0 011 1v5m-6 0V9a1 1 0 011-1h4a1 1 0 011 1v11"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-blue-600">University</p>
                                <p class="text-2xl font-semibold text-blue-900">{{ $stats['university_events'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-green-600">Internal Campus</p>
                                <p class="text-2xl font-semibold text-green-900">{{ $stats['campus_events'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-orange-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 9c-5 0-9-4-9-9s4-9 9-9"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-orange-600">External Partners</p>
                                <p class="text-2xl font-semibold text-orange-900">{{ $stats['external_events'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create New Event Button -->
            <div class="mb-6">
                <a href="{{ route('events.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium inline-flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create New Event
                </a>
            </div>

            <!-- Events by Category -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- University Events -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-blue-800">🏛️ University Events</h3>
                            <a href="{{ route('events.index', ['category' => 'university']) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                View All →
                            </a>
                        </div>

                        @if(isset($universityEvents) && $universityEvents->count() > 0)
                            <div class="space-y-3">
                                @foreach($universityEvents->take(3) as $event)
                                    <div class="border border-blue-200 rounded-lg p-3 bg-blue-50">
                                        <h4 class="font-medium text-gray-900 mb-1">{{ $event->title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($event->description, 60) }}</p>
                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                            <span>{{ $event->event_date->format('M d, Y') }}</span>
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $event->status }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No university events.</p>
                        @endif
                    </div>
                </div>

                <!-- Internal Campus Events -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-green-800">🏫 Internal Campus</h3>
                            <a href="{{ route('events.index', ['category' => 'internal_campus']) }}" class="text-green-600 hover:text-green-800 text-sm">
                                View All →
                            </a>
                        </div>

                        @if(isset($campusEvents) && $campusEvents->count() > 0)
                            <div class="space-y-3">
                                @foreach($campusEvents->take(3) as $event)
                                    <div class="border border-green-200 rounded-lg p-3 bg-green-50">
                                        <h4 class="font-medium text-gray-900 mb-1">{{ $event->title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($event->description, 60) }}</p>
                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                            <span>{{ $event->event_date->format('M d, Y') }}</span>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">{{ $event->status }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No campus events.</p>
                        @endif
                    </div>
                </div>

                <!-- External Partner Events -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-orange-800">🤝 External Partners</h3>
                            <a href="{{ route('events.index', ['category' => 'external_partners']) }}" class="text-orange-600 hover:text-orange-800 text-sm">
                                View All →
                            </a>
                        </div>

                        @if(isset($externalEvents) && $externalEvents->count() > 0)
                            <div class="space-y-3">
                                @foreach($externalEvents->take(3) as $event)
                                    <div class="border border-orange-200 rounded-lg p-3 bg-orange-50">
                                        <h4 class="font-medium text-gray-900 mb-1">{{ $event->title }}</h4>
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($event->description, 60) }}</p>
                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                            <span>{{ $event->event_date->format('M d, Y') }}</span>
                                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded">{{ $event->status }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No external events.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">📋 Recent Events Activity</h3>
                        <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Manage All Events →
                        </a>
                    </div>

                    @if(isset($recentEvents) && $recentEvents->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentEvents->take(5) as $event)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-medium text-gray-900">{{ $event->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ $event->description }}</p>
                                            <div class="flex items-center mt-2 space-x-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($event->category === 'university') bg-blue-100 text-blue-800
                                                    @elseif($event->category === 'internal_campus') bg-green-100 text-green-800
                                                    @elseif($event->category === 'external_partners') bg-orange-100 text-orange-800
                                                    @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $event->category)) }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    📅 {{ $event->event_date->format('M d, Y h:i A') }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    📍 {{ $event->location }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('events.edit', $event) }}"
                                               class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded-md text-sm">
                                                ✏️ Edit
                                            </a>
                                            <a href="{{ route('events.show', $event) }}"
                                               class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-md text-sm">
                                                👁️ View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p>No events created yet.</p>
                            <a href="{{ route('events.create') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Create your first event →
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
</x-app-layout>
