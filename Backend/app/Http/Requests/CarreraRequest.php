<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarreraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // On PUT/PATCH we need to ignore the current record's own nombre
        // so it can be "updated" to the same value without triggering unique violation.
        $carreraId = $this->route('carrera');
        if ($carreraId instanceof \App\Models\Carrera) {
            $carreraId = $carreraId->idCarrera;
        }

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\-\.\,\(\)\'\"“”‘’]+$/',
                Rule::unique('carreras', 'nombre')->ignore($carreraId, 'idCarrera'),
            ],
            'descripcion' => [
                'nullable', 
                'string', 
                'max:255',
                'regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s\-\.\,\(\)\'\"“”‘’\?\!\¿\¡\:\;]+$/',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors (in Spanish).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la carrera es obligatorio.',
            'nombre.string'   => 'El nombre de la carrera debe ser una cadena de texto.',
            'nombre.max'      => 'El nombre de la carrera no puede superar los 255 caracteres.',
            'nombre.unique'   => 'Ya existe una carrera con ese nombre.',
            'nombre.regex'    => 'El nombre no debe contener caracteres especiales como $, %, @, etc.',
            'descripcion.string' => 'La descripción debe ser una cadena de texto.',
            'descripcion.max'    => 'La descripción no puede superar los 255 caracteres.',
            'descripcion.regex'  => 'La descripción no debe contener caracteres especiales como $, %, @, etc.',
        ];
    }
}
