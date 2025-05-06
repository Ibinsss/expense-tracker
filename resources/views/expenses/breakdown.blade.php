<x-app-layout>
    <x-slot name="header">
      <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight leading-tight">
        {{ __('Expense Breakdown: ') }}{{ $month }}
      </h2>
    </x-slot>
  
    <main class="py-8">
      <div class="container space-y-8">
  
        {{-- Success Message --}}
        @if(session('success'))
          <div class="card border-l-4 border-green-500 max-w-3xl mx-auto">
            <div class="card-body bg-green-50 text-green-800 text-center">
              {{ session('success') }}
            </div>
          </div>
        @endif
  
        {{-- Chart Summary --}}
        <div class="card max-w-3xl mx-auto">
          <div class="card-body space-y-6 text-center">
            <p class="text-lg font-semibold text-gray-800 dark:text-gray-100">
              Total Spent: RM {{ number_format($categoryTotals->sum(), 2) }}
            </p>
  
            <div class="w-60 h-60 mx-auto">
              <canvas id="categoryChart"></canvas>
            </div>
  
            <div class="flex flex-col sm:flex-row justify-center gap-4">
              <a href="{{ route('expenses.index') }}"
                 class="btn btn-outline btn-sm">
                ← Back to Expenses
              </a>
              <button onclick="document.getElementById('emailModal').classList.toggle('hidden')"
                      class="btn btn-primary btn-sm">
                📧 Email this breakdown
              </button>
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
                  <th class="py-2 text-right">Percentage</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($categoryTotals as $category => $amount)
                  <tr class="border-t border-gray-300 dark:border-gray-700">
                    <td class="py-2">{{ $category }}</td>
                    <td class="py-2 text-right">{{ number_format($amount, 2) }}</td>
                    <td class="py-2 text-right">
                      {{ number_format(($amount / $categoryTotals->sum()) * 100, 1) }}%
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
  
      </div>
    </main>
  
    {{-- Email Modal --}}
    <div id="emailModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
          Send Breakdown via Email
        </h3>
        <form method="POST" action="{{ route('expenses.breakdown.email', $month) }}">
          @csrf
          <label class="block mb-2 text-sm text-gray-700 dark:text-gray-300">
            Recipient Email:
          </label>
          <input type="email"
                 name="email"
                 value="{{ Auth::user()->email }}"
                 required
                 class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md
                        focus:outline-none focus:ring-2 focus:ring-indigo-400 text-gray-800 dark:text-gray-200">
  
          <div class="flex justify-end gap-3 mt-4">
            <button type="button"
                    onclick="document.getElementById('emailModal').classList.add('hidden')"
                    class="btn btn-outline btn-sm">
              Cancel
            </button>
            <button type="submit"
                    class="btn btn-primary btn-sm">
              Send
            </button>
          </div>
        </form>
      </div>
    </div>
  
    {{-- Chart.js --}}
   {{-- Chart.js --}}
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script>
     const darkMode = document.documentElement.classList.contains('dark');
     const textColor = darkMode ? '#f3f4f6' : '#1f2937'; // gray-100 vs. gray-800
   
     const ctx = document.getElementById('categoryChart').getContext('2d');
     new Chart(ctx, {
       type: 'doughnut',
       data: {
         labels: {!! json_encode($categoryTotals->keys()) !!},
         datasets: [{
           data:  {!! json_encode($categoryTotals->values()) !!},
           backgroundColor: [
             'rgb(99,102,241)', 'rgb(245,158,11)', 'rgb(16,185,129)',
             'rgb(239,68,68)',  'rgb(59,130,246)', 'rgb(139,92,246)', 'rgb(236,72,153)'
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
             labels: {
               color: '#ffffff',   // <— force pure white in all modes
               // or you could use textColor if you only want white in dark:
               // color: darkMode ? '#ffffff' : '#1f2937',
               boxWidth: 20,
               padding: 16,
               usePointStyle: true
             }
           }
         }
       }
     });
   </script>
   </x-app-layout>
  