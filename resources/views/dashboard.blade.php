<x-app-layout>
    <x-slot name="header">
      <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight leading-tight">
        {{ __('Dashboard') }}
      </h2>
    </x-slot>
  
    <main class="py-12">
      <div class="container space-y-6">
  
        {{-- Welcome Card --}}
        <div class="card">
          <div class="card-body">
            <h3 class="text-2xl sm:text-3xl font-semibold">
              Welcome, {{ Auth::user()->name }}!
            </h3>
            <p class="mt-2 text-base sm:text-lg">
              Here to track your wallet again? 💸
            </p>
          </div>
        </div>
  
        {{-- Recent Expenses --}}
        <div class="card">
          <div class="card-body">
            <h4 class="text-lg sm:text-xl font-semibold mb-4">
              Recent Expenses
            </h4>
  
            @if ($expenses->count())
              <ul class="space-y-3">
                @foreach ($expenses as $expense)
                  <li class="border-b border-gray-200 dark:border-gray-700 pb-2">
                    <div class="font-medium">
                      {{ $expense->title }} &mdash; RM {{ number_format($expense->amount, 2) }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ $expense->category }} • {{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}
                    </div>
                  </li>
                @endforeach
              </ul>
  
              <div class="mt-4">
                <a href="{{ route('expenses.index') }}"
                   class="btn btn-outline">
                  View all expenses →
                </a>
              </div>
            @else
              <p class="text-gray-500">No expenses recorded yet.</p>
            @endif
          </div>
        </div>
  
      </div>
    </main>
  </x-app-layout>
  