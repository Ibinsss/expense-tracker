@php
    $categoryOptions = ['Food', 'Transport', 'Bills', 'Shopping', 'Health', 'Entertainment', 'Education'];
    $selectedCategory = old('category', $expense->category ?? '');
@endphp

<div class="space-y-6">

  {{-- Title --}}
  <div>
    <label for="title" class="block text-sm font-medium mb-1">
      Title
    </label>
    <input
      type="text"
      name="title"
      id="title"
      value="{{ old('title', $expense->title ?? '') }}"
      placeholder="e.g. Grocery, Lunch with client"
      class="block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 
             border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
             focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >
    @error('title')
      <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror
  </div>

  {{-- Amount --}}
  <div>
    <label for="amount" class="block text-sm font-medium mb-1">
      Amount (RM)
    </label>
    <input
      type="number"
      step="0.01"
      name="amount"
      id="amount"
      value="{{ old('amount', $expense->amount ?? '') }}"
      placeholder="e.g. 45.50"
      class="block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 
             border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
             focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >
    @error('amount')
      <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror
  </div>

  {{-- Date --}}
  <div>
    <label for="date" class="block text-sm font-medium mb-1">
      Date
    </label>
    <input
      type="date"
      name="date"
      id="date"
      value="{{ old('date', $expense->date ?? '') }}"
      class="block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 
             border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
             focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >
    @error('date')
      <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror
  </div>

  {{-- Category --}}
  <div>
    <label for="category-select" class="block text-sm font-medium mb-1">
      Category
    </label>
    <select
      name="category"
      id="category-select"
      onchange="toggleCustomCategory(this.value)"
      class="block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 
             border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
             focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >
      <option disabled value="" {{ $selectedCategory === '' ? 'selected' : '' }}>
        -- Select Category --
      </option>
      @foreach ($categoryOptions as $cat)
        <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>
          {{ $cat }}
        </option>
      @endforeach
      <option value="Other" {{ !in_array($selectedCategory, $categoryOptions) ? 'selected' : '' }}>
        Other
      </option>
    </select>
    @error('category')
      <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror

    <input
      type="text"
      name="category"
      id="custom-category"
      placeholder="Enter custom category"
      value="{{ !in_array($selectedCategory, $categoryOptions) ? $selectedCategory : '' }}"
      class="mt-3 block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 
             border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
             focus:outline-none focus:ring-2 focus:ring-indigo-500 hidden"
      disabled
    >
  </div>

  {{-- Notes --}}
  <div>
    <label for="notes" class="block text-sm font-medium mb-1">
      Notes (optional)
    </label>
    <textarea
      name="notes"
      id="notes"
      rows="3"
      placeholder="Add any notes…"
      class="block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 
             border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
             focus:outline-none focus:ring-2 focus:ring-indigo-500"
    >{{ old('notes', $expense->notes ?? '') }}</textarea>
    @error('notes')
      <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror
  </div>

</div>

{{-- Toggle “Other” category input --}}
<script>
  function toggleCustomCategory(val) {
    const input = document.getElementById('custom-category');
    if (val === 'Other') {
      input.disabled = false;
      input.classList.remove('hidden');
      input.focus();
    } else {
      input.disabled = true;
      input.classList.add('hidden');
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    toggleCustomCategory(document.getElementById('category-select').value);
  });
</script>
