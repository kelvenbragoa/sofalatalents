<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAuthController extends Controller
{
    public function login(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8',
        ]);

        if (!Auth::attempt($loginUserData)) {
            return response()->json([
                'message' => 'Email ou palavra-passe incorrectos',
            ], 401);
        }

        $user = User::with('role')->where('email', $loginUserData['email'])->first();

        if (!$user || !$user->role || strcasecmp($user->role->name, 'Staff') !== 0) {
            Auth::logout();

            return response()->json([
                'message' => 'Apenas utilizadores Staff podem aceder a esta aplicação',
            ], 403);
        }

        $token = $user->createToken($user->name.'-StaffToken')->plainTextToken;

        return response()->json([
            'user' => $this->payload($user, $token),
        ]);
    }

    public function me(Request $request)
    {
        $user = Auth::user()->load('role');
        $token = $request->header('token');

        return response()->json([
            'user' => $this->payload($user, $token),
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Sessão terminada',
        ]);
    }

    private function payload(User $user, ?string $token): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role?->name ?? 'Staff',
            'token' => $token,
        ];
    }
}
