<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        return view('purchases.invoices.index');
    }

    public function create()
    {
        return view('purchases.invoices.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('purchases.invoices.index');
    }

    public function show($id)
    {
        return view('purchases.invoices.show', compact('id'));
    }

    public function edit($id)
    {
        return view('purchases.invoices.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('purchases.invoices.index');
    }

    public function destroy($id)
    {
        return redirect()->route('purchases.invoices.index');
    }

    public function approve($id)
    {
        return redirect()->route('purchases.invoices.show', $id);
    }

    public function recordPayment(Request $request, $id)
    {
        return response()->json(['success' => true]);
    }

    public function addItem(Request $request, $id)
    {
        return response()->json(['success' => true]);
    }

    public function updateItem(Request $request, $invoiceId, $itemId)
    {
        return response()->json(['success' => true]);
    }

    public function deleteItem($invoiceId, $itemId)
    {
        return response()->json(['success' => true]);
    }
}
