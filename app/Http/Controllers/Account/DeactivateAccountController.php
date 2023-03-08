<?php

namespace App\Http\Controllers\Account;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Account\DeactivateAccountRequest;

class DeactivateAccountController extends Controller
{
    use HttpResponses;

    /**
     * Remove the specified resource from storage.
     *
     * @param DeactivateAccountRequest $request
     * 
     * @return JsonResponse
     */
    public function destroy(DeactivateAccountRequest $request): JsonResponse
    {
        $request->validated($request->all());

        $user = User::findOrFail(Auth::user()->id);

        if (!Hash::check($request->password, $user->password)) {
            return $this->unprocessableResponse([], 'The password is incorrect.');
        }

        Auth::guard('web')->logout();

        rescue(
            function () use ($user) {
                $user->delete();
                return true;
            },
            false
        );

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->noContentResponse();
    }
}
