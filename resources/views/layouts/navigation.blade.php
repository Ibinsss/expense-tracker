<nav x-data="{ open: false }" class="relative z-50">
    {{-- Top bar --}}
    <div class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="flex-shrink-0">
          <x-application-logo class="h-8 w-auto fill-current text-gray-800 dark:text-gray-200"/>
        </a>
  
        {{-- Hamburger button --}}
        <button @click="open = true"
                class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </div>
  
    {{-- Overlay + Drawer --}}
    <div x-show="open"
         x-transition.opacity
         class="fixed inset-0 bg-black bg-opacity-50"
         @click="open = false">
    </div>
  
    <aside x-show="open"
           x-transition:enter="transform transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transform transition ease-in duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-lg overflow-y-auto">
      <div class="h-16 flex items-center px-4 border-b border-gray-200 dark:border-gray-700">
        {{-- Close button --}}
        <button @click="open = false"
                class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        <span class="ml-2 font-semibold text-lg text-gray-800 dark:text-gray-100">Menu</span>
      </div>
  
      <div class="p-4 space-y-2">
        <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
          {{ __('Dashboard') }}
        </x-responsive-nav-link>
  
        <x-responsive-nav-link :href="route('expenses.index')" :active="request()->routeIs('expenses.*')">
          {{ __('Expenses') }}
        </x-responsive-nav-link>
  
        <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>
  
        <x-responsive-nav-link :href="route('profile.edit')">
          {{ __('Profile') }}
        </x-responsive-nav-link>
  
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <x-responsive-nav-link :href="route('logout')"
                                 onclick="event.preventDefault(); this.closest('form').submit();">
            {{ __('Log Out') }}
          </x-responsive-nav-link>
        </form>
      </div>
    </aside>
  </nav>
  