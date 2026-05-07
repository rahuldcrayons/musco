<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountProxyController extends Controller
{
    /**
     * Forward /account/* requests to their public counterparts while keeping auth middleware applied.
     */
    public function forward(Request $request, ?string $path = null): Response
    {
        $targetPath = trim($path ?? '', '/');

        // Prevent loops if someone hits /account/account/...
        if ($targetPath === '' || str_starts_with($targetPath, 'account')) {
            abort(404);
        }

        $forwarded = Request::create(
            '/' . $targetPath,
            $request->method(),
            $request->all(),
            $request->cookies->all(),
            $request->allFiles(),
            $request->server->all(),
            $request->getContent()
        );

        // Mark forwarded requests (optional for debugging/middleware checks)
        $forwarded->headers->set('X-Account-Passthrough', '1');

        return app()->handle($forwarded);
    }
}
