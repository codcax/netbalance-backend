<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class EmailLinkController extends Controller
{
    use HttpResponses;

    /**
     * Send a new email verification notification.
     * 
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                config('app.frontend_url') . '\dashboard'
            );
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->noContentResponse();
    }
}
