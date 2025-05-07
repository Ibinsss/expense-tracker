<x-app-layout>
    <x-slot name="header">
      <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight leading-tight">
        {{ __('Add New Expense') }}
      </h2>
    </x-slot>
  
    <main class="py-8">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
  
        <div class="card max-w-xl mx-auto">
          <div class="card-body space-y-6">
  
            <form action="{{ route('expenses.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
              @csrf
  
              {{-- your existing fields --}}
              @include('expenses.form')
  
              {{-- Receipt Upload
              <div>
                <label for="receipt" class="block text-sm font-medium mb-1">
                  Receipt (jpg, png, pdf, doc)
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
              </div> --}}
  
              {{-- Submit --}}
              <div class="flex justify-end">
                <button type="submit" class="btn btn-primary btn-lg">
                  Save
                </button>
              </div>
            </form>
  
          </div>
        </div>
  
      </div>
    </main>
  </x-app-layout>
  