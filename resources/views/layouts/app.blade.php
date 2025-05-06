<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'Expense Tracker') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts & Styles -->
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
  <div class="min-h-screen flex flex-col">

    {{-- NAVIGATION --}}
    <nav class="bg-white dark:bg-gray-800 shadow">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        @include('layouts.navigation')
      </div>
    </nav>

    {{-- OPTIONAL WELCOME BANNER --}}
    <div class="bg-indigo-600 text-white text-center text-sm font-medium">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-2">
        Welcome to your Personal Expense Tracker 💼
      </div>
    </div>

    {{-- PAGE HEADING (if any) --}}
    @isset($header)
      <header class="bg-white dark:bg-gray-800 shadow">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
          {{ $header }}
        </div>
      </header>
    @endisset

    {{-- MAIN CONTENT --}}
    <main class="flex-1">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
      </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white dark:bg-gray-800 text-sm text-gray-500 dark:text-gray-400">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center">
        © {{ date('Y') }} {{ config('app.name', 'Expense Tracker') }} — Irvine Shearer.
      </div>
    </footer>

  </div>
</body>
</html>
