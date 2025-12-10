<!DOCTYPE html>
<html>
<head>
    <title>إنشاء فاتورة تلقائياً</title>
</head>
<body>
    <h1>جاري إنشاء الفاتورة...</h1>
    <div id="result"></div>

    <script>
        // استخدام fetch API لإرسال البيانات مباشرة
        fetch('/purchases/invoices', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                invoice_type_id: 1, // الدهمية
                supplier_id: 1,
                warehouse_id: 1,
                invoice_date: '2025-12-10',
                due_date: '2026-01-09',
                payment_method: 'cash',
                status: 'draft',
                payment_status: 'unpaid',
                notes: 'فاتورة اختبار تلقائية',
                items: [
                    {
                        item_id: 1,
                        quantity: 10,
                        unit_price: 100,
                        discount: 0
                    }
                ],
                subtotal: 1000,
                tax_amount: 0,
                discount_amount: 0,
                total_amount: 1000
            })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        })
        .catch(error => {
            document.getElementById('result').innerHTML = '<p style="color: red;">خطأ: ' + error.message + '</p>';
        });
    </script>
</body>
</html>
