<?php

namespace App\Http\Controllers;

class TestController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        return response()->json([
            'user' => $user,
        ]);
    }
}