<?php

namespace App\Http\Requests\Web\Dashboard;

use App\Http\Resources\Templates\Response\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CreateOfficialContactRequest extends FormRequest
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
            'contact_person' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:whatsapp,telephone,instagram,facebook,x,email,website'],
            'value' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_person.required' => 'Contact person wajib diisi.',
            'contact_person.exists' => 'Contact person tidak ditemukan.',
            'type.required' => 'Tipe kontak wajib diisi.',
            'type.in' => 'Tipe kontak tidak valid.',
            'value.required' => 'Nilai kontak wajib diisi.',
            'value.string' => 'Nilai kontak harus berupa string.',
            'value.max' => 'Nilai kontak maksimal 255 karakter.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $messages = implode(' ', $validator->errors()->all());
        $response = new WithoutDataResource(
            Response::HTTP_BAD_REQUEST,
            'FAILED_VALIDATION',
            'Create Official Contact Gagal',
            $messages
        );

        throw new HttpResponseException(response()->json($response, Response::HTTP_BAD_REQUEST));
    }
}
