<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return view('purchases.suppliers.index');
    }

    public function create()
    {
        return view('purchases.suppliers.create');
    }

    public function store(Request $request)
    {
        // Store logic
        return redirect()->route('purchases.suppliers.index');
    }

    public function show($id)
    {
        return view('purchases.suppliers.show', compact('id'));
    }

    public function edit($id)
    {
        return view('purchases.suppliers.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update logic
        return redirect()->route('purchases.suppliers.index');
    }

    public function destroy($id)
    {
        // Delete logic
        return redirect()->route('purchases.suppliers.index');
    }

    public function transactions($id)
    {
        return view('purchases.suppliers.transactions', compact('id'));
    }

    public function search(Request $request)
    {
        return response()->json([]);
    }

    public function getBalance($id)
    {
        return response()->json(['balance' => 0]);
    }
}
