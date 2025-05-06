<x-app-layout>
    <x-slot name="header">
      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight">
          {{ __('My Expenses') }}
        </h2>
        <a href="{{ route('expenses.create') }}"
           class="btn btn-primary btn-sm">
          + Add Expense
        </a>
      </div>
    </x-slot>
  
    <main class="py-8">
      <div class="container space-y-8">
  
        @if(session('success'))
          <div class="card border-l-4 border-green-500">
            <div class="card-body bg-green-50 text-green-800">
              {{ session('success') }}
            </div>
          </div>
        @endif
  
        @forelse ($expenses as $month => $monthlyExpenses)
          <section class="space-y-4">
  
            {{-- Month Header --}}
            <div class="bg-indigo-100 dark:bg-gray-800 rounded-lg px-5 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center">
              <span class="text-lg font-medium text-indigo-800 dark:text-indigo-200">
                {{ $month }}
              </span>
              <div class="mt-2 sm:mt-0 flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ route('expenses.breakdown', ['month' => $month]) }}"
                   class="link">
                  View More
                </a>
                <span>
                  Total: 
                  <strong>RM {{ number_format($rmTotals[$month] ?? 0, 2) }}</strong>
                  → 
                  <strong>{{ number_format($convertedTotals[$month] ?? 0, 2) }} {{ $currency }}</strong>
                </span>
              </div>
            </div>
  
            {{-- Expenses Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              @foreach ($monthlyExpenses as $expense)
                <div class="card hover:shadow-lg transition-shadow">
                  <div class="card-body flex flex-col justify-between h-full">
                    
                    {{-- Title --}}
                    <div>
                      <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        {{ $expense->title }}
                      </h4>
                      
                      {{-- Amounts, Date & Category --}}
                      <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        <span class="font-medium">RM {{ number_format($expense->amount, 2) }}</span>
                        <span class="ml-1 text-gray-800 dark:text-gray-100">
                          → {{ number_format($expense->converted_amount, 2) }} {{ $currency }}
                        </span>
                        • {{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }} • {{ $expense->category }}
                      </p>
  
                      @if($expense->notes)
                        <p class="mt-2 text-sm italic text-gray-500 dark:text-gray-300">
                          {{ $expense->notes }}
                        </p>
                      @endif
                    </div>
  
                    {{-- Actions --}}
                    <div class="mt-4 flex flex-wrap gap-2">
                      <a href="{{ route('expenses.edit', $expense->id) }}"
                         class="btn btn-outline btn-sm">
                        Edit
                      </a>
                      <a href="{{ route('expenses.show', $expense->id) }}"
                         class="btn btn-outline btn-sm">
                        View
                      </a>
                      <form action="{{ route('expenses.destroy', $expense->id) }}"
                            method="POST"
                            onsubmit="return confirm('Delete this expense?');">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="btn btn-outline btn-sm text-red-600 dark:text-red-400">
                          Delete
                        </button>
                      </form>
                    </div>
  
                  </div>
                </div>
              @endforeach
            </div>
          </section>
        @empty
          <p class="text-center text-gray-500 dark:text-gray-400">
            No expenses recorded yet.
          </p>
        @endforelse
  
      </div>
    </main>
  </x-app-layout>
  