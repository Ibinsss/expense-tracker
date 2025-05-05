@extends('expenses.layout')

@section('content')
    <h2 class="text-xl font-bold mb-4">Add New Expense</h2>

    <form action="{{ route('expenses.store') }}"
      method="POST"
      enctype="multipart/form-data">
    @csrf

    @include('expenses.form') {{-- your existing fields --}}

    <div class="mb-4">
        <label for="receipt" class="block text-sm font-medium mb-1">Receipt (jpg|png|pdf|doc)</label>
        <input
            type="file"
            name="receipt"
            id="receipt"
            accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
            class="w-full"
        >
        @error('receipt')<p class="text-red-500">{{ $message }}</p>@enderror
    </div>

    <button class="btn btn-primary">Save</button>
</form>

@endsection
