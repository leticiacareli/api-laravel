<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\InvoiceResource;
use App\Models\Invoice;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    use HttpResponses;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return (new Invoice())->filter($request);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => 'required',
            'type'          => 'required|max:1',
            'paid'          => 'required|numeric|between:0,1',
            'payment_date'  => 'nullable',
            'value'         => 'required|numeric|between:1, 9999.99',
        ]);

        if($validator->fails()){
            return $this->error('Erro ao cadastrar', 422, $validator->errors());
        }

        $created = Invoice::create($validator->validated());

        if($created){
            return $this->response('Pagamento criado', 200, new InvoiceResource($created->load('user')));
        }
        else{
            return $this->error('Pagamento não foi criado', 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return new InvoiceResource($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => 'required',
            'type'          => 'required|max:1|in:' . implode(',', ['B', 'C', 'P']), 
            'paid'          => 'required|numeric|between:0,1',
            'payment_date'  => 'nullable|date_format:Y-m-d H:i:s',
            'value'         => 'required|numeric',
        ]);

        if($validator->fails()){
            return $this->error('Erro ao cadastrar', 422, $validator->errors());
        }

        $validated = $validator->validated();

        $updated = $invoice->update([
            'user_id'       => $validated['user_id'],
            'type'          => $validated['type'],
            'paid'          => $validated['paid'],
            'value'         => $validated['value'],
            'payment_date'  => $validated['paid'] ? $validated['payment_date'] : null,
        ]);

        if($updated){
            return $this->response('Pagamento atualizado', 200, new InvoiceResource($invoice->load('user')));
        }
        else{
            return $this->error('Pagamento não foi atualizado', 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $deleted = $invoice->delete();

        if($deleted){
            return $this->response('Pagamento deletado', 200, new InvoiceResource($invoice->load('user')));
        }
        else{
            return $this->error('Pagamento não foi deletado', 400);
        }
    }
}
