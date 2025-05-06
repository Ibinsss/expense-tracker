<x-app-layout>
    <x-slot name="header">
      <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight leading-tight">
        {{ __('Profile Settings') }}
      </h2>
    </x-slot>
  
    <main class="py-8">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
  
        <div class="card max-w-lg mx-auto">
          <div class="card-body space-y-6">
  
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')  
  
              {{-- Name --}}
              <div>
                <label for="name" class="block text-sm font-medium mb-1">
                  {{ __('Name') }}
                </label>
                <input id="name" name="name" type="text"
                       value="{{ old('name', auth()->user()->name) }}"
                       class="block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100
                              border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                              focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('name')
                  <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
              </div>
  
              {{-- Email --}}
              <div>
                <label for="email" class="block text-sm font-medium mb-1">
                  {{ __('Email') }}
                </label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', auth()->user()->email) }}"
                       class="block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100
                              border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                              focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('email')
                  <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
              </div>
  
              {{-- Password (optional) --}}
              <div>
                <label for="password" class="block text-sm font-medium mb-1">
                  {{ __('New Password') }}
                </label>
                <input id="password" name="password" type="password"
                       placeholder="{{ __('Leave blank to keep current') }}"
                       class="block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100
                              border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                              focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('password')
                  <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
              </div>
  
              {{-- Display Currency --}}
              <div>
                <label for="currency" class="block text-sm font-medium mb-1">
                  {{ __('Display Currency') }}
                </label>
                <select name="currency" id="currency"
                        class="block w-full bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100
                               border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-indigo-500">
                  @php
                    $currencies = [
                      'MYR' => 'Malaysian Ringgit (RM)',
                      'USD' => 'US Dollar ($)',
                      'EUR' => 'Euro (€)',
                      'GBP' => 'British Pound (£)',
                      'JPY' => 'Japanese Yen (¥)',
                    ];
                    $current = old('currency', auth()->user()->currency);
                  @endphp
  
                  @foreach($currencies as $code => $label)
                    <option value="{{ $code }}"
                            {{ $current === $code ? 'selected' : '' }}>
                      {{ $label }}
                    </option>
                  @endforeach
                </select>
                @error('currency')
                  <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
              </div>
  
              {{-- Save Button --}}
              <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="btn btn-primary btn-lg">
                  {{ __('Save Changes') }}
                </button>
              </div>
            </form>
  
          </div>
        </div>
  
      </div>
    </main>
  </x-app-layout>
  