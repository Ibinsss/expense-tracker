<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Welcome Card -->
            <div class="card">
                <h3 class="text-2xl font-bold">
                    Welcome, {{ Auth::user()->name }}!
                </h3>
                <p class="mt-2 text-lg">Here to track your wallet again? 💸</p>
            </div>

            <!-- Recent Expenses -->
            <div class="card">
                <h4 class="text-lg font-semibold mb-4">Recent Expenses</h4>

                @if ($expenses->count())
                    <ul class="space-y-3">
                        @foreach ($expenses as $expense)
                            <li class="border-b border-gray-200 dark:border-gray-700 pb-2">
                                <div class="font-medium">
                                    {{ $expense->title }} - RM {{ number_format($expense->amount, 2) }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $expense->category }} • {{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4">
                        <a href="{{ route('expenses.index') }}" class="link">View all expenses →</a>
                    </div>
                @else
                    <p class="text-gray-500">No expenses recorded yet.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
