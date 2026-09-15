<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes"
    >

    <title>Iniciar sesión · Grupo Libérate</title>

    <!-- =====================================================
         FUENTES
    ====================================================== -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- =====================================================
         FONT AWESOME
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <!-- =====================================================
         CSS PRINCIPAL
    ====================================================== -->

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">


    <style>

        /* =====================================================
           VARIABLES
        ====================================================== */

        :root {

            --primary: #1088BA;
            --primary-dark: #0A5F7F;
            --primary-light: #4FB3D9;

            --primary-gradient:
                linear-gradient(
                    145deg,
                    #0A5F7F 0%,
                    #1088BA 55%,
                    #4FB3D9 100%
                );

            --primary-glow:
                rgba(16, 136, 186, 0.14);

            --secondary-light: #E8F6FB;

            --bg-start: #F6F8FC;
            --bg-end: #EEF2F8;

            --text-primary: #0F172A;
            --text-secondary: #334155;
            --text-muted: #64748B;
            --text-light: #94A3B8;

            --white: #FFFFFF;

            --border: #E2E8F0;
            --border-hover: #CBD5E1;

            --success: #0B8A5E;
            --danger: #DC2626;

            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;

            --shadow-xl:
                0 30px 80px rgba(16, 136, 186, 0.15);

            --transition:
                all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }


        /* =====================================================
           RESET
        ====================================================== */

        *,
        *::before,
        *::after {

            box-sizing: border-box;

            margin: 0;
            padding: 0;
        }


        html,
        body {

            width: 100%;
            min-height: 100%;

            margin: 0;
        }


        /* =====================================================
           BODY
        ====================================================== */

        body {

            min-height: 100vh;

            font-family:
                'Inter',
                -apple-system,
                BlinkMacSystemFont,
                sans-serif;

            color: var(--text-primary);

            background:

                radial-gradient(
                    circle at 10% 10%,
                    rgba(16, 136, 186, 0.06),
                    transparent 35%
                ),

                radial-gradient(
                    circle at 90% 90%,
                    rgba(79, 179, 217, 0.06),
                    transparent 35%
                ),

                linear-gradient(
                    135deg,
                    var(--bg-start),
                    var(--bg-end)
                );

            overflow-x: hidden;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 20px;
        }


        /* =====================================================
           CONTENEDOR PRINCIPAL
        ====================================================== */

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
        ====================================================== */

        .promo {

            position: relative;

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 55px 45px;

            overflow: hidden;

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
        ====================================================== */

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
                floatingCircle 8s ease-in-out infinite;

            pointer-events: none;
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
                floatingCircle 10s ease-in-out infinite reverse;

            pointer-events: none;
        }


        @keyframes floatingCircle {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0);
            }

            50% {

                transform:
                    translate3d(0, 18px, 0);
            }
        }


        /* =====================================================
           PUNTOS
        ====================================================== */

        .floating-dot {

            position: absolute;

            border-radius: 50%;

            pointer-events: none;

            opacity: 0.5;
        }


        .dot-one {

            width: 10px;
            height: 10px;

            top: 12%;
            left: 12%;

            background:
                rgba(255,255,255,0.3);

            animation:
                floatDot 5s ease-in-out infinite;
        }


        .dot-two {

            width: 16px;
            height: 16px;

            top: 28%;
            right: 15%;

            background:
                rgba(255,255,255,0.2);

            animation:
                floatDot 7s ease-in-out infinite reverse;
        }


        .dot-three {

            width: 8px;
            height: 8px;

            bottom: 20%;
            left: 18%;

            background:
                rgba(255,255,255,0.25);

            animation:
                floatDot 6s ease-in-out infinite;
        }


        @keyframes floatDot {

            0%,
            100% {

                transform:
                    translateY(0);
            }

            50% {

                transform:
                    translateY(-14px);
            }
        }


        /* =====================================================
           CONTENIDO PROMO
        ====================================================== */

        .promo .z {

            position: relative;

            z-index: 5;

            width: 100%;

            max-width: 440px;

            text-align: center;
        }


        /* =====================================================
           LOGO
        ====================================================== */

        .promo .z img {

            display: block;

            width: 170px !important;

            max-width: 70%;

            height: auto;

            margin:
                0 auto 28px !important;

            object-fit: contain;

            filter:
                drop-shadow(
                    0 8px 24px
                    rgba(0,0,0,0.15)
                );

            animation:
                logoFloat 5s ease-in-out infinite;
        }


        @keyframes logoFloat {

            0%,
            100% {

                transform:
                    translateY(0);
            }

            50% {

                transform:
                    translateY(-5px);
            }
        }


        /* =====================================================
           TITULO PROMO
        ====================================================== */

        .promo h2 {

            display: block;

            width: 100%;

            margin:
                0 auto 14px;

            color: #FFFFFF;

            font-size: 32px;

            line-height: 1.3;

            font-weight: 700;

            letter-spacing: -0.8px;

            text-align: center;

            text-shadow:
                0 2px 20px
                rgba(0,0,0,0.08);
        }


        .promo h2 .highlight {

            display: block;

            color: #BEE6F2;

            font-weight: 700;

            text-shadow:
                0 0 30px
                rgba(190,230,242,0.18);
        }


        /* Línea decorativa */

        .promo h2::after {

            content: "";

            display: block;

            width: 56px;
            height: 3px;

            margin:
                18px auto 0;

            border-radius: 20px;

            background:

                linear-gradient(
                    90deg,
                    #BEE6F2,
                    #3E9EC4
                );

            box-shadow:
                0 0 20px
                rgba(190,230,242,0.25);
        }


        /* =====================================================
           DESCRIPCIÓN
        ====================================================== */

        .promo p {

            display: block;

            width: 100%;

            max-width: 390px;

            margin:
                0 auto 28px;

            color:
                rgba(255,255,255,0.86);

            font-size: 13px;

            line-height: 1.8;

            font-weight: 300;

            text-align: center;
        }


        /* =====================================================
           BENEFICIOS
        ====================================================== */

        .promo ul {

            width: 100%;

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 10px;

            padding: 0;
            margin: 0;

            list-style: none;

            text-align: left;
        }


        .promo li {

            position: relative;

            min-width: 0;

            display: flex;

            align-items: center;

            gap: 12px;

            min-height: 62px;

            padding: 10px 14px;

            color: #FFFFFF;

            background:
                rgba(255,255,255,0.10);

            border:
                1px solid
                rgba(255,255,255,0.08);

            border-radius:
                var(--radius-sm);

            backdrop-filter:
                blur(10px);

            box-shadow:
                0 4px 16px
                rgba(0,0,0,0.06);

            transition:
                transform .25s ease,
                box-shadow .25s ease,
                background .25s ease;
        }


        .promo li:hover {

            transform:
                translateY(-3px);

            background:
                rgba(255,255,255,0.16);

            box-shadow:
                0 8px 30px
                rgba(0,0,0,0.12);

            border-color:
                rgba(255,255,255,0.15);
        }


        /* =====================================================
           ICONOS BENEFICIOS
        ====================================================== */

        .promo li i {

            width: 40px;
            height: 40px;

            flex:
                0 0 40px;

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

            transition:
                transform .25s ease,
                color .25s ease,
                background .25s ease;
        }


        .promo li:hover i {

            transform:
                scale(1.06);

            color: #FFFFFF;

            background:
                rgba(255,255,255,0.18);

            border-color:
                rgba(255,255,255,0.15);
        }


        .promo li span {

            display: block;

            min-width: 0;

            color:
                rgba(255,255,255,0.92);

            font-size: 11px;

            line-height: 1.45;

            font-weight: 500;
        }


        /* =====================================================
           PANEL DERECHO
        ====================================================== */

        .panel {

            width: 100%;

            min-width: 0;

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 55px 65px;

            background:
                var(--white);
        }


        .box {

            width: 100%;

            max-width: 410px;
        }


        /* =====================================================
           CABECERA LOGIN
        ====================================================== */

        .box-header {

            text-align: center;
        }


        /* =====================================================
           ICONO BIENVENIDA
        ====================================================== */

        .welcome-icon {

            width: 52px;
            height: 52px;

            display: flex;

            align-items: center;
            justify-content: center;

            margin:
                0 auto 20px;

            border-radius:
                var(--radius-sm);

            color:
                var(--primary);

            background:
                var(--secondary-light);

            border:
                1px solid
                rgba(16,136,186,0.08);

            box-shadow:
                0 6px 20px
                rgba(13,138,191,0.10);

            transition:
                transform .25s ease,
                box-shadow .25s ease;
        }


        .welcome-icon:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 10px 26px
                rgba(13,138,191,0.14);
        }


        .welcome-icon i {

            font-size: 20px;
        }


        /* =====================================================
           TITULO LOGIN
        ====================================================== */

        .box h1 {

            margin:
                0 0 8px;

            color:
                var(--text-primary);

            font-size: 28px;

            line-height: 1.25;

            font-weight: 700;

            letter-spacing: -0.7px;
        }


        .sub {

            margin:
                0 0 30px;

            color:
                var(--text-secondary);

            font-size: 14px;

            line-height: 1.7;
        }


        /* =====================================================
           ALERTAS
        ====================================================== */

        .alert {

            width: 100%;

            display: flex;

            align-items: flex-start;

            gap: 12px;

            padding: 14px 16px;

            margin-bottom: 20px;

            border-radius:
                var(--radius-sm);

            font-size: 13px;

            line-height: 1.5;

            overflow-wrap: anywhere;

            border:
                1px solid transparent;
        }


        .alert.ok {

            color:
                var(--success);

            background:
                #ECFDF5;

            border-color:
                #BBF7D0;
        }


        .alert.ok i {

            color:
                var(--success);

            font-size: 16px;

            margin-top: 1px;
        }


        .alert.error {

            color:
                var(--danger);

            background:
                #FEF2F2;

            border-color:
                #FECACA;
        }


        .alert.error i {

            color:
                var(--danger);

            font-size: 16px;

            margin-top: 1px;
        }


        /* =====================================================
           CAMPOS
        ====================================================== */

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
        ====================================================== */

        /* =====================================================
   CAMPO CON ICONO
===================================================== */

