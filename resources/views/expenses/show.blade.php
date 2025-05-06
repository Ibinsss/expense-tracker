@extends('expenses.layout')

@section('content')
    <h2 class="text-xl font-bold mb-4">{{ $expense->title }}</h2>

    <p><strong>Amount:</strong> RM {{ number_format($expense->amount, 2) }}</p>
    <p><strong>Date:</strong>  {{ $expense->date }}</p>
    <p><strong>Category:</strong> {{ $expense->category }}</p>
    <p><strong>Notes:</strong>  {{ $expense->notes ?: '—' }}</p>

    {{-- ---------------- Receipt ---------------- --}}
    @php
        // 1️⃣ decide which “source” we have
        $hasFile   = filled($expense->receipt_path);
        $hasBlob   = filled($expense->receipt_src);

        $src       = $hasFile
                     ? asset('storage/'.$expense->receipt_path)
                     : ($hasBlob ? $expense->receipt_src : null);

        // 2️⃣ figure out extension / mime
        $ext = null;

        if ($hasFile) {
            $ext = strtolower(pathinfo($expense->receipt_path, PATHINFO_EXTENSION));
        } elseif ($hasBlob && preg_match('#^data:([^;]+)#', $src, $m)) {
            $mime = $m[1];
            $ext  = str_contains($mime,'pdf') ? 'pdf' : 'img';
        }
    @endphp

    @if ($src)
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-2">Receipt</h3>

            {{-- PDF vs image preview --}}
            @if ($ext === 'pdf')
                <div class="border rounded overflow-hidden bg-white">
                    <object data="{{ $src }}"
                            type="application/pdf"
                            class="w-full h-64 sm:h-80">
                        <p class="p-4">
                            Your browser can’t display PDFs here.
                            <a href="{{ $src }}"
                               {{ $hasFile ? 'target=_blank' : '' }}
                               class="text-indigo-600 underline">
                                Download the file
                            </a>
                        </p>
                    </object>
                </div>
            @else
                <div class="border rounded bg-white p-2">
                    <img src="{{ $src }}"
                         alt="Receipt for {{ $expense->title }}"
                         class="w-full max-h-96 object-contain" />
                </div>
            @endif

            {{-- “open original” link – only makes sense when we have a real URL --}}
            @if ($hasFile)
                <p class="mt-2 text-sm">
                    <a href="{{ $src }}" target="_blank"
                       class="text-blue-600 underline">
                       Open original
                    </a>
                </p>
            @endif
        </div>
    @endif
    {{-- ------------------------------------------------ --}}

    <div class="mt-6 flex space-x-4">
        <a href="{{ route('expenses.edit', $expense) }}"
           class="btn btn-outline">Edit</a>
        <a href="{{ route('expenses.index') }}"
           class="btn btn-outline">Back</a>
    </div>
@endsection
