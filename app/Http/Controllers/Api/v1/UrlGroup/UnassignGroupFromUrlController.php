<?php

namespace App\Http\Controllers\Api\v1\UrlGroup;

use App\Http\Controllers\Controller;
use App\Services\UrlGroup\UnassignGroupFromUrlService;
use Illuminate\Http\Request;

class UnassignGroupFromUrlController extends Controller
{
    public function __construct(private UnassignGroupFromUrlService $unassignGroupFromUrlService){}

    public function __invoke(Request $request) {
        $this->unassignGroupFromUrlService->execute($request->urlId, $request->groupId, $request->user()->id);
        return response()->json(['message' => 'Url unassigned from group successfully'], 200);
    }
}