.input-icon {
    position: relative;
    width: 100%;
}

.input-icon > i {
    position: absolute;
    left: 16px;
    top: 50%;

    transform: translateY(-50%);

    width: 18px;

    color: #94A3B8;
    font-size: 14px;

    text-align: center;

    pointer-events: none;

    z-index: 2;

    transition: color .2s ease;
}

.input-icon input {
    width: 100%;

    padding-left: 48px !important;
    padding-right: 16px;
}

.input-icon:focus-within > i {
    color: var(--primary);
}


        /* =====================================================
           INPUTS
        ====================================================== */

        .field input {

            width: 100%;

            height: 52px;

            display: block;

            padding:
                0 16px;

            border:
                1px solid
                var(--border);

            border-radius:
                12px;

            outline: none;

            color:
                var(--text-primary);

            background:
                #F8FAFC;

            font-family: inherit;

            font-size: 14px;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }


        .field input::placeholder {

            color:
                var(--text-light);
        }


        .field input:hover {

            border-color:
                var(--border-hover);
        }


        .field input:focus {

            background:
                var(--white);

            border-color:
                var(--primary);

            box-shadow:
                0 0 0 4px
                var(--primary-glow);
        }


        /* =====================================================
           CONTRASEÑA
        ====================================================== */

        .password-field {

            position: relative;

            width: 100%;
        }


        .password-field input {

            padding-left: 45px;

            padding-right: 52px;
        }


        .password-icon {

            position: absolute;

            left: 16px;
            top: 50%;

            transform:
                translateY(-50%);

            color:
                var(--text-light);

            font-size: 14px;

            pointer-events: none;

            z-index: 2;

            transition:
                color .2s ease;
        }


        .password-field:focus-within .password-icon {

            color:
                var(--primary);
        }


        /* =====================================================
           BOTON MOSTRAR CONTRASEÑA
        ====================================================== */

        .password-toggle {

            position: absolute;

            right: 10px;
            top: 50%;

            width: 34px;
            height: 34px;

            display: flex;

            align-items: center;
            justify-content: center;

            transform:
                translateY(-50%);

            padding: 0;

            border: 0;

            border-radius: 8px;

            background:
                transparent;

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

            outline-offset:
                2px;
        }


        .password-toggle i {

            font-size: 14px;
        }


        /* =====================================================
           RECORDAR
        ====================================================== */

        .remember {

            width: 100%;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            margin:
                4px 0 24px;

            font-size: 13px;
        }


        .remember label {

            display: flex;

            align-items: center;

            gap: 8px;

            color:
                var(--text-secondary);

            cursor: pointer;

            white-space: nowrap;
        }


        .remember input[type="checkbox"] {

            width: 17px;
            height: 17px;

            margin: 0;

            accent-color:
                var(--primary);

            cursor: pointer;

            flex-shrink: 0;
        }


        .remember a {

            color:
                var(--primary) !important;

            font-weight: 600;

            text-decoration: none;

            transition:
                var(--transition);
        }


        .remember a:hover {

            color:
                var(--primary-dark) !important;

            text-decoration:
                underline;
        }


        .remember a:focus-visible {

            outline:
                2px solid
                var(--primary);

            outline-offset:
                3px;

            border-radius: 3px;
        }


        /* =====================================================
           BOTON LOGIN
        ====================================================== */

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

            color:
                #FFFFFF;

            background:
                var(--primary-gradient);

            font-family: inherit;

            font-size: 15px;

            font-weight: 600;

            cursor: pointer;

            box-shadow:
                0 8px 24px
                rgba(16,136,186,0.24);

            transition:
                transform .2s ease,
                box-shadow .2s ease,
                filter .2s ease;

            -webkit-tap-highlight-color:
                transparent;
        }


        .btn-primary:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 12px 32px
                rgba(16,136,186,0.32);

            filter:
                brightness(1.02);
        }


        .btn-primary:active {

            transform:
                translateY(0)
                scale(0.985);
        }


        .btn-primary:focus-visible {

            outline:
                3px solid
                rgba(16,136,186,0.22);

            outline-offset:
                3px;
        }


        .btn-primary i {

            font-size: 16px;
        }


        /* =====================================================
           LINKS INFERIORES
        ====================================================== */

        .bottom-links {

            position: relative;

            text-align: center;

            margin-top: 30px;

            padding-top: 24px;

            font-size: 12px;

            line-height: 1.6;

            color:
                var(--text-muted);
        }


        .bottom-links::before {

            content: "";

            position: absolute;

            top: 0;
            left: 50%;

            width: 32px;
            height: 1px;

            transform:
                translateX(-50%);

            background:
                var(--border);
        }


        .bottom-links a {

            display: inline-block;

            margin-left: 4px;

            color:
                var(--primary) !important;

            font-weight: 600;

            text-decoration: none;

            transition:
                var(--transition);
        }


        .bottom-links a:hover {

            color:
                var(--primary-dark) !important;

            text-decoration:
                underline;
        }


        .bottom-links a:focus-visible {

            outline:
                2px solid
                var(--primary);

            outline-offset:
                3px;

            border-radius: 3px;
        }


        /* =====================================================
           RESPONSIVE - TABLET
        ====================================================== */

        @media (max-width: 992px) {

            .login {

                grid-template-columns:
                    1fr 1fr;

                min-height:
                    auto;

                border-radius:
                    var(--radius-lg);
            }


            .promo {

                padding:
                    40px 30px 38px;
            }


            .promo .z {

                max-width:
                    500px;
            }


            .promo .z img {

                width:
                    145px !important;
            }


            .promo h2 {

                font-size:
                    27px;
            }


            .promo p {

                max-width:
                    470px;
            }


            .promo ul {

                max-width:
                    500px;

                margin-left:
                    auto;

                margin-right:
                    auto;
            }


            .panel {

                padding:
                    40px 35px 45px;
            }


            .box {

                max-width:
                    440px;
            }
        }


        /* =====================================================
           RESPONSIVE - MOVIL
        ====================================================== */

        @media (max-width: 768px) {

            body {

                padding:
                    0;

                background:
                    var(--white);
            }


            .login {

                grid-template-columns:
                    1fr;

                border-radius:
                    0;

                box-shadow:
                    none;

                min-height:
                    100vh;
            }


            /* -------------------------------------------------
               PANEL PROMOCIONAL OCULTO
            ------------------------------------------------- */

            .promo {

                display:
                    none !important;
            }


            /* -------------------------------------------------
               LOGIN
            ------------------------------------------------- */

            .panel {

                width:
                    100%;

                padding:
                    30px 20px 40px;

                align-items:
                    flex-start;
            }


            .box {

                width:
                    100%;

                max-width:
                    none;
            }


            .welcome-icon {

                width:
                    46px;

                height:
                    46px;

                margin-bottom:
                    16px;
            }


            .welcome-icon i {

                font-size:
                    17px;
            }


            .box h1 {

                font-size:
                    24px;
            }


            .sub {

                margin-bottom:
                    24px;

                font-size:
                    13px;
            }


            .field input {

                height:
                    50px;

                font-size:
                    14px;
            }


            .remember {

                align-items:
                    flex-start;

                flex-direction:
                    column;

                gap:
                    9px;
            }


            .remember a {

                margin-left:
                    25px;
            }


            .btn-primary {

                height:
                    52px;

                font-size:
                    15px;
            }


            .bottom-links {

                font-size:
                    12px;
            }
        }


        /* =====================================================
           RESPONSIVE - MOVIL PEQUEÑO
        ====================================================== */

        @media (max-width: 420px) {

            .panel {

                padding:
                    24px 16px 32px;
            }


            .box h1 {

                font-size:
                    22px;
            }


            .field input {

                height:
                    48px;

                font-size:
                    13px;

                padding-left:
                    43px;
            }


            .password-field input {

                padding-left:
                    43px;

                padding-right:
                    50px;
            }


            .input-icon > i,
            .password-icon {

                left:
                    14px;

                font-size:
                    13px;
            }


            .password-toggle {

                right:
                    8px;
            }


            .btn-primary {

                height:
                    50px;

                font-size:
                    14px;
            }


            .bottom-links {

                font-size:
                    11px;
            }
        }


        /* =====================================================
           RESPONSIVE - MUY PEQUEÑO
        ====================================================== */

        @media (max-width: 350px) {

            .panel {

                padding:
                    18px 12px 28px;
            }


            .box h1 {

                font-size:
                    20px;
            }


            .sub {

                font-size:
                    12px;
            }


            .welcome-icon {

                width:
                    40px;

                height:
                    40px;
            }


            .welcome-icon i {

                font-size:
                    15px;
            }


            .field input {

                height:
                    44px;

                font-size:
                    12px;

                padding-left:
                    40px;
            }


            .password-field input {

                padding-left:
                    40px;

                padding-right:
                    46px;
            }


            .input-icon > i,
            .password-icon {

                left:
                    12px;

                font-size:
                    12px;
            }


            .password-toggle {

                right:
                    5px;

                width:
                    30px;

                height:
                    30px;
            }


            .password-toggle i {

                font-size:
                    12px;
            }


            .btn-primary {

                height:
                    46px;

                font-size:
                    13px;
            }


            .bottom-links {

                font-size:
                    10px;
            }
        }


        /* =====================================================
           PANTALLAS ALTAS
        ====================================================== */

        @media (min-height: 900px) {

            .login {

                min-height:
                    720px;
            }


            .promo {

                padding:
                    60px 50px;
            }


            .panel {

                padding:
                    60px 70px;
            }
        }


        /* =====================================================
           PANTALLAS ANCHAS
        ====================================================== */

        @media (min-width: 1400px) {

            .login {

                max-width:
                    1300px;
            }


            .promo {

                padding:
                    65px 55px;
            }


            .promo .z img {

                width:
                    180px !important;
            }


            .promo h2 {

                font-size:
                    36px;
            }


            .panel {

                padding:
                    65px 75px;
            }


            .box h1 {

                font-size:
                    32px;
            }
        }


        /* =====================================================
           LANDSCAPE EN MOVIL
        ====================================================== */

        @media (max-width: 768px) and (orientation: landscape) {

            .login {

                min-height:
                    100vh;
            }


            .panel {

                padding:
                    20px 25px 30px;
            }


            .box h1 {

                font-size:
                    22px;
            }


            .field input {

                height:
                    44px;
            }


            .btn-primary {

                height:
                    46px;
            }
        }


        /* =====================================================
           REDUCIR ANIMACIONES
        ====================================================== */

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {

                animation-duration:
                    0.01ms !important;

                animation-iteration-count:
                    1 !important;

                transition-duration:
                    0.01ms !important;
            }
        }

    </style>

