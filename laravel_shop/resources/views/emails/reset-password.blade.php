<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #fff; background-color: #0a0a0c; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 600px; margin: 20px auto; padding: 40px; background: #121214; border: 1px solid #1f1f23; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { color: #00D2FF; font-size: 28px; font-weight: 900; text-transform: uppercase; font-style: italic; letter-spacing: -1px; }
        .logo span { color: #8B5CF6; }
        .content { background: rgba(255,255,255,0.03); border-radius: 15px; padding: 30px; border: 1px solid rgba(255,255,255,0.05); }
        h1 { font-size: 20px; font-weight: 900; text-transform: uppercase; margin-top: 0; color: #fff; }
        p { font-size: 14px; color: #94a3b8; }
        .button-container { text-align: center; margin: 30px 0; }
        .button { display: inline-block; padding: 15px 35px; background: linear-gradient(90deg, #00D2FF, #8B5CF6); color: #000 !important; font-weight: 900; text-decoration: none; border-radius: 12px; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3); }
        .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #475569; text-transform: uppercase; letter-spacing: 1px; }
        .link-alt { word-break: break-all; font-size: 10px; color: #475569; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Soul <span>Guild</span></div>
        </div>
        <div class="content">
            <h1>Recuperación de Contraseña</h1>
            <p>Hola,</p>
            <p>Has recibido este correo porque hemos recibido una solicitud para restablecer la contraseña de tu cuenta en el Gremio.</p>
            <p>Si no realizaste esta solicitud, puedes ignorar este mensaje con total seguridad. Tu contraseña actual seguirá funcionando.</p>
            
            <div class="button-container">
                <a href="{{ $url }}" class="button">Restablecer Contraseña</a>
            </div>
            
            <p>Este enlace de recuperación expirará en 60 minutos.</p>
            <p>¡Nos vemos en el campo de batalla!<br>El equipo de Soul Guild</p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} Soul Guild Store. Sistema de Seguridad Avanzado.
        </div>
        
        <div class="link-alt">
            Si tienes problemas con el botón, copia y pega esta URL en tu navegador:<br>
            {{ $url }}
        </div>
    </div>
</body>
</html>
