<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    use HttpResponses;

    /**
     * Mark the authenticated user's email address as verified.
     * 
     * @param EmailVerificationRequest $request
     * @return JsonResponse
     */
    public function store(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                config('app.frontend_url') . '\login?verified=1'
            );
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(
            config('app.frontend_url') . '\login?verified=1'
        );
    }

    /**
     * Send a new email verification notification.
     * 
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function send(Request $request): JsonResponse|RedirectResponse
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
