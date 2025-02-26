<?php
namespace App\Http\Controllers;

use App\Http\Requests\EncodeUrlRequest;
use App\Http\Requests\DecodeUrlRequest;
use App\Services\UrlShortenerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UrlShortenerController extends Controller
{
    public function __construct(private readonly UrlShortenerService $urlShortenerService)
    {
    }

    public function encode(EncodeUrlRequest $request): JsonResponse
    {
        return $this->successResponse(
            "Original URL encoded successfully",
            ['url' => $this->urlShortenerService->encode($request->url)]
        );
    }

    public function decode(DecodeUrlRequest $request): JsonResponse
    {
        try {
            $originalUrl = $this->urlShortenerService->decode($request->shortUrl);
            if (is_null($originalUrl)) {
                return $this->notFoundResponse("Short URL not found or expired");
            }
            return $this->successResponse("Short URL decoded successfully", ['url' => $originalUrl]);
        } catch (\Throwable $e) {
            Log::error("Decoding error: {$e->getMessage()}");
            return $this->errorResponse("Error decoding URL: {$e->getMessage()}", 500);
        }
    }
    private function successResponse(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }
    private function notFoundResponse(string $message, int $status = 404): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => ['url' => null]
        ], $status);
    }

    private function errorResponse(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $status);
    }
}
