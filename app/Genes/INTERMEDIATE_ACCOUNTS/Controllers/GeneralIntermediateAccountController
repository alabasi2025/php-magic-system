<?php

namespace App\Genes\INTERMEDIATE_ACCOUNTS\Controllers;

use App\Http\Controllers\Controller;
use App\Genes\INTERMEDIATE_ACCOUNTS\Services\GeneralIntermediateAccountService;
use Illuminate\Http\Request;

class GeneralIntermediateAccountController extends Controller
{
    protected $service;

    public function __construct(GeneralIntermediateAccountService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Implement logic to display a listing of resources
        return response()->json($this->service->getAll());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Implement logic to show the creation form
        return view('intermediate_accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Implement logic to store a new resource
        $data = $request->validate([
            // Validation rules here
        ]);

        $resource = $this->service->create($data);

        return response()->json($resource, 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Implement logic to show the edit form
        $resource = $this->service->find($id);
        return view('intermediate_accounts.edit', compact('resource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Implement logic to update the specified resource
        $data = $request->validate([
            // Validation rules here
        ]);

        $resource = $this->service->update($id, $data);

        return response()->json($resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Implement logic to remove the specified resource
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