</head>


<body>


<div class="login">


    <!-- =====================================================
         PANEL GRUPO LIBÉRATE
    ====================================================== -->

    <div class="promo">


        <!-- Decoraciones -->

        <span
            class="floating-dot dot-one"
            aria-hidden="true"
        ></span>

        <span
            class="floating-dot dot-two"
            aria-hidden="true"
        ></span>

        <span
            class="floating-dot dot-three"
            aria-hidden="true"
        ></span>


        <div class="z">


            <!-- Logo -->

            <img
                src="{{ asset('images/logosistema.jpg') }}"
                alt="Grupo Libérate"
            >


            <!-- Título -->

            <h2>

                Un espacio para

                <span class="highlight">
                    volver a encontrarte
                </span>

            </h2>


            <!-- Descripción -->

            <p>

                En Grupo Libérate acompañamos tu proceso de
                bienestar emocional con atención psicológica
                profesional, cercana y humana.

            </p>


            <!-- Beneficios -->

            <ul>

                <li>

                    <i
                        class="fa-solid fa-brain"
                        aria-hidden="true"
                    ></i>

                    <span>
                        Acompañamiento psicológico
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


            <!-- =================================================
                 CABECERA
            ================================================== -->

            <div class="box-header">


                <div
                    class="welcome-icon"
                    aria-hidden="true"
                >

                    <i class="fa-solid fa-brain"></i>

                </div>


                <h1>
                    Bienvenidos 🪷
                </h1>


                <p class="sub">
                    Ingresa tus credenciales para acceder
                    al sistema
                </p>


            </div>



            <!-- =================================================
                 MENSAJE OK
            ================================================== -->

            @if(session('ok'))

                <div
                    class="alert ok"
                    role="status"
                    aria-live="polite"
                >

                    <i
                        class="fa-solid fa-circle-check"
                        aria-hidden="true"
                    ></i>

                    <span>
                        {{ session('ok') }}
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
                    aria-live="assertive"
                >

                    <i
                        class="fa-solid fa-circle-exclamation"
                        aria-hidden="true"
                    ></i>

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
                action="{{ route('login') }}"
            >

                @csrf


                <!-- =============================================
                     CORREO
                ============================================== -->

                <div class="field">


                    <label for="email">
                        Correo electrónico
                    </label>


                    <div class="input-icon">


                        <i
                            class="fa-solid fa-envelope"
                            aria-hidden="true"
                        ></i>


                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="tucorreo@grupoliberate.com"
                            autocomplete="email"
                            required
                            autofocus
                        >


                    </div>


                </div>



                <!-- =============================================
                     CONTRASEÑA
                ============================================== -->

                <div class="field">


                    <label for="password">
                        Contraseña
                    </label>


                    <div class="password-field">


                        <!-- Icono candado -->

                        <i
                            class="fa-solid fa-lock password-icon"
                            aria-hidden="true"
                        ></i>


                        <!-- Input -->

                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            required
                        >


                        <!-- Mostrar contraseña -->

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                            aria-label="Mostrar contraseña"
                            title="Mostrar contraseña"
                        >

                            <i
                                class="fa-solid fa-eye"
                                aria-hidden="true"
                            ></i>

                        </button>


                    </div>


                </div>



                <!-- =============================================
                     RECORDAR
                ============================================== -->

                <div class="remember">


                    <label for="remember">


                        <input
                            id="remember"
                            type="checkbox"
                            name="remember"
                        >


                        <span>
                            Recordarme
                        </span>


                    </label>



                    <a
                        href="{{ route('password.request') }}"
                    >
                        ¿Olvidaste tu contraseña?
                    </a>


                </div>



                <!-- =============================================
                     BOTÓN
                ============================================== -->

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i
                        class="fa-solid fa-right-to-bracket"
                        aria-hidden="true"
                    ></i>

                    <span>
                        Iniciar sesión
                    </span>

                </button>


            </form>



            <!-- =================================================
                 PORTAL PACIENTE
            ================================================== -->

            <div class="bottom-links">

                <span>
                    ¿Eres paciente?
                </span>


                <a
                    href="{{ route('portal.login') }}"
                >
                    Ingresa al Portal del Paciente →
                </a>

            </div>


        </div>

    </div>


</div>



<!-- =========================================================
     JAVASCRIPT
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


            icon.classList.remove('fa-eye');

            icon.classList.add('fa-eye-slash');


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


            icon.classList.remove('fa-eye-slash');

            icon.classList.add('fa-eye');


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