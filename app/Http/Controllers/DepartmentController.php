<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('organization.departments.index');
    }

    public function create()
    {
        return view('organization.departments.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('departments.index')->with('success', 'تم إضافة القسم بنجاح');
    }

    public function show($id)
    {
        return view('organization.departments.show');
    }

    public function edit($id)
    {
        return view('organization.departments.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('departments.index')->with('success', 'تم تحديث القسم بنجاح');
    }

    public function destroy($id)
    {
        return redirect()->route('departments.index')->with('success', 'تم حذف القسم بنجاح');
    }
}
