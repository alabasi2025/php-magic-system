<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return view('purchases.orders.index');
    }

    public function create()
    {
        return view('purchases.orders.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('purchases.orders.index');
    }

    public function show($id)
    {
        return view('purchases.orders.show', compact('id'));
    }

    public function edit($id)
    {
        return view('purchases.orders.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('purchases.orders.index');
    }

    public function destroy($id)
    {
        return redirect()->route('purchases.orders.index');
    }

    public function approve($id)
    {
        return redirect()->route('purchases.orders.show', $id);
    }

    public function cancel($id)
    {
        return redirect()->route('purchases.orders.show', $id);
    }

    public function addItem(Request $request, $id)
    {
        return response()->json(['success' => true]);
    }

    public function updateItem(Request $request, $orderId, $itemId)
    {
        return response()->json(['success' => true]);
    }

    public function deleteItem($orderId, $itemId)
    {
        return response()->json(['success' => true]);
    }

    public function getItems($id)
    {
        return response()->json([]);
    }
}
