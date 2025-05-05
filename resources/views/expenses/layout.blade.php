<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl leading-tight">
            {{ __('Expense Tracker') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto space-y-6">
            @if (session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                @yield('content')
            </div>
        </div>
    </div>
</x-app-layout>
