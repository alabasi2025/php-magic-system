<?php
/**
 * Quick Item Add - Temporary Solution
 * This file adds items directly to database
 */

// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        DB::beginTransaction();
        
        $data = [
            'sku' => $_POST['sku'],
            'name' => $_POST['name'],
            'description' => $_POST['description'] ?? null,
            'unit_id' => (int)$_POST['unit_id'],
            'unit_price' => (float)$_POST['unit_price'],
            'min_stock' => (float)$_POST['min_stock'],
            'max_stock' => (float)$_POST['max_stock'],
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        $itemId = DB::table('items')->insertGetId($data);
        
        DB::commit();
        
        $success = "تم إضافة الصنف بنجاح! رقم الصنف: {$itemId}";
        
    } catch (Exception $e) {
        DB::rollBack();
        $error = "خطأ: " . $e->getMessage();
    }
}

// Get units
$units = DB::table('item_units')->get();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة صنف سريعة - حل مؤقت</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
        }
        button:active {
            transform: translateY(0);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #667eea;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚡ إضافة صنف سريعة</h1>
        <p class="subtitle">حل مؤقت - يضيف الصنف مباشرة إلى قاعدة البيانات</p>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success">✅ <?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">❌ <?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>رمز الصنف (SKU) *</label>
                <input type="text" name="sku" required placeholder="DIESEL-001">
            </div>
            
            <div class="form-group">
                <label>اسم الصنف *</label>
                <input type="text" name="name" required placeholder="ديزل">
            </div>
            
            <div class="form-group">
                <label>الوصف</label>
                <textarea name="description" placeholder="وصف الصنف (اختياري)"></textarea>
            </div>
            
            <div class="form-group">
                <label>الوحدة *</label>
                <select name="unit_id" required>
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= $unit->id ?>"><?= $unit->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>سعر الوحدة *</label>
                <input type="number" step="0.01" name="unit_price" required placeholder="5.5">
            </div>
            
            <div class="form-group">
                <label>الحد الأدنى للمخزون *</label>
                <input type="number" step="0.01" name="min_stock" required placeholder="100">
            </div>
            
            <div class="form-group">
                <label>الحد الأقصى للمخزون *</label>
                <input type="number" step="0.01" name="max_stock" required placeholder="10000">
            </div>
            
            <button type="submit">💾 حفظ الصنف</button>
        </form>
        
        <a href="/inventory/items" class="back-link">← العودة لقائمة الأصناف</a>
    </div>
</body>
</html>
