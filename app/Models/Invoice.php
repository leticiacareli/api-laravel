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
      /**
       * A função cria uma instância da classe 'InvoiceFilter' e chama seu método 'filter' passando um objeto '$request'. O resultado desse filtro é armazenado na variável '$queryFilter'.
       * Em seguida, é verificado se '$queryFilter' está vazio. Se estiver vazio, significa que nenhum filtro válido foi aplicado, então a função retorna uma coleção de recursos 'InvoiceResource' contendo todas as intâncias de 'Invoice' com a relação 'user' pré-carregada.
       */
      $queryFilter = (new InvoiceFilter)->filter($request);
      if (empty($queryFilter)) {
        return InvoiceResource::collection(Invoice::with('user')->get());
      }

      /**
       * A variável '$data' é criada para armazenar a consulta do model 'Invoice' com a relação 'user' pré-carregada.
       * Em seguida, é verificado se '$queryFilter['whereIn']' não está vazio. Se não estiver vazio, significa que foram aplicados filtros 'whereIn'. o código itera sobre cada entrada em '$queryFilter['whereIn']' e aplica a cláusula '$data' usando os valores fornecidos.  
       */
      $data = Invoice::with('user');
      if (!empty($queryFilter['whereIn'])) {
        foreach ($queryFilter['whereIn'] as $value) {
          $data->whereIn($value[0], $value[1]);
        }
      }

      /**
       * A consulta continua sendo contruída. A cláusula 'where' é aplicada à consulta '$data' usando os valores em '$queryFilter['where']'.
       * Em seguida, a função executa a consulta chamando 'get()' para obter os resultados finais da filtragem.
       * Por fim, os resultados são retornados como uma coleção de recursos 'InvoiceResource' usando 'InvoiceResource::collection($resource)'.
       */
      $resource = $data->where($queryFilter['where'])->get();
      return InvoiceResource::collection($resource);
    }

}
