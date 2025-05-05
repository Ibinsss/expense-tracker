@extends('expenses.layout')

@section('content')
    <h2 class="text-xl font-bold mb-4">{{ $expense->title }}</h2>

    <p><strong>Amount:</strong> RM {{ number_format($expense->amount, 2) }}</p>
    <p><strong>Date:</strong> {{ $expense->date }}</p>
    <p><strong>Category:</strong> {{ $expense->category }}</p>
    <p><strong>Notes:</strong> {{ $expense->notes }}</p>

    @if($expense->receipt_data)
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-2">Receipt</h3>

            @php
                // build a data: URI from the raw blob
                $mime = $expense->receipt_mime;
                $src  = 'data:' . $mime . ';base64,' . base64_encode($expense->receipt_data);
            @endphp

            @if($mime === 'application/pdf')
                <div class="border rounded overflow-hidden bg-white">
                    <object
                        data="{{ $src }}"
                        type="application/pdf"
                        class="w-full h-64"
                    >
                        <p class="p-4">
                            Your browser doesn’t support inline PDFs.
                            <a href="{{ $src }}" class="text-indigo-600 underline">
                                Download the PDF
                            </a>
                        </p>
                    </object>
                </div>

            @elseif(str_starts_with($mime, 'image/'))
                <div class="border rounded bg-white p-4">
                    <img
                        src="{{ $src }}"
                        alt="Receipt for {{ $expense->title }}"
                        class="w-full max-h-96 object-contain"
                    />
                </div>

            @else
                <p>
                    <a href="{{ $src }}" class="text-indigo-600 underline">
                        Download receipt file
                    </a>
                </p>
            @endif

            <p class="mt-2 text-sm">
                <a href="{{ $src }}" target="_blank" class="text-blue-600 underline">
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
