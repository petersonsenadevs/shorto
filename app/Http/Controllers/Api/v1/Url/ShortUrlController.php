<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api\v1\Url;

use App\Exceptions\ShortUrlExistException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShortenUrlRequest;
use App\Services\Url\ShortenUrlService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ShortUrlController extends Controller
{
    public function __construct(private ShortenUrlService $shortenUrlService) {}

    public function __invoke(ShortenUrlRequest $request)
    {
        $data = $request->all();
        try {
            $shortUrl = $this->shortenUrlService->shortenUrl($data['original_url'], $data['shortened_url'] ?? null, $data['custom_alias'] ?? null, $data['password'] ?? null, $data['description'] ?? null, $request->user()->id);

            return response()->json([
                'short_url' => $shortUrl,
            ]);
        } catch (ShortUrlExistException $e) {
            throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
        }
    }
}
