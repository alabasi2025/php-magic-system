<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseReceiptController extends Controller
{
    public function index()
    {
        return view('purchases.receipts.index');
    }

    public function create()
    {
        return view('purchases.receipts.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('purchases.receipts.index');
    }

    public function show($id)
    {
        return view('purchases.receipts.show', compact('id'));
    }

    public function edit($id)
    {
        return view('purchases.receipts.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('purchases.receipts.index');
    }

    public function destroy($id)
    {
        return redirect()->route('purchases.receipts.index');
    }

    public function approve($id)
    {
        return redirect()->route('purchases.receipts.show', $id);
    }

    public function reject($id)
    {
        return redirect()->route('purchases.receipts.show', $id);
    }

    public function addItem(Request $request, $id)
    {
        return response()->json(['success' => true]);
    }

    public function deleteItem($receiptId, $itemId)
    {
        return response()->json(['success' => true]);
    }
}
