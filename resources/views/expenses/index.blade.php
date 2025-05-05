<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl leading-tight">
            {{ __('My Expenses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 text-green-800 rounded p-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Add Button -->
            <div class="flex justify-end">
                <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                    + Add Expense
                </a>
            </div>

            <!-- Grouped by Month -->
            @forelse ($expenses as $month => $monthlyExpenses)
            <div class="bg-indigo-600 text-white px-4 py-2 rounded shadow flex justify-between items-center">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                    <h3 class="text-lg font-semibold mr-2">{{ $month }}</h3>
                    <a href="{{ route('expenses.breakdown', ['month' => $month]) }}"
                       class="text-sm underline hover:text-gray-200">
                        View More
                    </a>
                </div>
                <p class="text-sm">Total: RM {{ number_format($totals[$month], 2) }}</p>
            </div>
            

                @foreach ($monthlyExpenses as $expense)
                    <div class="card">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-lg font-semibold">{{ $expense->title }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    RM {{ number_format($expense->amount, 2) }} •
                                    {{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }} •
                                    {{ $expense->category }}
                                </p>
                                @if ($expense->notes)
                                    <p class="text-sm mt-1 text-gray-600 dark:text-gray-300">
                                        <em>{{ $expense->notes }}</em>
                                    </p>
                                @endif
                            </div>

                            <div class="flex gap-2 text-sm">
                                <a href="{{ route('expenses.edit', $expense->id) }}"
                                   class="btn btn-outline text-yellow-500">Edit</a>

                                <a href="{{ route('expenses.show', $expense->id) }}"
                                   class="btn btn-outline text-blue-500">View</a>

                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline text-red-500">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @empty
                <p class="text-gray-500 dark:text-gray-300">No expenses recorded yet.</p>
            @endforelse

        </div>
    </div>
</x-app-layout>
