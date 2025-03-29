<?php

namespace App\Http\Requests\Web\MasterData;

use App\Http\Resources\Templates\Response\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateInventoryRequest extends FormRequest
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
            'amount' => ['sometimes', 'integer', 'min:0'],
            'amount_usage' => ['sometimes', 'integer', 'min:0'],
            'documents' => ['nullable', 'array', 'max:3'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'delete_document_ids' => ['nullable', 'array'],
            'delete_document_ids.*' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Nama harus berupa teks.',
            'amount.integer' => 'Jumlah harus berupa angka.',
            'amount.min' => 'Jumlah tidak boleh kurang dari 0.',
            'amount_usage.integer' => 'Jumlah terpakai harus berupa angka.',
            'amount_usage.min' => 'Jumlah terpakai tidak boleh kurang dari 0.',
            'documents.max' => 'Maksimal hanya boleh mengunggah 3 dokumen.',
            'documents.*.mimes' => 'Dokumen hanya boleh berupa file PDF.',
            'documents.*.max' => 'Ukuran maksimal setiap dokumen adalah 10MB.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $messages = implode(' ', $validator->errors()->all());
        $response = new WithoutDataResource(
            Response::HTTP_BAD_REQUEST,
            'FAILED_VALIDATION',
            'Update Inventaris Gagal',
            $messages
        );

        throw new HttpResponseException(response()->json($response, Response::HTTP_BAD_REQUEST));
    }
}
