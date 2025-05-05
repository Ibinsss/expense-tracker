@extends('expenses.layout')

@section('content')
    <div class="card">
        <h2 class="text-xl font-bold mb-4">Edit Expense</h2>

        <form action="{{ route('expenses.update',$expense) }}"
      method="POST"
      enctype="multipart/form-data">
    @csrf @method('PUT')

    @include('expenses.form')

    {{-- receipt upload: --}}
    <div class="mb-4">
        <label for="receipt" class="block text-sm font-medium mb-1">
            Replace Receipt
        </label>
        <input
            type="file"
            name="receipt"
            id="receipt"
            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
            class="w-full"
        >
        @error('receipt')<p class="text-red-500">{{ $message }}</p>@enderror
    </div>

    @if($expense->receipt_path)
        <div class="mb-4">
            <p class="text-sm text-gray-600">Current receipt:</p>
            <a href="{{ asset('storage/'.$expense->receipt_path) }}"
               target="_blank"
               class="underline text-blue-600">
               Download / View
            </a>
        </div>
    @endif

    <button class="btn btn-primary">Update</button>
</form>

    </div>
@endsection
