<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request,  User $user)
    {
        $input = $request->validated();
        $user = $request->user();
        $data = [];
        try {
            if ($request->password) {
                $input['password'] = bcrypt($input['password']);
            }
            $user->update($input);
            $data['user'] = new UserResource(Auth::user());
        } catch (\Throwable $th) {
            return $this->sendError('error in the data submitted', $th->getMessage());
        }

        return $this->sendResponse($data, 'User updated successfully.');
    }
    public function uploadImage(Request $request, User $user)
    {
        //
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|file|mimes:jpg,png,jeg,gif|max:2048'
        ]);
        if ($validator->fails()) {
            //loop through errors and return them as array of objects
            $errors = [];
            foreach ($validator->errors()->all() as $key => $error) {
                $field  = $validator->errors()->keys()[$key];
                $errors[$field] =  $error;
            }
            return $this->sendError('error in the data submitted', $errors);
        }
        $user = Auth::user();
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars');
            $user->avatar = $path;
        }
        $user->save();
        return $this->sendResponse(["user" => new UserResource(Auth::user())], 'User updated successfully.');
    }
}