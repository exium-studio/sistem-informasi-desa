<?php

namespace App\Helpers;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use App\Helpers\StorageServerHelper;

class DocumentHelper
{
	public static function uploadDocuments(array $files): array
	{
		$documentIds = [];
		$uploadedFiles = StorageServerHelper::uploadToServer($files);

		if (is_array($uploadedFiles) && count($uploadedFiles) > 0) {
			foreach ($uploadedFiles as $uploadedFile) {
				if (is_array($uploadedFile) && isset($uploadedFile['file_id'])) {
					$document = Document::create([
						'uploaded_by'        => auth()->user()->id,
						'document_status_id' => 2,
						'verified_by'        => auth()->user()->id,
						'file_id'            => $uploadedFile['file_id'],
						'file_name'          => $uploadedFile['filename'],
						'file_path'          => $uploadedFile['url'],
						'file_mime_type'     => $uploadedFile['mime_type'],
						'file_size'          => $uploadedFile['size'],
						'reason'             => null
					]);

					$documentIds[] = $document->id;
				} else {
					Log::error('Gagal menyimpan dokumen. Tidak ada file_id dalam response.', [$uploadedFile]);
				}
			}
		} else {
			Log::error('Format response upload file tidak sesuai.', [$uploadedFiles]);
		}

		return $documentIds;
	}

	public static function deleteDocuments(array $documentIdsToDelete): void
	{
		$documents = Document::whereIn('id', $documentIdsToDelete)->get();
		$fileIds = $documents->pluck('file_id')->toArray();

		if (!empty($fileIds)) {
			StorageServerHelper::deleteFromServer($fileIds);
		}

		Document::whereIn('id', $documentIdsToDelete)->delete();
	}

	public static function deleteDocumentsAndNullify(mixed $model, string $documentField = 'document_id'): void
	{
		$documentIds = is_array($model->{$documentField})
			? $model->{$documentField}
			: json_decode($model->{$documentField}, true);

		if (!empty($documentIds)) {
			$fileIds = Document::whereIn('id', $documentIds)->pluck('file_id')->toArray();

			$model->update([$documentField => null]);

			if (!empty($fileIds)) {
				StorageServerHelper::deleteFromServer($fileIds);
			}

			Document::whereIn('id', $documentIds)->delete();
		}
	}
}
