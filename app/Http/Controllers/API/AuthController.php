<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|string',
                'lastname' => 'required|string',
                'email' => 'required|email|unique:users,email',
                // 'phone' => 'required|numeric|min:11|unique:users,phone',
                'password' => 'required',
                'confirm' => 'required|same:password',
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
            $input = $validator->validated();
            $input['password'] = bcrypt($input['password']);
            unset($input['confirm']);
            $user = User::create($input);
            $secret = env('TOKEN_SECRET');
            $success['token'] =  $user->createToken($secret)->plainTextToken;
            $success['user'] = new UserResource($user);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->sendError($th->getMessage(), $validator->errors());
        }
        DB::commit();

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',

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
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $secret = env('TOKEN_SECRET');
            $success['token'] =  $user->createToken($secret)->plainTextToken;
            $success['user'] =  new UserResource($user);
            //send welcome email to user
            //$user->notify(new \App\Notifications\WelcomeUser($user));
            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Invalid credentials.', [], 401);
        }
    }

    public function logOut(Request $request)
    {
        try {
            // Get user who requested the logout
            $user = $request->user();
            // Revoke current user token
            $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
            return $this->sendResponse([], 'User logout successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError($th->getMessage(), [], 401);
        }
        return $this->sendResponse([], 'User logout successfully.');
    }
}