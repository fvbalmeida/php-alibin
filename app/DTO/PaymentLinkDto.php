<?php

namespace App\DTO;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PaymentLinkDto
{
    public string $slug;
    public ?string $dt_validade;
    public ?int $nu_max_pagamentos;
    public bool $tp_quantidade;
    public string $ds_softdescriptor;
    public bool $tp_boleto;
    public string $tp_pagamento_boleto;
    public int $nu_max_parcelas_boleto;
    public ?int $dia_cobranca_boleto;
    public int $nu_baixa_automatica_boleto;
    public int $nu_boleto_dias_vencimento;
    public bool $tp_credito;
    public string $tp_pagamento_credito;
    public int $nu_max_parcelas_credito;
    public ?int $dia_cobranca_credito;
    public float $vl_total;
    public bool $tp_mostrar_itens_checkout;
    public array $itens;
    public string $url_retorno;

    public function __construct(array $data)
    {
        $this->validate($data);

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    private function validate(array $data)
    {
        $validator = Validator::make($data, [
            'slug' => 'required|string',
            'dt_validade' => 'nullable|date',
            'nu_max_pagamentos' => 'nullable|integer',
            'tp_quantidade' => 'required|boolean',
            'ds_softdescriptor' => 'required|string',
            'tp_boleto' => 'required|boolean',
            'tp_pagamento_boleto' => 'required|string',
            'nu_max_parcelas_boleto' => 'required|integer',
            'dia_cobranca_boleto' => 'nullable|integer',
            'nu_baixa_automatica_boleto' => 'required|integer',
            'nu_boleto_dias_vencimento' => 'required|integer',
            'tp_credito' => 'required|boolean',
            'tp_pagamento_credito' => 'required|string',
            'nu_max_parcelas_credito' => 'required|integer',
            'dia_cobranca_credito' => 'nullable|integer',
            'vl_total' => 'required|numeric',
            'tp_mostrar_itens_checkout' => 'required|boolean',
            'itens' => 'required|array',
            'itens.*.nm_item' => 'required|string',
            'itens.*.ds_item' => 'required|string',
            'itens.*.qtd_item' => 'required|integer',
            'itens.*.vl_item' => 'required|numeric',
            'url_retorno' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator, $this->getValidationError($validator));
        }
    }

    private function getValidationError($validator)
    {
        $errors = [];

        foreach ($validator->errors()->messages() as $field => $messages) {
            $errors[$field] = $messages[0];
        }

        return $errors;
    }

}

