<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Portal del Paciente · Iniciar sesión</title>

    <!-- Fuente -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <!-- CSS principal -->
    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >


    <style>

        /* =====================================================
           VARIABLES
        ===================================================== */

        :root {

            --primary: #1088BA;
            --primary-dark: #0A5F7F;
            --primary-light: #4FB3D9;

            --primary-soft: #E8F6FB;

            --primary-gradient:
                linear-gradient(
                    145deg,
                    #0A3D52 0%,
                    #4FB3D9 100%
                );

            --primary-glow:
                rgba(16, 136, 186, 0.15);

            --bg-start: #F6F8FC;
            --bg-end: #EEF2F8;

            --text-primary: #0F172A;
            --text-secondary: #334155;
            --text-muted: #64748B;
            --text-light: #94A3B8;

            --white: #FFFFFF;

            --border: #E2E8F0;

            --success: #0B8A5E;
            --danger: #DC2626;

            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;

            --shadow-xl:
                0 30px 80px rgba(16, 136, 186, 0.15);

        }


        /* =====================================================
           RESET
        ===================================================== */

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        html,
        body {
            width: 100%;
            min-height: 100%;
        }


        body {

            min-height: 100vh;

            font-family:
                'Inter',
                -apple-system,
                BlinkMacSystemFont,
                sans-serif;

            background:

                radial-gradient(
                    circle at 10% 10%,
                    rgba(16, 136, 186, 0.06),
                    transparent 35%
                ),

                radial-gradient(
                    circle at 90% 90%,
                    rgba(13, 138, 191, 0.05),
                    transparent 35%
                ),

                var(--bg-start);

            color: var(--text-primary);

            overflow-x: hidden;

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 20px;

        }


        /* =====================================================
           CONTENEDOR
        ===================================================== */

        .login {

            width: 100%;

            max-width: 1200px;

            min-height: 640px;

            display: grid;

            grid-template-columns:
                1fr
                1.2fr;

            background: var(--white);

            border-radius: var(--radius-xl);

            box-shadow: var(--shadow-xl);

            overflow: hidden;

            position: relative;

            z-index: 1;

        }


        /* =====================================================
           PANEL IZQUIERDO
        ===================================================== */

        .promo {

            position: relative;

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 55px 45px;

            overflow: hidden;

            color: white;

            background:

                linear-gradient(
                    155deg,
                    #0A4D66 0%,
                    #0D6688 35%,
                    #0F7BA3 70%,
                    #4FB3D9 100%
                );

        }


        /* =====================================================
           DECORACIONES
        ===================================================== */

        .promo::before {

            content: "";

            position: absolute;

            width: 500px;
            height: 500px;

            right: -250px;
            top: -250px;

            border-radius: 50%;

            background:

                radial-gradient(
                    circle,
                    rgba(255,255,255,0.08) 0%,
                    rgba(255,255,255,0.02) 45%,
                    transparent 70%
                );

            animation:
                floatingCircle
                8s
                ease-in-out
                infinite;

        }


        .promo::after {

            content: "";

            position: absolute;

            width: 400px;
            height: 400px;

            left: -200px;
            bottom: -220px;

            border-radius: 50%;

            background:

                radial-gradient(
                    circle,
                    rgba(255,255,255,0.06),
                    transparent 70%
                );

            animation:
                floatingCircle
                10s
                ease-in-out
                infinite
                reverse;

        }


        @keyframes floatingCircle {

            0%,
            100% {
                transform: translate3d(0, 0, 0);
            }

            50% {
                transform: translate3d(0, 18px, 0);
            }

        }


        /* =====================================================
           CONTENIDO PROMO
        ===================================================== */

        .promo .z {

            position: relative;

            z-index: 5;

            width: 100%;

            max-width: 440px;

            text-align: center;

        }


        /* =====================================================
           LOGO
        ===================================================== */

        .promo .z img {

            display: block;

            width: 160px !important;

            max-width: 70%;

            height: auto;

            margin:
                0
                auto
                28px !important;

            object-fit: contain;

            filter:
                drop-shadow(
                    0 8px 24px
                    rgba(0,0,0,0.15)
                );

            animation:
                logoFloat
                5s
                ease-in-out
                infinite;

        }


        @keyframes logoFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }

        }


        /* =====================================================
           TITULO PROMO
        ===================================================== */

        .promo h2 {

            width: 100%;

            margin:
                0
                auto
                14px;

            color: white;

            font-size: 32px;

            line-height: 1.3;

            font-weight: 700;

            letter-spacing: -0.8px;

            text-align: center;

            text-shadow:
                0
                2px
                20px
                rgba(0,0,0,0.08);

        }


        .promo h2::after {

            content: "";

            display: block;

            width: 56px;
            height: 3px;

            margin:
                18px
                auto
                0;

            border-radius: 20px;

            background:

                linear-gradient(
                    90deg,
                    #BEE6F2,
                    #3E9EC4
                );

            box-shadow:
                0
                0
                20px
                rgba(190,230,242,0.3);

        }


        /* =====================================================
           TEXTO PROMO
        ===================================================== */

        .promo p {

            width: 100%;

            max-width: 390px;

            margin:
                0
                auto
                28px;

            color:
                rgba(255,255,255,0.85);

            font-size: 13px;

            line-height: 1.8;

            font-weight: 300;

            text-align: center;

        }


        /* =====================================================
           BENEFICIOS
        ===================================================== */

        .promo ul {

            width: 100%;

            display: grid;

            grid-template-columns: 1fr;

            gap: 10px;

            padding: 0;

            margin: 0;

            list-style: none;

        }


        .promo li {

            position: relative;

            display: flex;

            align-items: center;

            gap: 12px;

            min-height: 62px;

            padding:
                10px
                14px;

            color: white;

            background:
                rgba(255,255,255,0.10);

            border:
                1px solid
                rgba(255,255,255,0.08);

            border-radius: var(--radius-sm);

            backdrop-filter: blur(10px);

            box-shadow:
                0
                4px
                16px
                rgba(0,0,0,0.06);

            transition:
                transform .25s ease,
                box-shadow .25s ease,
                background .25s ease;

        }


        .promo li:hover {

            transform:
                translateY(-4px);

            background:
                rgba(255,255,255,0.18);

            box-shadow:
                0
                8px
                30px
                rgba(0,0,0,0.12);

        }


        .promo li i {

            width: 40px;
            height: 40px;

            flex: 0 0 40px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 11px;

            color: #BEE6F2;

            background:
                rgba(255,255,255,0.08);

            border:
                1px solid
                rgba(255,255,255,0.06);

            font-size: 15px;

        }


        .promo li span {

            color:
                rgba(255,255,255,0.92);

            font-size: 11px;

            line-height: 1.45;

            font-weight: 500;

        }


        /* =====================================================
           PANEL DERECHO
        ===================================================== */

        .panel {

            width: 100%;

            min-width: 0;

            display: flex;

            align-items: center;
            justify-content: center;

            padding:
                55px
                65px;

            background: white;

        }


        .box {

            width: 100%;

            max-width: 410px;

        }


        /* =====================================================
           ENCABEZADO
        ===================================================== */

        .box-header {

            text-align: center;

        }


        .welcome-icon {

            width: 52px;
            height: 52px;

            display: flex;

            align-items: center;
            justify-content: center;

            margin:
                0
                auto
                20px;

            border-radius:
                var(--radius-sm);

            color: var(--primary);

            background:
                var(--primary-soft);

            box-shadow:
                0
                4px
                16px
                rgba(13,138,191,0.12);

        }


        .welcome-icon i {

            font-size: 20px;

        }


        .box h1 {

            margin:
                0
                0
                8px;

            color:
                var(--text-primary);

            font-size: 28px;

            line-height: 1.25;

            font-weight: 700;

            letter-spacing: -0.7px;

        }


        .sub {

            margin:
                0
                0
                30px;

            color:
                var(--text-secondary);

            font-size: 14px;

            line-height: 1.7;

        }


        /* =====================================================
           ALERTAS
        ===================================================== */

        .alert {

            width: 100%;

            display: flex;

            align-items: flex-start;

            gap: 12px;

            padding:
                14px
                16px;

            margin-bottom: 20px;

            border-radius:
                var(--radius-sm);

            font-size: 13px;

            line-height: 1.5;

            border:
                1px solid
                transparent;

        }


        .alert.ok {

            color: var(--success);

            background: #ECFDF5;

            border-color: #BBF7D0;

        }


        .alert.error {

            color: var(--danger);

            background: #FEF2F2;

            border-color: #FECACA;

        }


        .alert i {

            font-size: 16px;

            margin-top: 1px;

        }


        /* =====================================================
           CAMPOS
        ===================================================== */

        .field {

            width: 100%;

            margin-bottom: 20px;

        }


        .field label {

            display: block;

            margin-bottom: 7px;

            color:
                var(--text-secondary);

            font-size: 13px;

            font-weight: 600;

        }


        /* =====================================================
           INPUT CON ICONO
        ===================================================== */

        .input-icon {

            position: relative;

            width: 100%;

        }


        .input-icon > i {

            position: absolute;

            left: 16px;

            top: 50%;

            width: 18px;

            transform:
                translateY(-50%);

            color:
                var(--text-light);

            font-size: 14px;

            text-align: center;

            pointer-events: none;

            z-index: 2;

            transition:
                color .2s ease;

        }


        .input-icon input {

            width: 100%;

            height: 52px;

            display: block;

            padding:
                0
                16px
                0
                48px !important;

            border:
                1px solid
                var(--border);

            border-radius:
                var(--radius-sm);

            outline: none;

            color:
                var(--text-primary);

            background:
                var(--bg-start);

            font-family: inherit;

            font-size: 14px;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;

            -webkit-appearance: none;

            appearance: none;

        }


        .input-icon input::placeholder {

            color:
                var(--text-light);

        }


        .input-icon input:hover {

            border-color:
                #CBD5E1;

        }


        .input-icon input:focus {

            background: white;

            border-color:
                var(--primary);

            box-shadow:
                0
                0
                0
                4px
                var(--primary-glow);

        }


        .input-icon:focus-within > i {

            color:
                var(--primary);

        }


        /* =====================================================
           CONTRASEÑA
        ===================================================== */

        .password-field {

            position: relative;

            width: 100%;

        }


        .password-field input {

            width: 100%;

            height: 52px;

            display: block;

            padding:
                0
                48px
                0
                48px !important;

            border:
                1px solid
                var(--border);

            border-radius:
                var(--radius-sm);

            outline: none;

            color:
                var(--text-primary);

            background:
                var(--bg-start);

            font-family: inherit;

            font-size: 14px;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;

        }


        .password-field input:focus {

            background: white;

            border-color:
                var(--primary);

            box-shadow:
                0
                0
                0
                4px
                var(--primary-glow);

        }


        .password-icon {

            position: absolute;

            left: 16px;

            top: 50%;

            width: 18px;

            transform:
                translateY(-50%);

            color:
                var(--text-light);

            font-size: 14px;

            text-align: center;

            pointer-events: none;

            z-index: 2;

            transition:
                color .2s ease;

        }


        .password-field:focus-within
        .password-icon {

            color:
                var(--primary);

        }


        /* =====================================================
           MOSTRAR CONTRASEÑA
        ===================================================== */

        .password-toggle {

            position: absolute;

            right: 10px;

            top: 50%;

            width: 32px;
            height: 32px;

            display: flex;

            align-items: center;
            justify-content: center;

            transform:
                translateY(-50%);

            padding: 0;

            border: 0;

            border-radius: 8px;

            background: transparent;

            color:
                var(--text-light);

            cursor: pointer;

            transition:
                color .2s ease,
                background .2s ease;

        }


        .password-toggle:hover {

            color:
                var(--primary);

            background:
                rgba(16,136,186,0.08);

        }


        .password-toggle:focus-visible {

            outline:
                2px solid
                var(--primary);

            outline-offset: 2px;

        }


        .password-toggle i {

            font-size: 14px;

        }


        /* =====================================================
           ERROR
        ===================================================== */

        .field input.error {

            border-color:
                var(--danger);

        }


        .field .error-text {

            display: block;

            margin-top: 5px;

            color:
                var(--danger);

            font-size: 12px;

        }


        /* =====================================================
           RECORDAR
        ===================================================== */

        .remember {

            width: 100%;

            display: flex;

            align-items: center;

            margin:
                4px
                0
                24px;

        }


        .remember label {

            display: flex;

            align-items: center;

            gap: 8px;

            color:
                var(--text-secondary);

            font-size: 13px;

            cursor: pointer;

        }


        .remember input[type="checkbox"] {

            width: 17px;

            height: 17px;

            margin: 0;

            accent-color:
                var(--primary);

            cursor: pointer;

        }


        /* =====================================================
           BOTÓN
        ===================================================== */

        .btn-primary {

            width: 100%;

            height: 54px;

            display: flex;

            align-items: center;
            justify-content: center;

            gap: 10px;

            border: 0;

            border-radius:
                var(--radius-sm);

            color: white;

            background:
                var(--primary-gradient);

            font-family: inherit;

            font-size: 15px;

            font-weight: 600;

            cursor: pointer;

            box-shadow:
                0
                8px
                24px
                rgba(16,136,186,0.25);

            transition:
                transform .2s ease,
                box-shadow .2s ease;

        }


        .btn-primary:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0
                12px
                32px
                rgba(16,136,186,0.32);

        }


        .btn-primary:active {

            transform:
                translateY(0)
                scale(.98);

        }


        .btn-primary i {

            font-size: 16px;

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 992px) {

            .login {

                grid-template-columns:
                    1fr
                    1fr;

                min-height: auto;

                border-radius:
                    var(--radius-lg);

            }


            .promo {

                padding:
                    40px
                    30px;

            }


            .panel {

                padding:
                    40px
                    35px
                    45px;

            }

        }


        @media (max-width: 768px) {

            body {

                padding: 0;

                background:
                    white;

            }


            .login {

                grid-template-columns: 1fr;

                min-height: 100vh;

                border-radius: 0;

                box-shadow: none;

            }


          .promo {
    display: none !important;
}


            .panel {

                padding:
                    30px
                    20px
                    40px;

                align-items:
                    flex-start;

            }


            .box {

                max-width: none;

            }


            .box h1 {

                font-size: 24px;

            }

        }


        @media (max-width: 420px) {

            .panel {

                padding:
                    24px
                    16px
                    32px;

            }


            .box h1 {

                font-size: 22px;

            }


            .input-icon input,
            .password-field input {

                height: 48px;

                font-size: 13px;

            }


            .btn-primary {

                height: 50px;

            }

        }

    </style>

