<?php

namespace App\Http\Resources\v1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    private array $types = [
        'C' => 'Cartão',
        'B' => 'Boleto',
        'P' => 'Pix',
    ];

    public function toArray(Request $request): array
    {
        $paid = $this->paid;

        return [
            'user' => [
                'userName'  => $this->user->firstName . ' ' . $this->user->lastName,
            ],
            'type'          => $this->types[$this->type],
            'paid'          => $paid ? 'Pago' : 'Pendente',
            'value'         => 'R$ ' . number_format($this->value, 2,',','.'),
            'paymentDate'   => $paid ? Carbon::parse($this->payment_date)->format('d/m/y H:i:s'): null,
            'paymentSince'  => $paid ? Carbon::parse($this->payment_date)->diffForHumans() : null,
        ];
    }
}
