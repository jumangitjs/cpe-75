<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class UserController extends Controller
{
    public function signUp(Request $request)
    {
        $this->validate($request,[
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required',
            'email' => 'required|email'
        ]);

        $user = new User([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password'))
        ]);

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $user->role = false;

        $user->save();

        return response()->json([
            'message' => 'Successfully registered!',
            'user' => $user
        ], 200);
    }

    public function signIn(Request $request)
    {
        $credentials = $this->extractCredentials($request);

        try {
            if(!$token = JWTAuth::attempt($credentials))
            {
                return response()->json([
                    'error' => 'invalid credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Error generating token!'
            ], 500);
        }

        $user = User::where('username',
            $credentials['username'])->first();

        return response()->json([
            'token' => $token,
            'user' => $user
        ], 200);
    }

    private function extractCredentials(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        return $request->only('username', 'password');
    }

    /*
     *
     * ADMIN CRUD FOR ACCOUNTS
     *
     * */

    private function checkRole()
    {
        $account = JWTAuth::parseToken()->toUser();
        return $account->role;
    }

    private function unauthorizedResponse()
    {
        return response()->json([
            'message' => 'Unauthorized access!'
        ], 401);
    }

    public function getUsers()
    {
        $user = JWTAuth::parseToken()->toUser();

        if($this->checkRole())
        {
            $accounts = User::all();

            return response()->json(
                $accounts
            , 200);
        }
        else
        {
            return $this->unauthorizedResponse();
        }
    }

    public function deleteUser($id)
    {
        if($this->checkRole())
        {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'message' => 'Successfully deleted user'
            ], 200);
        }
        else
        {
            return $this->unauthorizedResponse();
        }
    }

    public function putUser(Request $request, $id)
    {
        $user = JWTAuth::parseToken()->toUser();
        if($this->checkRole() || $user->id == $id)
        {
            $user = User::find($id);
            if(!$user)
            {
                return response()->json([
                    'message' => 'Entry not found'
                ], 404);
            }

            $this->transferData($request, $user);

            $user->save();

            return response()->json([
                $user
            ], 200);
        }
        else
        {
            return $this->unauthorizedResponse();
        }
    }

    private function transferData(Request $request, User $user)
    {
        if($request->input('first_name'))
            $user->first_name = $request->input('first_name');
        if($request->input('last_name'))
            $user->last_name = $request->input('last_name');
        if($request->input('username'))
            $user->username = $request->input('username');
        if($request->input('password'))
            $user->password = bcrypt($request->input('password'));
        if($request->input('email'))
            $user->email = $request->input('email');
    }
}
