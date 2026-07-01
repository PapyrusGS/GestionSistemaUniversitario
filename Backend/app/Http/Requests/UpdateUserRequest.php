<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('estado')) {
            $this->merge([
                'estado' => filter_var($this->input('estado'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $idUsuario = $this->route('idUsuario');

        return [
            'nombre1' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/'],
            'nombre2' => ['nullable', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/'],
            'apellido1' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/'],
            'apellido2' => ['nullable', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/'],
            'ci' => ['required', 'string', 'max:255', Rule::unique('usuarios', 'ci')->ignore($idUsuario, 'idUsuario')],
            'correo' => ['required', 'string', 'email', 'max:255', Rule::unique('usuarios', 'correo')->ignore($idUsuario, 'idUsuario')],
            'telefono' => ['nullable', 'string', 'regex:/^[67]\d{7}$/'],
            'password' => [
                'nullable',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
            'estado' => ['required', 'boolean'],
            'idCarrera' => [
                'nullable',
                'integer',
                'exists:carreras,idCarrera',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre1.required' => 'El primer nombre es obligatorio.',
            'nombre1.string' => 'El primer nombre debe ser una cadena de texto.',
            'nombre1.min' => 'El primer nombre debe tener al menos 2 caracteres.',
            'nombre1.max' => 'El primer nombre no puede superar los 255 caracteres.',
            'nombre1.regex' => 'El primer nombre solo puede contener letras y espacios.',
            'nombre2.string' => 'El segundo nombre debe ser una cadena de texto.',
            'nombre2.min' => 'El segundo nombre debe tener al menos 2 caracteres.',
            'nombre2.max' => 'El segundo nombre no puede superar los 255 caracteres.',
            'nombre2.regex' => 'El segundo nombre solo puede contener letras y espacios.',
            'apellido1.required' => 'El primer apellido es obligatorio.',
            'apellido1.string' => 'El primer apellido debe ser una cadena de texto.',
            'apellido1.min' => 'El primer apellido debe tener al menos 2 caracteres.',
            'apellido1.max' => 'El primer apellido no puede superar los 255 caracteres.',
            'apellido1.regex' => 'El primer apellido solo puede contener letras y espacios.',
            'apellido2.string' => 'El segundo apellido debe ser una cadena de texto.',
            'apellido2.min' => 'El segundo apellido debe tener al menos 2 caracteres.',
            'apellido2.max' => 'El segundo apellido no puede superar los 255 caracteres.',
            'apellido2.regex' => 'El segundo apellido solo puede contener letras y espacios.',
            'ci.required' => 'La cédula de identidad (CI) es obligatoria.',
            'ci.string' => 'La cédula de identidad debe ser una cadena de texto.',
            'ci.max' => 'La cédula de identidad no puede superar los 255 caracteres.',
            'ci.unique' => 'Esta cédula de identidad ya está registrada.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.string' => 'El correo electrónico debe ser una cadena de texto.',
            'correo.email' => 'El correo electrónico debe tener un formato válido.',
            'correo.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'telefono.regex' => 'El teléfono debe empezar con 6 o 7 y tener exactamente 8 dígitos.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.letters' => 'La contraseña debe contener al menos una letra.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser activo o inactivo.',
            'idCarrera.integer' => 'La carrera debe ser un número entero.',
            'idCarrera.exists' => 'La carrera seleccionada no existe.',
        ];
    }
}
