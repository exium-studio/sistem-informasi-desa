<?php

namespace App\Http\Requests\Web\MasterData;

use App\Http\Resources\Templates\Response\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class CreateDocumentTypeRequest extends FormRequest
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
            'label' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:resident,civil'],
            'description' => ['nullable', 'string', 'max:255'],
            'max_upload' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'label.required' => 'Nama jenis dokumen tidak boleh kosong.',
            'label.string' => 'Nama jenis dokumen harus berupa string.',
            'category.required' => 'Kategori jenis dokumen harus diisi.',
            'category.in' => 'Kategori jenis dokumen harus bernilai resident atau civil.',
            'description.string' => 'Deskripsi harus berupa teks.',
            'max_upload.integer' => 'Maksimal upload harus berupa angka byte.',
            'max_upload.min' => 'Maksimal upload minimal 1 byte.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $messages = implode(' ', $validator->errors()->all());
        $response = new WithoutDataResource(
            Response::HTTP_BAD_REQUEST,
            'FAILED_VALIDATION',
            'Create Jenis Dokumen Gagal',
            $messages
        );

        throw new HttpResponseException(response()->json($response, Response::HTTP_BAD_REQUEST));
    }
}
