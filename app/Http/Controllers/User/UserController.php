<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserCollection;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    use HttpResponses;

    /**
     * Create the controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new UserCollection(User::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUserRequest
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request): JsonResponse
    {

        $request->validated($request->all());

        $userModel = rescue(function () use ($request) {
            return User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => $request->password
            ]);
        }, false);

        if (!$userModel) {
            return $this->errorResponse([], 'Record could not be created.');
        }

        event(new Registered($userModel));

        return $this->noContentResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param  User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return new UserResource(User::find($user->id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUserRequest
     * @param  User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $request->validated($request->all());

        $userUpdated = rescue(function () use ($request, $user) {
            $inputs = $request->except(['old_password']);
            $user->update($inputs);
            return true;
        }, false);

        if (!$userUpdated) {
            return $this->errorResponse([], 'Record could not be updated.');
        }

        return $this->successResponse($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteUserRequest $request
     * @param  User $user
     * @return JsonResponse
     */
    public function destroy(DeleteUserRequest $request, User $user): JsonResponse
    {
        $request->validated($request->all());

        if (!Hash::check($request->password, $user->password)) {
            return $this->unprocessableResponse([], 'The password is incorrect.');
        }

        $userDeleted = rescue(function () use ($request, $user) {
            $user->forceFill(
                [
                    'remember_token' => Str::random(60),
                    'is_active' => false
                ]
            )->save();
            $user->delete();
            return true;
        }, false);

        if (!$userDeleted) {
            return $this->errorResponse([], 'Record could not be deleted.');
        }

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->noContentResponse();
    }
}
