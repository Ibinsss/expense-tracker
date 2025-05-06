<x-app-layout>
    <x-slot name="header">
      <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight leading-tight">
        {{ $expense->title }}
      </h2>
    </x-slot>
  
    <main class="py-8">
      <div class="container space-y-8">
  
        {{-- Expense Details --}}
        <div class="card max-w-2xl mx-auto">
          <div class="card-body space-y-6">
  
            {{-- Basic Info --}}
            <div class="space-y-2">
              <p>
                <span class="font-medium text-gray-700 dark:text-gray-300">Amount:</span>
                <span class="text-gray-800 dark:text-gray-100">RM {{ number_format($expense->amount, 2) }}</span>
              </p>
              <p>
                <span class="font-medium text-gray-700 dark:text-gray-300">Date:</span>
                <span class="text-gray-800 dark:text-gray-100">{{ $expense->date }}</span>
              </p>
              <p>
                <span class="font-medium text-gray-700 dark:text-gray-300">Category:</span>
                <span class="text-gray-800 dark:text-gray-100">{{ $expense->category }}</span>
              </p>
              <p>
                <span class="font-medium text-gray-700 dark:text-gray-300">Notes:</span>
                <span class="text-gray-800 dark:text-gray-100">{{ $expense->notes ?: '—' }}</span>
              </p>
            </div>
  
            {{-- Receipt Preview Logic --}}
            @php
              $disk    = app()->environment('production') ? 's3' : 'public';
              $hasFile = filled($expense->receipt_path);
              $hasBlob = filled($expense->receipt_src);
  
              $src = $hasFile
                   ? \Illuminate\Support\Facades\Storage::disk($disk)->url($expense->receipt_path)
                   : ($hasBlob ? $expense->receipt_src : null);
  
              $ext = null;
              if ($hasFile) {
                  $ext = strtolower(pathinfo($expense->receipt_path, PATHINFO_EXTENSION));
              } elseif ($hasBlob && preg_match('#^data:([^;]+)#', $src, $m)) {
                  $ext = str_contains($m[1], 'pdf') ? 'pdf' : 'img';
              }
            @endphp
  
            @if($src)
              <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">
                  Receipt
                </h3>
  
                @if($ext === 'pdf')
                  <div class="border rounded overflow-hidden bg-white dark:bg-gray-900">
                    <object data="{{ $src }}"
                            type="application/pdf"
                            class="w-full h-64 sm:h-80">
                      <p class="p-4 text-gray-600 dark:text-gray-400">
                        Cannot preview PDF.
                        <a href="{{ $src }}"
                           class="link"
                           @if($hasFile) target="_blank" @endif>
                          Download
                        </a>
                      </p>
                    </object>
                  </div>
                @else
                  <div class="border rounded bg-white dark:bg-gray-900 p-2 flex justify-center">
                    <img src="{{ $src }}"
                         alt="Receipt for {{ $expense->title }}"
                         class="max-h-80 object-contain" />
                  </div>
                @endif
  
                @if($hasFile)
                  <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <a href="{{ $src }}" target="_blank" class="link">
                      Open original
                    </a>
                  </p>
                @endif
              </div>
            @endif
  
            {{-- Actions --}}
            <div class="flex flex-wrap justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
              <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline btn-sm">
                Edit
              </a>
              <a href="{{ route('expenses.index') }}" class="btn btn-outline btn-sm">
                Back
              </a>
            </div>
  
          </div>
        </div>
  
      </div>
    </main>
  </x-app-layout>
  