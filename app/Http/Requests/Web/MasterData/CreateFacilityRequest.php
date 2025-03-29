<?php

namespace App\Http\Requests\Web\MasterData;

use App\Http\Resources\Templates\Response\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CreateFacilityRequest extends FormRequest
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
            'documents' => ['nullable', 'array', 'max:3'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'array']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama fasilitas wajib diisi.',
            'location.required' => 'Lokasi fasilitas wajib diisi.',
            'documents.max' => 'Maksimal unggah 3 file.',
            'documents.*.file' => 'Setiap dokumen harus berupa file.',
            'documents.*.mimes' => 'Dokumen hanya boleh berupa PDF.',
            'documents.*.max' => 'Ukuran dokumen maksimal 10MB.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $messages = implode(' ', $validator->errors()->all());
        $response = new WithoutDataResource(
            Response::HTTP_BAD_REQUEST,
            'FAILED_VALIDATION',
            'Create Fasilitas Gagal',
            $messages
        );

        throw new HttpResponseException(response()->json($response, Response::HTTP_BAD_REQUEST));
    }
}
