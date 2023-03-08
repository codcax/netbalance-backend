<?php

namespace App\Http\Controllers\Account;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\Account\DeleteAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;

class AccountController extends Controller
{
    use HttpResponses;

    /**
     * Display user account.
     * 
     * @param  Request
     * 
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->user()->id);

        return $this->successResponse(new UserResource($user));
    }

    /**
     * Update the user account.
     *
     * @param  UpdateAccountRequest
     * 
     * @return JsonResponse
     */
    public function update(UpdateAccountRequest $request): JsonResponse
    {
        $request->validated($request->all());

        $userUpdated = User::where('id', Auth::user()->id)->update(
            $request->all()
        );

        if (!$userUpdated) {
            return $this->errorResponse([], 'Record could not be updated.');
        }

        return $this->noContentResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteAccountRequest $request
     * 
     * @return JsonResponse
     */
    public function destroy(DeleteAccountRequest $request): JsonResponse
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
