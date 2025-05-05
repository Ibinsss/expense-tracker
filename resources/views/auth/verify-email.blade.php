@extends('expenses.layout') {{-- or your app’s base layout --}}
@section('content')
  <div class="max-w-md mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Verify Your Email Address</h1>

    @if (session('message'))
      <div class="mb-4 text-green-600">
        {{ session('message') }}
      </div>
    @endif

    <p>
      Before proceeding, please check your email for a verification link.
      If you did not receive the email,
    </p>

    <form method="POST" action="{{ route('verification.send') }}" class="mt-4">
      @csrf
      <button type="submit"
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
        Click here to request another
      </button>
    </form>
  </div>
@endsection
