@php
    $categoryOptions = ['Food', 'Transport', 'Bills', 'Shopping', 'Health', 'Entertainment', 'Education'];
    $selectedCategory = old('category', $expense->category ?? '');
@endphp

<div class="space-y-4">
    <!-- Title -->
    <div>
        <label for="title" class="block text-sm font-medium mb-1">Title</label>
        <input type="text" name="title" id="title"
            value="{{ old('title', $expense->title ?? '') }}"
            placeholder="e.g. Grocery, Lunch with client"
            class="w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <!-- Amount -->
    <div>
        <label for="amount" class="block text-sm font-medium mb-1">Amount (RM)</label>
        <input type="number" step="0.01" name="amount" id="amount"
            value="{{ old('amount', $expense->amount ?? '') }}"
            placeholder="e.g. 45.50"
            class="w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <!-- Date -->
    <div>
        <label for="date" class="block text-sm font-medium mb-1">Date</label>
        <input type="date" name="date" id="date"
            value="{{ old('date', $expense->date ?? '') }}"
            class="w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>

    <!-- Category (Select + Optional Input) -->
    <div>
        <label for="category" class="block text-sm font-medium mb-1">Category</label>
        <select name="category" id="category-select"
            class="w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            onchange="toggleCustomCategory(this.value)">
            <option disabled value="" class="text-gray-500 dark:text-gray-400" {{ $selectedCategory == '' ? 'selected' : '' }}>
                -- Select Category --
            </option>
            @foreach ($categoryOptions as $cat)
                <option value="{{ $cat }}" class="text-gray-800 dark:text-gray-100"
                    {{ $selectedCategory === $cat ? 'selected' : '' }}>
                    {{ $cat }}
                </option>
            @endforeach
            <option value="Other" class="text-gray-800 dark:text-gray-100"
                {{ !in_array($selectedCategory, $categoryOptions) ? 'selected' : '' }}>
                Other
            </option>
        </select>

        <!-- Custom input for "Other" -->
        <input type="text" name="category" id="custom-category"
            placeholder="Enter your category"
            value="{{ !in_array($selectedCategory, $categoryOptions) ? $selectedCategory : '' }}"
            class="mt-2 w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden">
    </div>

    <!-- Notes -->
    <div>
        <label for="notes" class="block text-sm font-medium mb-1">Notes</label>
        <textarea name="notes" id="notes" rows="3"
            placeholder="Optional notes about this expense..."
            class="w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes', $expense->notes ?? '') }}</textarea>
    </div>
</div>

<!-- Toggle custom category JS -->
<script>
    function toggleCustomCategory(value) {
        const customInput = document.getElementById('custom-category');
        if (value === 'Other') {
            customInput.classList.remove('hidden');
            customInput.removeAttribute('disabled');
            customInput.focus();
        } else {
            customInput.classList.add('hidden');
            customInput.setAttribute('disabled', 'disabled');
        }
    }

    // Auto trigger on page load
    document.addEventListener('DOMContentLoaded', function () {
        toggleCustomCategory(document.getElementById('category-select').value);
    });
</script>
