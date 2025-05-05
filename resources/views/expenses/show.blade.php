@extends('expenses.layout')

@section('content')
    <h2 class="text-xl font-bold mb-4">{{ $expense->title }}</h2>
    <p><strong>Amount:</strong> RM {{ $expense->amount }}</p>
    <p><strong>Date:</strong> {{ $expense->date }}</p>
    <p><strong>Category:</strong> {{ $expense->category }}</p>
    <p><strong>Notes:</strong> {{ $expense->notes }}</p>

    @if($expense->receipt_path)
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-2">Receipt</h3>

            @php
                $ext = strtolower(pathinfo($expense->receipt_path, PATHINFO_EXTENSION));
                $url = asset('storage/'.$expense->receipt_path);
            @endphp

            @if($ext === 'pdf')
                <div class="border rounded overflow-hidden bg-white">
                    <object 
                    data="{{ $url }}" 
                    type="application/pdf" 
                    class="w-full h-64">  {{-- h-64 = 16rem = 256px; use h-72 or h-80 for 288px/320px --}}
                    <p class="p-4">
                      Your browser doesn’t support embedded PDFs.
                      <a href="{{ $url }}" class="text-indigo-600 underline">Download the PDF</a>
                    </p>
                  </object>
                </div>
            @else
                <img 
                    src="{{ $url }}" 
                    alt="Receipt" 
                    class="border rounded max-h-96 w-full object-contain bg-white" />
            @endif

            <p class="mt-2 text-sm">
                <a href="{{ $url }}" target="_blank" class="text-blue-600 underline">
                    Open original
                </a>
            </p>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline">Edit</a>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline">Back</a>
    </div>
@endsection
