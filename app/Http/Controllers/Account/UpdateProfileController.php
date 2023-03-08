<?php

namespace App\Http\Controllers\Account;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\Account\UpdateProfileRequest;

class UpdateProfileController extends Controller
{
    use HttpResponses;

    /**
     * Update the user account.
     *
     * @param  UpdateProfileRequest
     * 
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
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
}
