<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'level' => 'required|in:admin,kasir,koki,pelanggan'  // Tambahkan pelanggan jika diperlukan
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'level' => $request->input('level'),
            'api_token' => \Illuminate\Support\Str::random(60),
            'status' => 1
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Register berhasil',
            'data' => $user
        ], 201);
    }

    public function index()
    {
        try {
            $users = User::all(); // Now we can get all users since pelanggan is in separate table
            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $user = \App\Models\User::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $user = \App\Models\User::findOrFail($id);
            $user->update($request->only(['status']));
            return response()->json([
                'status' => 'success',
                'message' => 'Status user berhasil diubah',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah status user: ' . $e->getMessage()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Check if account is active
            if ($user->status != 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akun Anda tidak aktif'
                ], 401);
            }

            // Check user level
            if (!in_array($user->level, ['admin', 'kasir', 'koki'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke sistem admin'
                ], 403);
            }

            // Generate token
            $token = Str::random(60);
            $user->api_token = $token;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'level' => $user->level,
                    'status' => $user->status
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Email atau password salah'
        ], 401);
    }
    public function logout(Request $request)
    {
        $token = $request->header('Authorization');

        if ($token) {
            $user = User::where('api_token', $token)->first();
            if ($user) {
                $user->api_token = null;
                $user->save();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}
