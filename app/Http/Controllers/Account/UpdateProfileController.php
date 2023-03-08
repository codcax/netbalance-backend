<?php

namespace App\Http\Controllers\Account;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
    public function store(UpdateProfileRequest $request): JsonResponse
    {
        $request->validated($request->all());

        User::where('id',Auth::user()->id)->update(
            $request->all()
        );

        return $this->noContentResponse();
    }
}