</head>


<body>


<div class="login">


    <!-- =====================================================
         PANEL PROMOCIONAL
    ====================================================== -->

    <div class="promo">

        <div class="z">

            <img
                src="{{ asset('images/logosistema.png') }}"
                alt="Grupo Libérate"
            >


            <h2>
                Portal del<br>
                Paciente
            </h2>


            <p>
                Consulta tus próximas citas,
                tareas asignadas y tus estados de
                pago en un solo lugar.
            </p>


            <ul>

                <li>

                    <i class="fa-regular fa-calendar-check"></i>

                    <span>
                        Tus próximas citas
                    </span>

                </li>


                <li>

                    <i class="fa-solid fa-receipt"></i>

                    <span>
                        Tus pagos y recibos
                    </span>

                </li>

            </ul>

        </div>

    </div>


    <!-- =====================================================
         PANEL LOGIN
    ====================================================== -->

    <div class="panel">

        <div class="box">


            <!-- ENCABEZADO -->

            <div class="box-header">

                <div class="welcome-icon">

                    <i class="fa-solid fa-user"></i>

                </div>


                <h1>
                    Bienvenido al Portal 👋
                </h1>


                <p class="sub">
                    Ingresa con el correo registrado
                </p>

            </div>


            <!-- =================================================
                 AVISO
            ================================================== -->

            @if(session('aviso'))

                <div
                    class="alert ok"
                    role="alert"
                >

                    <i class="fa-solid fa-circle-info"></i>

                    <span>
                        {{ session('aviso') }}
                    </span>

                </div>

            @endif


            <!-- =================================================
                 ERRORES
            ================================================== -->

            @if($errors->any())

                <div
                    class="alert error"
                    role="alert"
                >

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span>
                        {{ $errors->first() }}
                    </span>

                </div>

            @endif


            <!-- =================================================
                 FORMULARIO
            ================================================== -->

            <form
                method="POST"
                action="{{ route('portal.login.attempt') }}"
            >

                @csrf


                <!-- CORREO -->

                <div class="field">

                    <label for="email">
                        Correo electrónico
                    </label>


                    <div class="input-icon">

                        <i
                            class="fa-regular fa-envelope"
                        ></i>


                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="ejemplo@correo.com"
                            autocomplete="email"
                            required
                            autofocus
                            class="@error('email') error @enderror"
                        >

                    </div>


                    @error('email')

                        <span class="error-text">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                <!-- CONTRASEÑA -->

                <div class="field">

                    <label for="password">
                        Contraseña
                    </label>


                    <div class="password-field">

                        <!-- Candado -->

                        <i
                            class="fa-solid fa-lock password-icon"
                        ></i>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                            class="@error('password') error @enderror"
                        >


                        <!-- Mostrar / ocultar -->

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                            aria-label="Mostrar contraseña"
                            title="Mostrar contraseña"
                        >

                            <i
                                class="fa-solid fa-eye"
                            ></i>

                        </button>

                    </div>


                    @error('password')

                        <span class="error-text">
                            {{ $message }}
                        </span>

                    @enderror

                </div>


                <!-- RECORDAR -->

                <div class="remember">

                    <label>

                        <input
                            type="checkbox"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                        >

                        Recordarme

                    </label>

                </div>


                <!-- BOTÓN -->

                <button
                    class="btn-primary"
                    type="submit"
                >

                    <i
                        class="fa-solid fa-right-to-bracket"
                    ></i>

                    Ingresar

                </button>

            </form>


        </div>

    </div>

</div>


<!-- =========================================================
     MOSTRAR / OCULTAR CONTRASEÑA
========================================================= -->

<script>

    function togglePassword() {

        const passwordInput =
            document.getElementById('password');

        const toggleButton =
            document.querySelector('.password-toggle');

        const icon =
            toggleButton.querySelector('i');


        if (passwordInput.type === 'password') {

            passwordInput.type = 'text';


            icon.classList.remove(
                'fa-eye'
            );

            icon.classList.add(
                'fa-eye-slash'
            );


            toggleButton.setAttribute(
                'aria-label',
                'Ocultar contraseña'
            );

            toggleButton.setAttribute(
                'title',
                'Ocultar contraseña'
            );

        } else {

            passwordInput.type = 'password';


            icon.classList.remove(
                'fa-eye-slash'
            );

            icon.classList.add(
                'fa-eye'
            );


            toggleButton.setAttribute(
                'aria-label',
                'Mostrar contraseña'
            );

            toggleButton.setAttribute(
                'title',
                'Mostrar contraseña'
            );

        }

    }

</script>


</body>

</html>
