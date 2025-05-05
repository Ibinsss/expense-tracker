@extends('expenses.layout')

@section('content')
    <h2 class="text-xl font-bold mb-4">{{ $expense->title }}</h2>

    <p><strong>Amount:</strong> RM {{ number_format($expense->amount, 2) }}</p>
    <p><strong>Date:</strong> {{ $expense->date }}</p>
    <p><strong>Category:</strong> {{ $expense->category }}</p>
    <p><strong>Notes:</strong> {{ $expense->notes }}</p>

    @if($expense->receipt_path)
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-2">Receipt</h3>

            @php
                $url = asset('storage/'.$expense->receipt_path);
                $ext = strtolower(pathinfo($expense->receipt_path, PATHINFO_EXTENSION));
            @endphp

            @if($ext === 'pdf')
                <div class="border rounded overflow-hidden bg-white">
                    <object
                        data="{{ $url }}"
                        type="application/pdf"
                        class="w-full h-64"  {{-- h-64 = 16rem; adjust as needed --}}
                    >
                        <p class="p-4">
                            Your browser doesn’t support embedded PDFs.
                            <a href="{{ $url }}" class="text-indigo-600 underline">
                                Download the PDF
                            </a>
                        </p>
                    </object>
                </div>
            @else
                <div class="border rounded bg-white p-4">
                    <img
                        src="{{ $url }}"
                        alt="Receipt for {{ $expense->title }}"
                        class="w-full max-h-96 object-contain"
                    />
                </div>
            @endif

            <p class="mt-2 text-sm">
                <a href="{{ $url }}" target="_blank" class="text-blue-600 underline">
                    Open original
                </a>
            </p>
        </div>
    @endif

    <div class="mt-6 flex space-x-4">
        <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-outline">
            Edit
        </a>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline">
            Back
        </a>
    </div>
@endsection
