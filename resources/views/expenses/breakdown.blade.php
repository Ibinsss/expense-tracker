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
            <strong>RM {{ number_format(collect($categoryBreakdown ?? [])->sum(), 2) }}</strong>
            &nbsp;→&nbsp;
            <strong>{{ number_format(collect($convertedBreakdown ?? [])->sum(), 2) }} {{ $currency ?? '' }}</strong>
          </p>

          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-center gap-8">
            {{-- Chart --}}
            <div class="w-60 h-60 mx-auto sm:mx-0">
              <canvas id="categoryChart"></canvas>
            </div>

            {{-- Legend --}}
            @if(isset($categoryBreakdown) && count($categoryBreakdown))
              <div class="flex flex-col gap-2 text-left text-sm text-gray-700 dark:text-gray-300">
                @foreach($categoryBreakdown as $category => $amount)
                  <div class="flex items-center gap-2">
                    <span class="inline-block w-3 h-3 rounded-full"
                          style="background-color: {{ [
                            '#6366f1', '#f59e0b', '#10b981',
                            '#ef4444', '#3b82f6', '#8b5cf6',
                            '#ec4899'
                          ][$loop->index % 7] }}"></span>
                    <span>{{ $category }}</span>
                  </div>
                @endforeach
              </div>
            @endif
          </div>

          <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline btn-sm">
              ← Back to Expenses
            </a>
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

  {{-- Chart.js --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const darkMode = document.documentElement.classList.contains('dark');
    const textColor = darkMode ? '#f3f4f6' : '#1f2937';

    const ctx = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: {!! json_encode(array_keys($categoryBreakdown->toArray() ?? [])) !!},
        datasets: [{
          data: {!! json_encode(array_values($categoryBreakdown->toArray() ?? [])) !!},
          backgroundColor: [
            '#6366f1', '#f59e0b', '#10b981',
            '#ef4444', '#3b82f6', '#8b5cf6',
            '#ec4899'
          ],
          borderColor: darkMode ? '#374151' : '#ffffff',
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        }
      }
    });
  </script>
</x-app-layout>
