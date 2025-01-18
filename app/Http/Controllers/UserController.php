<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return $this->apiResponse(200, User::all());
    }

    public function login(Request $request)
    {
        $validated = $this->validate([
            'username' => ['required', 'max:255'],
            'password' => ['required', 'min:8'],
        ], $request);

        $credentials = $request->all();
        if (!Auth::attempt($credentials)) {
            return $this->apiResponse(400, 'invalid username or password');
        }

        $user = User::where('username', $validated['username'])->first();
        $token = $user->createToken('auth')->plainTextToken;

        return $this->apiResponse(200, [
            'token' => $token,
            'tokenType' => 'Bearer'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->apiResponse(200, 'ok');
    }

    protected function validateUser(array $input)
    {
        $validator = Validator::make($input, [
            'username' => ['required', 'unique:users', 'max:255'],
            'firstName' => ['required', 'max:255'],
            'lastName' => ['required', 'max:255'],
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'min:8'],
        ]);
        $validated = $this->validate($validator);

        return $validated;
    }

    public function batchStore(Request $request)
    {
        $users = $request->all();

        $models = [];
        $error = null;
        foreach ($users as $user) {
            $userArray = $this->validateUser($user);
            if ($userArray instanceof Response) {
                $error = $userArray;
                break;
            }
            $models[] = $userArray;
        }

        if ($error) {
            return $error;
        }

        foreach ($models as $userArray) {
            User::create([
                'username' => $userArray['username'],
                'password' => $userArray['password'],
                'first_name' => $userArray['firstName'],
                'last_name' => $userArray['lastName'],
                'email' => $userArray['email'],
            ]);
        }

        return $this->apiResponse(200, 'ok');
    }

    public function store(Request $request)
    {
        $userArray = $this->validateUser($request->all());

        if ($userArray instanceof Response) {
            return $userArray;
        }

        $user = User::create([
            'username' => $userArray['username'],
            'password' => $userArray['password'],
            'first_name' => $userArray['firstName'],
            'last_name' => $userArray['lastName'],
            'email' => $userArray['email'],
        ]);

        return $this->apiResponse(200, strval($user->id));
    }

    public function show($user)
    {
        $userModel = User::find($user);
        if (!$userModel) {
            return $this->apiResponse(404, 'not found');
        }

        return $this->apiResponse(200, $user);
    }


    public function update(Request $request, $username)
    {
        $validated = $this->validate([
            'id' => ['required', 'numeric'],
            'username' => ['required', 'max:255'],
            'firstName' => ['required', 'max:255'],
            'lastName' => ['required', 'max:255'],
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'min:8'],
        ], $request);

        if (($validated instanceof Response)) {
            return $validated;
        }

        $takenUser = User::whereNot('id', $validated['id'])
            ->where('username', $validated['username'])
            ->orWhere('email', $validated['email'])
            ->first();
        if ($takenUser) {
            return $this->apiResponse(400, $takenUser);
        }

        $user = User::whereUsername($validated['username'])->first();

        $user->update([
            'username' => $validated['username'],
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->save();

        return $this->apiResponse(200, strval($user->id));
    }

    public function destroy($user)
    {
        $userModel = User::find($user);
        if (!$userModel) {
            return $this->apiResponse(404, 'not found');
        }
        $userModel->delete();
        return $this->apiResponse(200, strval($userModel->id));
    }
}
