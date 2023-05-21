<?php

namespace App\Models;

use App\Filters\InvoiceFilter;
use App\Http\Resources\v1\InvoiceResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Invoice extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'user_id',
        'type',
        'paid',
        'value',
        'payment_date',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function filter(Request $request){
        $queryFilter = (new InvoiceFilter)->filter($request);

        if (empty($queryFilter)) {
          return InvoiceResource::collection(Invoice::with('user')->get());
        }

        $data = Invoice::with('user');

        if (!empty($queryFilter['whereIn'])) {
          foreach ($queryFilter['whereIn'] as $value) {
            $data->whereIn($value[0], $value[1]);
          }
        }

        $resource = $data->where($queryFilter['where'])->get();

        return InvoiceResource::collection($resource);
  }

}
