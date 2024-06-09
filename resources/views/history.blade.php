<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Appointment History') }}
        </h2>
    </x-slot>

    <div class="min-h-screen py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($appointments->isEmpty())
                        <p>No appointments found.</p>
                    @else
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="w-1/4 px-4 py-2">User</th>
                                    <th class="w-1/4 px-4 py-2">Date</th>
                                    <th class="w-1/4 px-4 py-2">Time</th>
                                    <th class="w-1/4 px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $appointment)
                                    <tr class="text-gray-700">
                                        <td class="border px-4 py-2">{{ $appointment->user->name }}</td>
                                        <td class="border px-4 py-2">{{ $appointment->date }}</td>
                                        <td class="border px-4 py-2">{{ $appointment->time }}</td>
                                        <td class="border px-4 py-2">{{ $appointment->status }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
