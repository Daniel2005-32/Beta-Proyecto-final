<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    /**
     * Enviar enlace de recuperación de contraseña.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'No encontramos un usuario con ese correo.'], 404);
        }

        // Generar token manualmente usando el broker de Laravel
        $token = Password::broker()->createToken($user);
        // Formato correcto para Vue Router: /reset-password/token?email=...
        $resetUrl = 'https://soul-guild.onrender.com/reset-password/' . $token . '?email=' . urlencode($user->email);

        // Enviar email por la API de Resend
        try {
            $html = "
            <div style='background-color: #0a0a0c; color: #ffffff; font-family: sans-serif; padding: 40px; border-radius: 20px; max-width: 600px; margin: auto; border: 1px solid #1a1a1c;'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h1 style='color: #00d2ff; text-transform: uppercase; font-style: italic; letter-spacing: -1px; margin: 0;'>Soul <span style='color: #ffffff;'>Guild</span></h1>
                    <p style='color: #7000ff; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 3px;'>Protocolo de Seguridad</p>
                </div>
                
                <div style='background: #121214; padding: 30px; border-radius: 15px; border-left: 4px solid #00d2ff;'>
                    <h2 style='margin-top: 0; color: #ffffff;'>Hola, {$user->name}</h2>
                    <p style='color: #a0a0a0; line-height: 1.6;'>Has recibido este correo porque solicitaste restablecer tu contraseña en Soul Guild.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetUrl}' style='display: inline-block; padding: 15px 30px; background: linear-gradient(90deg, #00d2ff, #7000ff); color: white; text-decoration: none; border-radius: 10px; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; box-shadow: 0 10px 20px rgba(0, 210, 255, 0.2);'>Restablecer Contraseña</a>
                    </div>
                    
                    <p style='color: #555; font-size: 11px; text-align: center;'>Si no solicitaste este cambio, puedes ignorar este mensaje de forma segura.</p>
                </div>
                
                <div style='margin-top: 30px; text-align: center;'>
                    <p style='color: #333; font-size: 10px;'>© " . date('Y') . " Soul Guild. Todos los derechos reservados.</p>
                </div>
            </div>";

            \Illuminate\Support\Facades\Http::withToken(env('MAIL_PASSWORD'))
                ->post('https://api.resend.com/emails', [
                    'from' => env('MAIL_FROM_ADDRESS', 'onboarding@resend.dev'),
                    'to' => [$user->email],
                    'subject' => '🔑 Soul Guild - Restablecer contraseña',
                    'html' => $html,
                ]);

            return response()->json(['message' => 'Se ha enviado un enlace de recuperación a tu correo electrónico.'], 200);
        } catch (\Exception $e) {
            \Log::error("Error enviando recuperación por API: " . $e->getMessage());
            return response()->json(['error' => 'No se pudo enviar el correo en este momento.'], 500);
        }
    }

    /**
     * Procesar el restablecimiento de la contraseña.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Tu contraseña ha sido restablecida correctamente.'], 200);
        }

        return response()->json(['error' => 'El token es inválido o ha expirado.'], 400);
    }
}
