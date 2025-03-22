<?php

namespace App\Http\Requests\Web\Dashboard;

use App\Http\Resources\Templates\Response\WithoutDataResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateAnnouncementRequest extends FormRequest
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
            'documents' => ['nullable', 'array'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['nullable', 'array'],
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'documents.array' => 'Dokumen harus berupa array.',
            'documents.*.file' => 'Setiap dokumen harus berupa file yang valid.',
            'documents.*.mimes' => 'Setiap dokumen harus berupa file PDF.',
            'documents.*.max' => 'Setiap dokumen tidak boleh lebih dari 10 MB.',
            'title.required' => 'Judul tidak boleh kosong.',
            'title.string' => 'Judul harus berupa string.',
            'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'description.required' => 'Deskripsi tidak boleh kosong.',
            'description.string' => 'Deskripsi harus berupa string.',
            'location.array' => 'Lokasi harus berupa array.',
            'published_at.date' => 'Tanggal publikasi harus berupa tanggal.',
            'expires_at.date' => 'Tanggal kadaluarsa harus berupa tanggal.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $messages = implode(' ', $validator->errors()->all());
        $response = new WithoutDataResource(
            Response::HTTP_BAD_REQUEST,
            'FAILED_VALIDATION',
            'Update Pengumuman Gagal',
            $messages
        );

        throw new HttpResponseException(response()->json($response, Response::HTTP_BAD_REQUEST));
    }
}
