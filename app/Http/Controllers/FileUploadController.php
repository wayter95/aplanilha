<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Exception;

class FileUploadController extends Controller
{
    private S3Client $s3Client;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);
    }

    /**
     * Gera URL pré-assinada para upload direto ao S3
     */
    public function generatePresignedUrl(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'filename' => 'required|string|max:255',
                'content_type' => 'required|string',
                'folder' => 'nullable|string|max:255',
                'auto_generate_name' => 'boolean',
            ]);

            $filename = $request->input('filename');
            $contentType = $request->input('content_type');
            $folder = $request->input('folder', 'uploads');
            $autoGenerateName = $request->input('auto_generate_name', true);

            // Gerar nome único se solicitado
            if ($autoGenerateName) {
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $extension;
            }

            // Construir a chave (key) do arquivo
            $key = $folder . '/' . $filename;

            // Configurar parâmetros para URL pré-assinada
            $expires = '+15 minutes'; // Tempo de expiração
            $bucket = config('filesystems.disks.s3.bucket');

            // Gerar URL pré-assinada para PUT
            $cmd = $this->s3Client->getCommand('PutObject', [
                'Bucket' => $bucket,
                'Key' => $key,
                'ContentType' => $contentType,
            ]);

            $presignedRequest = $this->s3Client->createPresignedRequest($cmd, $expires);
            $presignedUrl = (string) $presignedRequest->getUri();

            return response()->json([
                'success' => true,
                'url' => $presignedUrl, // URL para upload (PUT)
                'key' => $key, // Key para salvar no banco
                'bucket' => $bucket,
                'expires_in' => 900, // 15 minutos em segundos
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar URL pré-assinada: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gera URL temporária para visualização/download
     */
    public function generateTemporaryUrl(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'key' => 'required|string',
            ]);

            $key = $request->input('key');
            $expires = '+1 hour'; // Tempo de expiração para visualização
            $bucket = config('filesystems.disks.s3.bucket');

            // Gerar URL pré-assinada para GET
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            $presignedRequest = $this->s3Client->createPresignedRequest($cmd, $expires);
            $temporaryUrl = (string) $presignedRequest->getUri();

            return response()->json([
                'success' => true,
                'temporary_url' => $temporaryUrl,
                'expires_in' => 3600, // 1 hora em segundos
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar URL temporária: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gera URL pré-assinada para leitura (GET) - novo endpoint
     */
    public function getSignedUrl(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'key' => 'required|string',
            ]);

            $key = $request->input('key');
            $expires = '+5 minutes'; // Tempo de expiração para leitura
            $bucket = config('filesystems.disks.s3.bucket');

            // Gerar URL pré-assinada para GET
            $cmd = $this->s3Client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            $presignedRequest = $this->s3Client->createPresignedRequest($cmd, $expires);
            $signedUrl = (string) $presignedRequest->getUri();

            return response()->json([
                'success' => true,
                'url' => $signedUrl,
                'expires_in' => 300, // 5 minutos em segundos
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar URL de leitura: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove arquivo do S3
     */
    public function deleteFile(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'key' => 'required|string',
            ]);

            $key = $request->input('key');
            $bucket = config('filesystems.disks.s3.bucket');

            $this->s3Client->deleteObject([
                'Bucket' => $bucket,
                'Key' => $key,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Arquivo removido com sucesso',
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover arquivo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
