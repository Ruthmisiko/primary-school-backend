<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\API\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepo
    )
    {
        $this->userRepository = $userRepo;

    }

    public function register(RegisterRequest $request) {

            DB::beginTransaction();
            try {
                $input = $request->all();

                if (isset($request->validator) && $request->validator->fails()) {
                    return response([
                        'status' => 'failed',
                        'errors' => $request->validator->errors()
                    ], 422);
                }

                $token = base64_encode($input['password']);


                $user_input['id'] = Str::uuid()->toString();
                $user_input['name'] = $input['name'];
                $user_input['email'] = $input['email'];
                $user_input['username'] = $input['username'];
                $user_input['status'] = '0';
                $user_input['phone_number'] = $input['phone_number'];
                $user_input['userType'] = 'admin';
                $user_input['userOTP'] = $token;
                $user_input['password'] =  Hash::make($input['password']);
                $user_input['remember_token'] = Str::random(10);
                $user = $this->userRepository->create($user_input);

                $token = $user->createToken('authToken')->accessToken;

                DB::commit();

                return response()->json([
                    'message' => 'User registered successfully',
                    'user' => $user,
                    'token' => $token
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json(['error' => $e->getMessage()]);
            }
    }

    public function logout(Request $request) {

            $token = $request->user()->token();
            $token->revoke();
            $response = ['message' => 'You have been successfully logged out!'];

            return response()->json($response, 200);
    }



}
