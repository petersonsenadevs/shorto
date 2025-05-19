<?php

namespace App\Http\Controllers\Api\v1\UrlGroup;

use App\Exceptions\GroupNotFoundException;
use App\Exceptions\UrlNotFoundException;
use App\Http\Controllers\Controller;
use App\Services\UrlGroup\AssignGroupFromUrlService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class AssignGroupFromUrlController extends Controller
{
    public function __construct(private AssignGroupFromUrlService $assignGroupToUrlService){}

    public function __invoke(Request $request) {
        try{
         $this->assignGroupToUrlService->execute($request->groupId, $request->urlId, $request->user()->id);
            return response()->json(['message' => 'Url assigned to group successfully'], 200);
        }catch( GroupNotFoundException | UrlNotFoundException  $e){
            throw new HttpResponseException(response()->json(['message' => $e->getMessage()], $e->getCode()));
        }
    }
}
