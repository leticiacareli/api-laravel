<?php 

namespace App\Filters;

use DeepCopy\Exception\PropertyException;
use Exception;
use Illuminate\Http\Request;

 abstract class Filter{
    protected array $allowedOperatorsFields = [];

    protected array $translateOperatorsFields = [
        'gt'    => '>',
        'gte'   => '>=',
        'lt'    => '<',
        'lte'   => '<=',
        'eq'    => '=',
        'ne'    => '!=',
        'in'    => 'in',
    ];

    public function filter(Request $request){
        //Essas variáveis serão preenchidas com a condição de filtro, dependendo dos valores passados no objeto 'Request'.
        $where = [];
        $whereIn = [];

        /**
         * Essa linha verifica se a propriedade 'allowedOperatorsFields' está vazia. Se estiver vazia, é lançada uma exceção com uma mensagem "Propriedade allowedOperatorsFields está vazio".
         * Isso garante que exista pelo menos um operador de filtro definido para o campo.
         */
        if(empty($this->allowedOperatorsFields)){
            throw new PropertyException('Propriedade allowedOperatorsFields está vazio');
        }

        /**
         * Essa parte inicia um loop 'foreach' que percorre cada campo permitindo ('$param') e seus operadores correspondentes ('$operators') definidos na propriedade 'allowedOperatorsFields'.
         * O código verifica se o campo está presente na consulta do objeto 'Request' ('$request->query($param)'). Se estiver presente, ele itera pelos operadores e seus valores correspondentes.
         * Dentro do loop é verificado se o operador está presente na lista de operadores permitidos para o campo. Se não estiver, uma exceção é lançada com a mensagem "{$param} não possui o operador {$operator}".
         */
        foreach ($this->allowedOperatorsFields as $param => $operators){
            $queryOperator = $request->query($param);
            if($queryOperator){
                foreach ($queryOperator as $operator => $value){
                    if(!in_array($operator, $operators)){
                        throw new Exception("{$param} não possui o operador {$operator}");
                    }

                    /**
                     * Nesta parte, é verificado se o '$value' contém o caractere "[". Se sim, isso siginifica que é um filtro para a cláusula "whereIn" do SQL. O código remove os colchetes e cria
                     * uma entrada na matriz '$whereIn' com o campo, um arrau de valores obtidos dividindo a string pelo caractere "," e o valor original.
                     * Se o valor não contiver "[", ele é considerado um filtro para a cláusula "where" do SQL. Nesse caso, uma entrada é adicionada na matriz '$where' com o campo, o operador traduzido (obtido da propriedade 'translateOperatorsFields') e o valor original.
                     */
                    if (str_contains($value, '[')) {
                        $whereIn[] = [
                          $param,
                          explode(',', str_replace(['[', ']'], ['', ''], $value)),
                          $value
                        ];
                      } 
                      else{
                        $where[] = [
                          $param,
                          $this->translateOperatorsFields[$operator],
                          $value
                        ];
                    }
                }
            }
        }

        /**
         * Após o loop, essa linha verifica se tanto '$where' quanto '$whereIn' estão vazios. Se estiverem vazios, significa que nenhum filtro válido foi encontrado na consulta, então é retornado um array vazio '[]'.
         */
        if(empty($where) && empty($whereIn)){
            return [];
        }

        /**
         * Se existirem filtros válidos, eles são retornados em um array associativo com as chaves 'where' e 'whereIn'.
         */
        return [
            'where'     => $where,
            'whereIn'   => $whereIn
        ];
    }
}