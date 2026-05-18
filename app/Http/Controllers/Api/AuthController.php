<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;

class AuthController extends Controller
{
    /**
     * Autenticar al usuario para la aplicación móvil
     */
    public function login(Request $request)
    {
        // 1. Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'email' => 'required|string', // Puede ser email o username dependiendo de cómo se loguean
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // 2. Intentar autenticar con email o username
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        // Alternativa por si usan username en lugar de email para loguearse:
        // $credentials = ['username' => $request->email, 'password' => $request->password];

        if (Auth::attempt($credentials)) {
            // Autenticación exitosa
            $user = Auth::user();
            
            // Verificar si el usuario está activo (suponiendo que 'estado' indica si está activo)
            if (isset($user->estado) && $user->estado != 1 && $user->estado != 'Activo') {
                return response()->json([
                    'status' => false,
                    'message' => 'El usuario se encuentra inactivo.'
                ], 403);
            }

            // Cargar la relación sede para obtener el nombre
            $user->load('sede');

            return response()->json([
                'status' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    'user' => $user
                ]
            ], 200);
        } else {
            // Autenticación fallida
            return response()->json([
                'status' => false,
                'message' => 'Credenciales incorrectas. Verifique su correo/usuario y contraseña.'
            ], 401);
        }
    }
}
