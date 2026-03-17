<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class InstagramCallbackController extends Controller
{
    public function deauthorize(): JsonResponse
    {
        return response()->json(['success' => true]);
    }

    public function delete(): JsonResponse
    {
        return response()->json([
            'url' => url('/privacy-policy'),
            'confirmation_code' => 'jikra_' . now()->timestamp,
        ]);
    }
}
