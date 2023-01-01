<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function registration(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|unique:users|min:3|max:20',
            'email' => 'required|email|unique:users|min:3|max:20',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => $validator->errors(),
            ];
            return response()->json($response, 400);
        }

        $hashPassword = Hash::make($request->password);
//        dd($hashPassword);

        try {
            $userDataInsert = User::create([
                'user_name' => $request->user_name,
                'email' => $request->email,
                'password' => $hashPassword
            ]);

            $success['token'] = $userDataInsert->createToken('MyApp')->plainTextToken;
            $success['user_name'] = $userDataInsert->user_name;

            return response()->json([$success, 'success' => 'User created successfully.', $userDataInsert], 200);

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }

    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages(['email' => 'Please provide a valid credentials']);
            }

            $success['token'] = $user->createToken('MyApp')->plainTextToken;

            return response()->json(['user' => $user, 'token' => $success, 'success' => 'User login successfully.']);

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function logout(Request $request){
//        Session::flush();
//        Auth::logout();
//        $request->user()->token()->delete();
        auth()->user()->tokens()->delete();
//       dd($user);
        return response()->json(['success'=>'User Successfully Logout']);
    }
}
