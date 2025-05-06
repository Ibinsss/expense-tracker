<x-app-layout>
    <x-slot name="header">
      <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight leading-tight">
        {{ __('Edit Expense') }}
      </h2>
    </x-slot>
  
    <main class="py-8">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="card max-w-xl mx-auto">
          <div class="card-body space-y-6">
  
            <form action="{{ route('expenses.update', $expense) }}"
                  method="POST"
                  enctype="multipart/form-data">
              @csrf
              @method('PUT')
  
              {{-- Common fields --}}
              @include('expenses.form')
  
              {{-- Replace Receipt --}}
              <div>
                <label for="receipt" class="block text-sm font-medium mb-1">
                  Replace Receipt
                </label>
                <input
                  type="file"
                  name="receipt"
                  id="receipt"
                  accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
                  class="block w-full text-sm text-gray-700 dark:text-gray-200 
                         border border-gray-300 dark:border-gray-600 rounded-md
                         focus:outline-none focus:ring-2 focus:ring-indigo-400 
                         cursor-pointer"
                >
                @error('receipt')
                  <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
              </div>
  
              {{-- Current Receipt Link --}}
              @if($expense->receipt_path)
                <div>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">
                    Current receipt:
                  </p>
                  <a href="{{ asset('storage/' . $expense->receipt_path) }}"
                     target="_blank"
                     class="link">
                    Download / View
                  </a>
                </div>
              @endif
  
              {{-- Submit --}}
              <div class="flex justify-end">
                <button type="submit" class="btn btn-primary btn-lg">
                  Update
                </button>
              </div>
            </form>
  
          </div>
        </div>
  
      </div>
    </main>
  </x-app-layout>
  