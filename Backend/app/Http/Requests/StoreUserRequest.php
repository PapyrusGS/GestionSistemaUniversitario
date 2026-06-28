<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
        return [
            'idRol' => ['required', 'integer', 'exists:roles,idRol'],
            'nombre1' => ['required', 'string', 'max:255'],
            'nombre2' => ['nullable', 'string', 'max:255'],
            'apellido1' => ['required', 'string', 'max:255'],
            'apellido2' => ['nullable', 'string', 'max:255'],
            'ci' => ['required', 'string', 'max:255', 'unique:usuarios,ci'],
            'correo' => ['required', 'string', 'email', 'max:255', 'unique:usuarios,correo'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
            'idCarrera' => [
                \Illuminate\Validation\Rule::requiredIf(function () {
                    $rol = \App\Models\Rol::find($this->idRol);
                    return $rol && $rol->nombre === 'Estudiante';
                }),
                'nullable',
                'integer',
                'exists:carreras,idCarrera',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'idRol.required' => 'El rol es obligatorio.',
            'idRol.integer' => 'El rol debe ser un número entero.',
            'idRol.exists' => 'El rol seleccionado no existe.',
            'nombre1.required' => 'El primer nombre es obligatorio.',
            'nombre1.string' => 'El primer nombre debe ser una cadena de texto.',
            'nombre1.max' => 'El primer nombre no puede superar los 255 caracteres.',
            'nombre2.string' => 'El segundo nombre debe ser una cadena de texto.',
            'nombre2.max' => 'El segundo nombre no puede superar los 255 caracteres.',
            'apellido1.required' => 'El primer apellido es obligatorio.',
            'apellido1.string' => 'El primer apellido debe ser una cadena de texto.',
            'apellido1.max' => 'El primer apellido no puede superar los 255 caracteres.',
            'apellido2.string' => 'El segundo apellido debe ser una cadena de texto.',
            'apellido2.max' => 'El segundo apellido no puede superar los 255 caracteres.',
            'ci.required' => 'La cédula de identidad (CI) es obligatoria.',
            'ci.string' => 'La cédula de identidad debe ser una cadena de texto.',
            'ci.max' => 'La cédula de identidad no puede superar los 255 caracteres.',
            'ci.unique' => 'Esta cédula de identidad ya está registrada.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.string' => 'El correo electrónico debe ser una cadena de texto.',
            'correo.email' => 'El correo electrónico debe tener un formato válido.',
            'correo.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'idCarrera.required' => 'La carrera es obligatoria para estudiantes.',
            'idCarrera.integer' => 'La carrera debe ser un número entero.',
            'idCarrera.exists' => 'La carrera seleccionada no existe.',
        ];
    }
}
