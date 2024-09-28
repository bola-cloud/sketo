<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            direction: rtl;
            color: #333;
            display: flex;
            height: 50vh;
            background-color: #f8f9fa;
        }

        .container {
            width: 80mm;
            box-sizing: border-box;
        }

        .receipt {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #000; /* Changed to black */
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000; /* Changed to black */
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #555;
        }

        .header .date {
            margin-top: 10px;
            font-size: 12px;
        }

        .header .invoice-code {
            margin-top: 3px;
            font-size: 10px;
            color: #777;
        }

        .details {
            margin-bottom: 15px;
        }

        .details p {
            margin: 5px 0;
            font-size: 10px;
        }

        .details .info-label {
            font-weight: bold;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table th, .table td {
            border: 1px solid #000; /* Changed to black */
            text-align: center;
        }

        .table th {
            background-color: #f8f8f8;
            font-weight: bold;
        }

        .totals {
            margin-top: 10px;
        }

        .totals th, .totals td {
            text-align: right;
            font-size: 12px;
        }

        .totals th, .totals td {
            border-top: 1px solid #000; /* Changed to black */
        }

        .footer {
            text-align: center;
            border-top: 1px solid #000; /* Changed to black */
            padding-top: 5px;
            font-size: 12px;
        }

        .footer p {
            margin: 0;
            color: #777;
        }

        .no-print {
            margin-top: 20px;
            text-align: center;
        }

        .btn {
            padding: 10px 20px;
            font-size: 14px;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 5px;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-primary:hover, .btn-secondary:hover {
            opacity: 0.9;
        }

        img {
            height: 80px !important;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="invoice-POS" class="receipt">
            <div id="top" class="header">
                <h1>Sketo</h1>
                <h1>فاتورة</h1>
                <div class="date">التاريخ: {{$invoice->created_at}}</div>
                <div class="invoice-code">INV-{{$invoice->invoice_code}}</div>
            </div>

            <div id="mid" class="details">
                <p><span class="info-label">معلومات العميل:</span></p>
                <p>
                    <span class="info-label">اسم العميل:</span> {{$invoice->buyer_name}}<br>
                    <span class="info-label">رقم العميل:</span> {{$invoice->buyer_phone}}<br>
                    <span class="info-label">تاريخ الشراء:</span> {{$invoice->created_at}}<br>
                </p>
            </div>

            <div id="bot">
                <table class="table">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>الكمية</th>
                            <th>الإجمالي الفرعي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->sales as $sale)
                        <tr class="service">
                            <td>{{ $sale->product->name }}</td>
                            <td>{{ $sale->quantity }}</td>
                            <td>{{ $sale->product->selling_price * $sale->quantity }} ج.م</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <table class="totals">
                    <tr>
                        <td></td>
                        <th>الإجمالي الفرعي:</th>
                        <td><strong>{{ $invoice->subtotal }} ج.م</strong></td>
                    </tr>
                      <tr>
                        <td></td>
                        <th>الخصم:</th>
                        <td><strong>{{ $invoice->discount }} ج.م</strong></td>
                    </tr>  
                    <tr>
                        <td></td>
                        <th>الإجمالي:</th>
                        <td><strong>{{ $invoice->total_amount }} ج.م</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <th>المبلغ المدفوع:</th>
                        <td><strong>{{ $invoice->paid_amount }} ج.م</strong></td>
                    </tr>
                    <tr>
                        <td></td>
                        <th>المتبقي:</th>
                        <td><strong>{{ $invoice->change }} ج.م</strong></td>
                    </tr>
                </table>
            </div>

            <div class="footer">
                <p>شكراً لتسوقكم معنا!</p>
            </div>
        </div>

        <div class="row no-print text-center">
            <button onclick="window.print()" class="btn btn-primary">طباعة الفاتورة</button>
            <a href="{{ route('cashier.viewCart') }}" class="btn btn-secondary">عودة إلى الكاشير</a>
        </div>
    </div>
</body>
</html>
