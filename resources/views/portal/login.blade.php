<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal del Paciente · Iniciar sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login {
            display: flex;
            width: 100%;
            max-width: 1100px;
            min-height: 600px;
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        /* Panel izquierdo - Promo */
        .promo {
            flex: 1;
            background: linear-gradient(160deg, #4f46e5, #7c3aed, #a855f7);
            padding: 48px 44px;
            display: flex;
            align-items: center;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .promo::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -30%;
            width: 80%;
            height: 80%;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            pointer-events: none;
        }

        .promo::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -20%;
            width: 60%;
            height: 60%;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
            pointer-events: none;
        }

        .promo .z {
            position: relative;
            z-index: 1;
        }

        .promo .logo {
            width: 72px;
            height: 72px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin-bottom: 28px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .promo h2 {
            font-size: 34px;
            font-weight: 700;
            line-height: 1.15;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .promo p {
            font-size: 15px;
            opacity: 0.92;
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 320px;
        }

        .promo ul {
            list-style: none;
            padding: 0;
        }

        .promo ul li {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 0;
            font-size: 14.5px;
            opacity: 0.95;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: transform 0.2s;
        }

        .promo ul li:hover {
            transform: translateX(4px);
        }

        .promo ul li:last-child {
            border-bottom: none;
        }

        .promo ul li i {
            width: 22px;
            font-size: 18px;
            opacity: 0.9;
        }

        /* Panel derecho - Formulario */
        .panel {
            flex: 1.2;
            padding: 48px 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }

        .panel .box {
            width: 100%;
            max-width: 400px;
        }

        .panel h1 {
            font-size: 30px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .panel .sub {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 32px;
        }

        /* Alertas */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            animation: slideIn 0.4s ease;
        }

        .alert.ok {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert.error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert i {
            font-size: 20px;
            flex-shrink: 0;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Campos de formulario */
        .field {
            margin-bottom: 22px;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .field label i {
            margin-right: 6px;
            color: #6c63ff;
        }

        .field input {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.25s ease;
            background: #fafbfc;
            color: #1a1a2e;
        }

        .field input::placeholder {
            color: #9ca3af;
        }

        .field input:focus {
            outline: none;
            border-color: #7c3aed;
            box-shadow: 0 0 0 5px rgba(124, 58, 237, 0.08);
            background: #ffffff;
        }

        .field input.error {
            border-color: #ef4444;
        }

        .field input.error:focus {
            box-shadow: 0 0 0 5px rgba(239, 68, 68, 0.08);
        }

        .field .error-text {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* Checkbox */
        .remember {
            display: flex;
            align-items: center;
            margin-bottom: 26px;
        }

        .remember label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #4b5563;
            cursor: pointer;
            user-select: none;
        }

        .remember input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #7c3aed;
            cursor: pointer;
            flex-shrink: 0;
        }

        /* Botón */
        .btn-primary {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            letter-spacing: 0.3px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(124, 58, 237, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.25);
        }

        .btn-primary i {
            font-size: 18px;
        }

        /* Demo info */
        .demo {
            margin-top: 32px;
            padding: 20px 24px;
            background: #faf9fe;
            border-radius: 14px;
            font-size: 13px;
            color: #4b5563;
            border: 1px solid #ede9fe;
            line-height: 1.9;
        }

        .demo b {
            color: #1a1a2e;
        }

        .demo .demo-label {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: #fff;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 6px;
            letter-spacing: 0.5px;
        }

        .demo a {
            color: #7c3aed;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
            margin-top: 6px;
            transition: color 0.2s;
        }

        .demo a:hover {
            color: #4f46e5;
            text-decoration: underline;
        }

        .demo a i {
            margin-right: 6px;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .login {
                flex-direction: column;
                border-radius: 20px;
                min-height: auto;
            }

            .promo {
                padding: 36px 32px;
            }

            .promo h2 {
                font-size: 28px;
            }

            .promo p {
                max-width: 100%;
            }

            .panel {
                padding: 36px 32px;
            }

            .panel h1 {
                font-size: 26px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .login {
                border-radius: 16px;
            }

            .promo {
                padding: 28px 20px;
            }

            .promo .logo {
                width: 56px;
                height: 56px;
                font-size: 26px;
            }

            .promo h2 {
                font-size: 22px;
            }

            .promo ul li {
                font-size: 13px;
                padding: 10px 0;
            }

            .panel {
                padding: 28px 20px;
            }

            .panel h1 {
                font-size: 22px;
            }

            .panel .sub {
                font-size: 13px;
            }

            .demo {
                font-size: 12px;
                padding: 16px 18px;
            }
        }
    </style>
</head>
<body>
    <div class="login">
        <!-- Panel izquierdo - Promo -->
        <div class="promo">
            <div class="z">
                <div class="logo">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h2>Portal del<br>Paciente</h2>
                <p>Consulta tus próximas citas, tu historia clínica y tus recibos de pago en un solo lugar.</p>
                <ul>
                    <li>
                        <i class="fa-regular fa-calendar-check"></i>
                        Tus próximas citas
                    </li>
                    <li>
                        <i class="fa-solid fa-notes-medical"></i>
                        Tu historia clínica
                    </li>
                    <li>
                        <i class="fa-solid fa-receipt"></i>
                        Tus pagos y recibos
                    </li>
                </ul>
            </div>
        </div>

        <!-- Panel derecho - Formulario -->
        <div class="panel">
            <div class="box">
                <h1>Hola de nuevo 👋</h1>
                <p class="sub">Ingresa con el correo registrado en tu clínica.</p>

                @if(session('aviso'))
                    <div class="alert ok">
                        <i class="fa-solid fa-circle-info"></i>
                        {{ session('aviso') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('portal.login.attempt') }}">
                    @csrf
                    <div class="field">
                        <label for="email">
                            <i class="fa-regular fa-envelope"></i> Correo
                        </label>
                        <input type="email" id="email" name="email" 
                               value="{{ old('email') }}" 
                               placeholder="ejemplo@correo.com" 
                               required autofocus
                               class="@error('email') error @enderror">
                        @error('email')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">
                            <i class="fa-solid fa-lock"></i> Contraseña
                        </label>
                        <input type="password" id="password" name="password" 
                               placeholder="••••••••" 
                               required
                               class="@error('password') error @enderror">
                        @error('password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="remember">
                        <label>
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            Recordarme
                        </label>
                    </div>

                    <button class="btn-primary" type="submit">
                        <i class="fa-solid fa-right-to-bracket"></i> 
                        Ingresar
                    </button>
                </form>

                <div class="demo">
                    <div>
                        <span class="demo-label">Demo</span>
                        <b>Credenciales de prueba</b>
                    </div>
                    <div>
                        Correo: <b>valentina@paciente.test</b> · Contraseña: <b>password</b>
                    </div>
                    <a href="{{ route('login') }}">
                        <i class="fa-regular fa-user"></i> 
                        ¿Eres personal de la clínica? Ingresa aquí
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>