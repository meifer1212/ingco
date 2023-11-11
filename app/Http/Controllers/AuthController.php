<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



/**
 * @OA\Info(
 *     title="Laravel API",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="juanjo_meifer@hotmail.com"
 *    )
 * )
 */
/**
 * @OA\Schema(
 *     schema="User",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2023-01-01 12:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="datetime", example="2023-01-01 12:30:00"),
 * )
 */

 /**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints for user authentication"
 * )
 */
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'name' => 'required|min:3|max:255'
        ], [], [
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'name' => 'nombre'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password) // bcrypt() is a helper function that hashes the password
        ]);

        Auth::login($user);

        return redirect()->route('tasks.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [], [
            'email' => 'correo electrónico',
            'password' => 'contraseña'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('tasks.index');
        }

        return redirect()->back()->withInput()->withErrors(
            'Las credenciales no coinciden con nuestros registros'
        );
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
/**
 * @OA\Post(
 *     path="/api/login",
 *     operationId="loginAPI",
 *     tags={"Authentication"},
 *     summary="Iniciar sesión",
 *     description="Inicia sesión con las credenciales proporcionadas",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="usuario@dominio.com"),
 *             @OA\Property(property="password", type="string", format="password", example="contraseña"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Inicio de sesión exitoso",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="user"),
 *             @OA\Property(property="token", type="string", example="auth_token"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Credenciales incorrectas",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Las credenciales no coinciden con nuestros registros"),
 *         ),
 *     ),
 * )
 */
    public function loginAPI(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [], [
            'email' => 'correo electrónico',
            'password' => 'contraseña'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::find(Auth::user()->id);
            return response()->json([
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken
            ]);
        }

        return response()->json([
            'message' => 'Las credenciales no coinciden con nuestros registros'
        ], 401);
    }

/**
 * @OA\Post(
 *     path="/api/register",
 *     operationId="registerAPI",
 *     tags={"Authentication"},
 *     summary="Registro de usuario",
 *     description="Registra un nuevo usuario",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password"},
 *             @OA\Property(property="name", type="string", example="Nombre Usuario"),
 *             @OA\Property(property="email", type="string", format="email", example="usuario@dominio.com"),
 *             @OA\Property(property="password", type="string", format="password", example="contraseña"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Registro exitoso",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="user"),
 *             @OA\Property(property="token", type="string", example="auth_token"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Error de validación"),
 *             @OA\Property(property="errors", type="object", example={"field": {"error message"}}),
 *         ),
 *     ),
 * )
 */
    public function registerAPI(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'name' => 'required|min:3|max:255'
        ], [], [
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'name' => 'nombre'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password) // bcrypt() is a helper function that hashes the password
        ]);

        Auth::login($user);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }
/**
 * @OA\Post(
 *     path="/api/logout",
 *     operationId="logoutAPI",
 *     tags={"Authentication"},
 *     summary="Cerrar sesión",
 *     description="Cierra la sesión actual del usuario",
 *     @OA\Response(
 *         response=200,
 *         description="Sesión cerrada correctamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Sesión cerrada correctamente"),
 *         ),
 *     ),
 *     security={
 *         {"passport": {}}
 *     }
 * )
 */
    public function logoutAPI()
    {
        $user = User::find(Auth::user()->id);
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
