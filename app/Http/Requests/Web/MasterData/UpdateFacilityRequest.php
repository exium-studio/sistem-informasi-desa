<?php

namespace App\Http\Requests\Web\MasterData;

use App\Http\Resources\Templates\Response\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateFacilityRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['sometimes', 'array'],
            'documents' => ['nullable', 'array', 'max:3'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'delete_document_ids' => ['nullable', 'array'],
            'delete_document_ids.*' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Nama fasilitas harus berupa teks.',
            'name.max' => 'Nama fasilitas maksimal 255 karakter.',
            'location.array' => 'Lokasi harus berupa array.',

            'documents.array' => 'Dokumen harus berupa array.',
            'documents.max' => 'Maksimal 3 dokumen yang dapat diunggah.',
            'documents.*.file' => 'Setiap dokumen harus berupa file.',
            'documents.*.mimes' => 'Dokumen hanya boleh berupa file PDF.',
            'documents.*.max' => 'Ukuran maksimal setiap dokumen adalah 10MB.',

            'delete_document_ids.array' => 'Format dokumen yang dihapus harus berupa array.',
            'delete_document_ids.*.integer' => 'ID dokumen yang dihapus harus berupa angka.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $messages = implode(' ', $validator->errors()->all());
        $response = new WithoutDataResource(
            Response::HTTP_BAD_REQUEST,
            'FAILED_VALIDATION',
            'Update Fasilitas Gagal',
            $messages
        );

        throw new HttpResponseException(response()->json($response, Response::HTTP_BAD_REQUEST));
    }
}
