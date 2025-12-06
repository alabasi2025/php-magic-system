<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganizationUnitController extends Controller
{
    public function index()
    {
        return view('organization.units.index');
    }

    public function create()
    {
        return view('organization.units.create');
    }

    public function store(Request $request)
    {
        // TODO: Implement store logic
        return redirect()->route('units.index')->with('success', 'تم إضافة الوحدة بنجاح');
    }

    public function show($id)
    {
        return view('organization.units.show');
    }

    public function edit($id)
    {
        return view('organization.units.edit');
    }

    public function update(Request $request, $id)
    {
        // TODO: Implement update logic
        return redirect()->route('units.index')->with('success', 'تم تحديث الوحدة بنجاح');
    }

    public function destroy($id)
    {
        // TODO: Implement destroy logic
        return redirect()->route('units.index')->with('success', 'تم حذف الوحدة بنجاح');
    }
}
