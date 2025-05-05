<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-white">
            Expense Breakdown: {{ $month }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto space-y-6">
            @if(session('success'))
                <div class="bg-green-100 text-green-800 rounded p-4 text-center">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Chart Summary -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded shadow text-center">
                <p class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                    Total Spent: RM {{ number_format($categoryTotals->sum(), 2) }}
                </p>

                <div class="flex justify-center">
                    <div style="width: 300px; height: 300px;">
                        <canvas id="categoryChart" width="300" height="300"></canvas>
                    </div>
                </div>

                {{--}}
                <div class="flex justify-center gap-4 mt-6 flex-wrap">
                    <a href="{{ route('expenses.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium px-4 py-2 rounded shadow">
                        ← Back to Expenses
                    </a>

                    <button onclick="document.getElementById('emailModal').classList.remove('hidden')"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded shadow">
                        📧 Email this breakdown
                    </button> 
                </div> --}}
            </div>

            <!-- Table Breakdown -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded shadow">
                <h3 class="text-md font-bold mb-4 text-gray-800 dark:text-white">Breakdown by Category</h3>
                <table class="w-full text-sm text-left text-gray-800 dark:text-gray-200">
                    <thead class="text-xs uppercase text-gray-600 dark:text-gray-400 border-b border-gray-300 dark:border-gray-600">
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

    <!-- Modal for Email Input -->
    <div id="emailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded shadow-lg w-full max-w-md">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Send Breakdown via Email</h3>
            <form method="POST" action="{{ route('expenses.breakdown.email', $month) }}">
                @csrf
                <label class="block mb-2 text-sm text-gray-700 dark:text-gray-300">Recipient Email:</label>
                <input type="email" name="email" value="{{ Auth::user()->email }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded text-black">

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('emailModal').classList.add('hidden')"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Chart Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const data = {
            labels: {!! json_encode($categoryTotals->keys()) !!},
            datasets: [{
                label: 'Category Breakdown',
                data: {!! json_encode($categoryTotals->values()) !!},
                backgroundColor: [
                    '#6366F1', '#F59E0B', '#10B981', '#EF4444', '#3B82F6', '#8B5CF6', '#EC4899'
                ],
                borderColor: '#1f2937',
                borderWidth: 2
            }]
        };

        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: getComputedStyle(document.documentElement)
                                .getPropertyValue('--tw-text-opacity') === '1'
                                ? '#1f2937' : '#e5e7eb'
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>
