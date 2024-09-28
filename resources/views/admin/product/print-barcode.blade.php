@extends('layouts.admin')

@section('content')
<style>
    @media print {
        /* Hide everything */
        body * {
            visibility: hidden;
        }

        /* Show only the barcode and its container */
        .barcode-print,
        .barcode-print * {
            visibility: visible;
        }

        /* Position the barcode at the top-left of the page */
        .barcode-print {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Remove buttons, header, and footer elements */
        .btn, a.btn, h1, .header, .footer {
            display: none !important;
        }

        /* Hide the default header and footer that the browser adds */
        @page {
            margin: 0;
        }
    }
</style>

<div class="container">
    <div class="card mt-4 barcode-print">
        <div class="card-body text-center">
            <img src="{{ asset('storage/'.$barcodePath) }}" alt="Barcode for {{ $product->name }}">
            <br>
            <strong>Barcode:</strong> {{ $product->barcode }}
        </div>
    </div>

    <button class="btn btn-info" onclick="window.print()"> طباعة الباركود </button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary"> العودة لصفحة المنتجات </a>
</div>
@endsection
