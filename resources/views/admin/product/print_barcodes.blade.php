<!DOCTYPE html>
<html>
<head>
    <title>Barcode Generation and Printing</title>    
    <style>
        :root {
            --width: 38mm;
            --height: 25mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            background-color: #f0f0f0;
        }
        .barcode_content {
            width: var(--width);
            height: var(--height);
            margin: 5px;
            background-color: white;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        img {
            width: calc(var(--width) - 5mm); /* Ensure the barcode fits within the container */
            height: auto;
        }

        @media print {
            @page {
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box;
                size: var(--width) var(--height);
            }
            body {
                margin: 0;
                padding: 0;
                background-color: white;
            }
            .barcode_content {
                border: none;
                margin: 0;
            }
            p {
    font-size: 8px;
}
        }
        p.code_price {
    font-size: 12px;
}
        
    </style>
</head>
<body>
    <div id="BarCodeArea">
        <!-- Loop through products from the controller -->
        @foreach($products as $product)
            <div class="barcode_content">
                <p >style</p>
                <p class="code_price"> price: {{$product->selling_price}} L.E </p> <!-- Display product name -->
                <img id="barcode-{{ $product->id }}" /> <!-- Barcode image for each product -->
            </div>
        @endforeach
    </div>

    <button onclick="GenerateBarCodes()">Generate Barcodes</button>
    <button onclick="print_barcode('BarCodeArea')">Print</button>

    <script src="{{asset('js/JsBarcode.all.min.js')}}"></script>
    <script>
        function GenerateBarCodes() {
            @foreach($products as $product)
                // Generate barcode for each product dynamically using its barcode data
                JsBarcode("#barcode-{{ $product->id }}", "{{ $product->barcode }}", {
                    format: "CODE39",    // You can change the format as per your requirement
                    width: 1,            // Adjust the line width
                    height: 20,          // Adjust the height of the barcode
                    font: "monospace",
                    displayValue: true,  // Display the barcode value below the bars
                    lineColor: "#000000" // Black barcode lines
                });
            @endforeach
        }

        // Function to print the barcode area
        function print_barcode(BarCodePrintDiv) {
            var body = document.body.innerHTML;
            var data = document.getElementById(BarCodePrintDiv).innerHTML;
            document.body.innerHTML = data;
            window.print();
            document.body.innerHTML = body;
        }
    </script>
</body>
</html>
