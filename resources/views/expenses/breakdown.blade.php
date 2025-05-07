<x-app-layout>
  <x-slot name="header">
    <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight leading-tight">
      {{ __('Expense Breakdown: ') }}{{ $month ?? 'Unknown Month' }}
    </h2>
  </x-slot>

  <main class="py-8">
    <div class="container space-y-8">

      @if(session('success'))
        <div class="card border-l-4 border-green-500 max-w-3xl mx-auto">
          <div class="card-body bg-green-50 text-green-800 text-center">
            {{ session('success') }}
          </div>
        </div>
      @endif

      {{-- Chart & Totals --}}
      <div class="card max-w-3xl mx-auto">
        <div class="card-body space-y-6 text-center">
          <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            Total Spent:
            <strong>
              RM {{ isset($categoryBreakdown) ? number_format(collect($categoryBreakdown)->sum(), 2) : '0.00' }}
            </strong>
            &nbsp;→&nbsp;
            <strong>
              {{ isset($convertedBreakdown) ? number_format(collect($convertedBreakdown)->sum(), 2) : '0.00' }} {{ $currency ?? '' }}
            </strong>
          </p>

          <div class="w-60 h-60 mx-auto">
            <canvas id="categoryChart"></canvas>
          </div>

          <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline btn-sm">
              ← Back to Expenses
            </a>
             {{-- <button onclick="document.getElementById('emailModal').classList.toggle('hidden')"
                    class="btn btn-primary btn-sm">
              📧 Email this breakdown
            </button>--}}
          </div>
        </div>
      </div>

      {{-- Table Breakdown --}}
      <div class="card max-w-3xl mx-auto">
        <div class="card-body overflow-x-auto">
          <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">
            Breakdown by Category
          </h3>
          <table class="w-full text-sm text-left text-gray-800 dark:text-gray-200">
            <thead class="uppercase text-gray-600 dark:text-gray-400 border-b border-gray-300 dark:border-gray-600">
              <tr>
                <th class="py-2">Category</th>
                <th class="py-2 text-right">Amount (RM)</th>
                <th class="py-2 text-right">Converted ({{ $currency ?? '' }})</th>
              </tr>
            </thead>
            <tbody>
              @if(isset($categoryBreakdown) && count($categoryBreakdown))
                @foreach($categoryBreakdown as $category => $amount)
                  <tr class="border-t border-gray-300 dark:border-gray-700">
                    <td class="py-2">{{ $category }}</td>
                    <td class="py-2 text-right">RM {{ number_format($amount, 2) }}</td>
                    <td class="py-2 text-right">
                      {{ number_format($convertedBreakdown[$category] ?? 0, 2) }} {{ $currency ?? '' }}
                    </td>
                  </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="3" class="text-center py-4 text-gray-500">No data available for this month.</td>
                </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

  {{-- Email Modal (optional) --}}
  {{-- @include('expenses.partials.email-modal') --}}

  {{-- Chart.js --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const darkMode = document.documentElement.classList.contains('dark');
    const textColor = darkMode ? '#f3f4f6' : '#1f2937';

    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: {!! isset($categoryBreakdown) ? json_encode(array_keys($categoryBreakdown->toArray())) : '[]' !!},
        datasets: [{
          data: {!! isset($categoryBreakdown) ? json_encode(array_values($categoryBreakdown->toArray())) : '[]' !!},
          backgroundColor: [
            'rgb(99,102,241)','rgb(245,158,11)','rgb(16,185,129)',
            'rgb(239,68,68)','rgb(59,130,246)','rgb(139,92,246)',
            'rgb(236,72,153)'
          ],
          borderColor: darkMode ? '#374151' : '#ffffff',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: { color: textColor, boxWidth:20, padding:16, usePointStyle:true }
          }
        }
      }
    });
  </script>
</x-app-layout>
