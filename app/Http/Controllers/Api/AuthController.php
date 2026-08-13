<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\User;
use App\Vendedor;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * Body: { "email": "...", "password": "..." }
     * Devuelve api_token (guardar en el cliente) + user + vendedor (si aplica)
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Errores de validación',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (isset($user->estado) && $user->estado != 1 && $user->estado != 'Activo') {
                Auth::logout();
                return response()->json([
                    'status'  => false,
                    'message' => 'El usuario se encuentra inactivo.',
                ], 403);
            }

            $user->load('sede');

            // Rotar token (invalida sesiones anteriores)
            $user->api_token = Str::random(60);
            $user->save();

            $vendedor = Vendedor::where('usuario_id', $user->id)->first();

            $roles = $user->getRoleNames();
            $userData = $user->toArray();
            $userData['is_admin'] = $user->isAdmin();
            $userData['is_vendedor'] = $roles->contains('VENDEDOR (a)') || $roles->contains('COBRADOR (a) / VENDEDOR (a)');
            $userData['is_cobrador'] = $roles->contains('COBRADOR (a)') || $roles->contains('COBRADOR (a) / VENDEDOR (a)');

            return response()->json([
                'status'  => true,
                'message' => 'Inicio de sesión exitoso',
                'data'    => [
                    'user'       => $userData,
                    'vendedor'   => $vendedor,
                    'api_token'  => $user->api_token,
                ],
            ], 200);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Credenciales incorrectas.',
        ], 401);
    }

    /**
     * POST /api/logout
     * Invalida el token actual (lo pone en null).
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }
        return response()->json([
            'status'  => true,
            'message' => 'Sesión cerrada',
        ]);
    }

    /**
     * GET /api/me
     * Devuelve el usuario autenticado (útil para validar el token al iniciar la app).
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('sede');
        $vendedor = Vendedor::where('usuario_id', $user->id)->first();

        $roles = $user->getRoleNames();
        $userData = $user->toArray();
        $userData['is_admin'] = $user->isAdmin();
        $userData['is_vendedor'] = $roles->contains('VENDEDOR (a)') || $roles->contains('COBRADOR (a) / VENDEDOR (a)');
        $userData['is_cobrador'] = $roles->contains('COBRADOR (a)') || $roles->contains('COBRADOR (a) / VENDEDOR (a)');

        return response()->json([
            'status' => true,
            'data'   => [
                'user'     => $userData,
                'vendedor' => $vendedor,
            ],
        ]);
    }
}
