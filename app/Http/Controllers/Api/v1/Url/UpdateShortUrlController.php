<?php

namespace App\Http\Controllers\Api\v1\Url;

use App\Exceptions\ShortUrlExistException;
use App\Exceptions\UrlNotFoundException;
use App\Http\Controllers\Controller;
use App\Services\Url\UpdateShortenUrlService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class UpdateShortUrlController extends Controller
{

    public function __construct(private UpdateShortenUrlService $updateShortenUrlService){}
    public function __invoke(Request $request)
    {

      try{
      $url =  $this->updateShortenUrlService->updateUrl(
          originalUrl: $request->originalUrl??null,
          shortenUrl: $request->shortenUrl ?? null,
          customAlias: $request->customAlias ?? null,
          password: $request->password ?? null,
          description: $request->description??null,
          urlId: $request->urlId??null,
          groupId: $request->groupId ?? null,
          isActive: $request->isActive  ?? null,
          userId: $request->user()->id
        );
        return response()->json([
            'short_url' => $url

            
        ], 200);
      }catch(ShortUrlExistException | UrlNotFoundException $e){
        throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
      }
    }
}
