<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes">

    <title>Iniciar sesión · Grupo Libérate</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>

        /* =====================================================
           ROOT VARIABLES
        ===================================================== */

        :root {
            --primary: #1f0670;
            --primary-dark: #14054d;
            --primary-light: #7B5DD9;
            --primary-gradient: linear-gradient(145deg, #140447 0%, #7B5DD9 100%);
            --primary-glow: rgba(91, 60, 196, 0.20);

            --secondary: #0D8ABF;
            --secondary-dark: #0A6E99;
            --secondary-light: #D6EEF7;

            --bg-start: #F6F8FC;
            --bg-end: #EEF2F8;

            --text-primary: #0F172A;
            --text-secondary: #334155;
            --text-muted: #64748B;
            --text-light: #94A3B8;

            --white: #FFFFFF;
            --border: #E2E8F0;

            --shadow-xl: 0 30px 80px rgba(91, 60, 196, 0.15);

            --radius-sm: 10px;
            --radius-md: 16px;
            --radius-lg: 24px;
            --radius-xl: 32px;

            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

            --success: #0B8A5E;
            --danger: #DC2626;
        }


        * {
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


        body {
            min-height: 100vh;

            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;

            background:
                radial-gradient(
                    circle at 10% 10%,
                    rgba(91, 60, 196, 0.06),
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
           CONTENEDOR PRINCIPAL
        ===================================================== */

        .login {
            width: 100%;
            max-width: 1200px;
            min-height: 640px;

            display: grid;
            grid-template-columns: 1fr 1.2fr;

            background: var(--white);

            border-radius: var(--radius-xl);

            box-shadow: var(--shadow-xl);

            overflow: hidden;

            position: relative;
            z-index: 1;
        }


        /* =====================================================
           PANEL IZQUIERDO - PROMO
        ===================================================== */

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
                    #4A2DA8 0%,
                    #5B3CC4 35%,
                    #6B4FD4 70%,
                    #7B5DD9 100%
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

            animation: floatingCircle 8s ease-in-out infinite;
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

            animation: floatingCircle 10s ease-in-out infinite reverse;
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
           PUNTOS FLOTANTES
        ===================================================== */

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

            background: rgba(255,255,255,0.3);

            animation: floatDot 5s ease-in-out infinite;
        }


        .dot-two {
            width: 16px;
            height: 16px;

            top: 28%;
            right: 15%;

            background: rgba(255,255,255,0.2);

            animation: floatDot 7s ease-in-out infinite reverse;
        }


        .dot-three {
            width: 8px;
            height: 8px;

            bottom: 20%;
            left: 18%;

            background: rgba(255,255,255,0.25);

            animation: floatDot 6s ease-in-out infinite;
        }


        @keyframes floatDot {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-14px);
            }
        }


        /* =====================================================
           CONTENIDO PROMOCIONAL
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

            margin: 0 auto 28px !important;

            object-fit: contain;

            filter:
                drop-shadow(0 8px 24px rgba(0,0,0,0.15));

            animation: logoFloat 5s ease-in-out infinite;
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
           TITULO
        ===================================================== */

        .promo h2 {
            display: block;

            width: 100%;

            margin: 0 auto 14px;

            color: #FFFFFF;

            font-size: 32px;

            line-height: 1.3;

            font-weight: 700;

            letter-spacing: -0.8px;

            text-align: center;

            text-shadow: 0 2px 20px rgba(0,0,0,0.08);
        }


        .promo h2 .highlight {
            display: block;

            color: #D4C4F7;

            font-weight: 700;

            text-shadow: 0 0 40px rgba(212, 196, 247, 0.2);
        }


        .promo h2::after {
            content: "";

            display: block;

            width: 56px;
            height: 3px;

            margin: 18px auto 0;

            border-radius: 20px;

            background:
                linear-gradient(
                    90deg,
                    #D4C4F7,
                    #A58BE0
                );

            box-shadow: 0 0 20px rgba(212, 196, 247, 0.3);
        }


        /* =====================================================
           DESCRIPCIÓN
        ===================================================== */

        .promo p {
            display: block;

            width: 100%;

            max-width: 390px;

            margin: 0 auto 28px;

            color: rgba(255,255,255,0.85);

            font-size: 13px;

            line-height: 1.8;

            font-weight: 300;

            text-align: center;
        }


        /* =====================================================
           LISTA DE BENEFICIOS
        ===================================================== */

        .promo ul {
            width: 100%;

            display: grid;

            grid-template-columns: 1fr 1fr;

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

            border: 1px solid rgba(255,255,255,0.08);

            border-radius: var(--radius-sm);

            backdrop-filter: blur(10px);

            box-shadow: 0 4px 16px rgba(0,0,0,0.06);

            transition:
                transform .25s ease,
                box-shadow .25s ease,
                background .25s ease;
        }


        .promo li:hover {
            transform: translateY(-4px);

            background: rgba(255,255,255,0.18);

            box-shadow: 0 8px 30px rgba(0,0,0,0.12);

            border-color: rgba(255,255,255,0.15);
        }


        /* =====================================================
           ICONOS DE BENEFICIOS
        ===================================================== */

        .promo li i {
            width: 40px;
            height: 40px;

            flex: 0 0 40px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 11px;

            color: #D4C4F7;

            background: rgba(255,255,255,0.08);

            border: 1px solid rgba(255,255,255,0.06);

            font-size: 15px;

            transition:
                transform .25s ease,
                color .25s ease,
                background .25s ease;
        }


        .promo li:hover i {
            transform: rotate(-5deg) scale(1.08);

            color: #FFFFFF;

            background: rgba(255,255,255,0.18);

            border-color: rgba(255,255,255,0.15);
        }


        /* =====================================================
           TEXTO DE BENEFICIOS
        ===================================================== */

        .promo li span {
            display: block;

            min-width: 0;

            color: rgba(255,255,255,0.92);

            font-size: 11px;

            line-height: 1.45;

            font-weight: 500;
        }


        /* =====================================================
           PANEL DERECHO - LOGIN
        ===================================================== */

        .panel {
            width: 100%;
            min-width: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 55px 65px;

            background: var(--white);
        }


        .box {
            width: 100%;

            max-width: 410px;
        }


        /* =====================================================
           ICONO DE BIENVENIDA
        ===================================================== */

        .welcome-icon {
            width: 52px;
            height: 52px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 20px;

            border-radius: var(--radius-sm);

            color: var(--primary);

            background: var(--secondary-light);

            box-shadow: 0 4px 16px rgba(13, 138, 191, 0.12);
        }


        .welcome-icon i {
            font-size: 20px;
        }


        /* =====================================================
           TITULO LOGIN
        ===================================================== */

        .box h1 {
            margin: 0 0 8px;

            color: var(--text-primary);

            font-size: 28px;

            line-height: 1.25;

            font-weight: 700;

            letter-spacing: -0.7px;
        }


        .sub {
            margin: 0 0 30px;

            color: var(--text-secondary);

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

            padding: 14px 16px;

            margin-bottom: 20px;

            border-radius: var(--radius-sm);

            font-size: 13px;

            line-height: 1.5;

            overflow-wrap: anywhere;

            border: 1px solid transparent;
        }


        .alert.ok {
            color: var(--success);

            background: #ECFDF5;

            border-color: #BBF7D0;
        }


        .alert.ok i {
            color: var(--success);
            font-size: 16px;
            margin-top: 1px;
        }


        .alert.error {
            color: var(--danger);

            background: #FEF2F2;

            border-color: #FECACA;
        }


        .alert.error i {
            color: var(--danger);
            font-size: 16px;
            margin-top: 1px;
        }


        /* =====================================================
           CAMPOS DE FORMULARIO
        ===================================================== */

        .field {
            width: 100%;

            margin-bottom: 20px;
        }


        .field label {
            display: block;

            margin-bottom: 7px;

            color: var(--text-secondary);

            font-size: 13px;

            font-weight: 600;
        }


        .field input {
            width: 100%;

            height: 52px;

            display: block;

            padding: 0 16px;

            border: 2px solid var(--border);

            border-radius: var(--radius-sm);

            outline: none;

            color: var(--text-primary);

            background: var(--bg-start);

            font-family: inherit;

            font-size: 14px;

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;

            -webkit-appearance: none;
            appearance: none;
        }


        .field input::placeholder {
            color: var(--text-light);
        }


        .field input:hover {
            border-color: #CBD5E1;
        }


        .field input:focus {
            background: var(--white);

            border-color: var(--primary);

            box-shadow: 0 0 0 4px var(--primary-glow);
        }


        /* =====================================================
           RECORDAR
        ===================================================== */

        .remember {
            width: 100%;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 15px;

            margin: 4px 0 24px;

            font-size: 13px;
        }


        .remember label {
            display: flex;
            align-items: center;

            gap: 8px;

            color: var(--text-secondary);

            cursor: pointer;

            white-space: nowrap;
        }


        .remember input[type="checkbox"] {
            width: 17px;
            height: 17px;

            margin: 0;

            accent-color: var(--primary);

            cursor: pointer;

            flex-shrink: 0;
        }


        .remember a {
            color: var(--primary) !important;

            font-weight: 600;

            text-decoration: none;

            transition: var(--transition);
        }


        .remember a:hover {
            color: var(--primary-dark) !important;
            text-decoration: underline;
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

            border-radius: var(--radius-sm);

            color: white;

            background: var(--primary-gradient);

            font-family: inherit;

            font-size: 15px;

            font-weight: 600;

            cursor: pointer;

            box-shadow: 0 8px 24px rgba(91, 60, 196, 0.25);

            transition:
                transform .2s ease,
                box-shadow .2s ease;

            -webkit-tap-highlight-color: transparent;
        }


        .btn-primary:hover {
            transform: translateY(-2px);

            box-shadow: 0 12px 36px rgba(91, 60, 196, 0.35);
        }


        .btn-primary:active {
            transform: translateY(0) scale(0.98);
        }


        .btn-primary i {
            font-size: 16px;
        }


        /* =====================================================
           LINKS INFERIORES
        ===================================================== */

        .bottom-links {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            line-height: 2;
            color: var(--text-muted) !important;
        }


        .bottom-links a {
            color: var(--primary) !important;

            font-weight: 600;

            text-decoration: none;

            transition: var(--transition);
        }


        .bottom-links a:hover {
            color: var(--primary-dark) !important;
            text-decoration: underline;
        }


        .bottom-links .separator {
            display: inline-block;
            width: 30px;
            height: 1px;
            background: var(--border);
            margin: 0 8px;
            vertical-align: middle;
        }


        /* =====================================================
           RESPONSIVE - TABLET (<= 992px)
        ===================================================== */

        @media (max-width: 992px) {

            .login {
                grid-template-columns: 1fr 1fr;
                min-height: auto;
                border-radius: var(--radius-lg);
            }


            .promo {
                padding: 40px 30px 38px;
            }


            .promo .z {
                max-width: 500px;
            }


            .promo .z img {
                width: 145px !important;
            }


            .promo h2 {
                font-size: 27px;
            }


            .promo p {
                max-width: 470px;
            }


            .promo ul {
                max-width: 500px;
                margin-left: auto;
                margin-right: auto;
            }


            .panel {
                padding: 40px 35px 45px;
            }


            .box {
                max-width: 440px;
            }
        }


        /* =====================================================
           RESPONSIVE - MÓVIL (<= 768px)
        ===================================================== */

        @media (max-width: 768px) {

            body {
                padding: 0;
                background: var(--white);
            }


            .login {
                grid-template-columns: 1fr;
                border-radius: 0;
                box-shadow: none;
                min-height: 100vh;
            }


            /* -------------------------------------------------
               PROMO MÓVIL
            ------------------------------------------------- */

            .promo {
                width: 100%;

                padding: 30px 20px 32px;

                min-height: auto;
            }


            .promo .z {
                width: 100%;

                max-width: 400px;
            }


            .promo .z img {
                width: 130px !important;

                max-width: 55%;

                margin-bottom: 18px !important;
            }


            .promo h2 {
                width: 100%;

                margin-bottom: 12px;

                font-size: 24px;

                line-height: 1.35;
            }


            .promo h2::after {
                width: 42px;

                height: 2px;

                margin-top: 14px;
            }


            .promo p {
                width: 100%;

                max-width: 350px;

                margin-bottom: 22px;

                font-size: 12px;

                line-height: 1.7;
            }


            /* -------------------------------------------------
               BENEFICIOS MÓVIL
            ------------------------------------------------- */

            .promo ul {
                width: 100%;

                grid-template-columns: 1fr 1fr;

                gap: 8px;
            }


            .promo li {
                width: 100%;

                min-height: 52px;

                padding: 8px 10px;

                gap: 10px;
            }


            .promo li i {
                width: 34px;
                height: 34px;

                flex-basis: 34px;

                border-radius: 9px;

                font-size: 13px;
            }


            .promo li span {
                font-size: 10px;

                line-height: 1.4;
            }


            /* -------------------------------------------------
               LOGIN MÓVIL
            ------------------------------------------------- */

            .panel {
                width: 100%;

                padding: 30px 20px 40px;

                align-items: flex-start;
            }


            .box {
                width: 100%;

                max-width: none;
            }


            .welcome-icon {
                width: 46px;
                height: 46px;

                margin-bottom: 16px;

                border-radius: var(--radius-sm);
            }


            .welcome-icon i {
                font-size: 17px;
            }


            .box h1 {
                font-size: 24px;
            }


            .sub {
                margin-bottom: 24px;

                font-size: 13px;
            }


            .field input {
                height: 50px;
                font-size: 14px;
            }


            .remember {
                align-items: flex-start;

                flex-direction: column;

                gap: 9px;
            }


            .remember a {
                margin-left: 25px;
            }


            .btn-primary {
                height: 52px;
                font-size: 15px;
            }


            .bottom-links {
                font-size: 12px;
            }
        }


        /* =====================================================
           RESPONSIVE - MÓVIL PEQUEÑO (<= 420px)
        ===================================================== */

        @media (max-width: 420px) {

            .promo {
                padding: 24px 16px 26px;
            }


            .promo .z img {
                width: 110px !important;
                max-width: 50%;
            }


            .promo h2 {
                font-size: 21px;
            }


            .promo p {
                font-size: 11px;
                max-width: 100%;
            }


            /* Beneficios en 1 columna en móviles muy pequeños */
            .promo ul {
                grid-template-columns: 1fr;
                gap: 6px;
            }


            .promo li {
                min-height: 48px;
                padding: 8px 12px;
                gap: 10px;
            }


            .promo li i {
                width: 32px;
                height: 32px;
                flex-basis: 32px;
                font-size: 12px;
            }


            .promo li span {
                font-size: 10px;
            }


            .panel {
                padding: 24px 16px 32px;
            }


            .box h1 {
                font-size: 22px;
            }


            .field input {
                height: 48px;
                font-size: 13px;
                padding: 0 14px;
            }


            .btn-primary {
                height: 50px;
                font-size: 14px;
            }


            .bottom-links {
                font-size: 11px;
            }
        }


        /* =====================================================
           RESPONSIVE - MÓVIL MUY PEQUEÑO (<= 350px)
        ===================================================== */

        @media (max-width: 350px) {

            .promo {
                padding: 18px 12px 20px;
            }


            .promo .z img {
                width: 90px !important;
            }


            .promo h2 {
                font-size: 18px;
            }


            .promo p {
                font-size: 10px;
            }


            .promo li {
                min-height: 42px;
                padding: 6px 8px;
                gap: 8px;
            }


            .promo li i {
                width: 28px;
                height: 28px;
                flex-basis: 28px;
                font-size: 10px;
                border-radius: 7px;
            }


            .promo li span {
                font-size: 9px;
            }


            .panel {
                padding: 18px 12px 28px;
            }


            .box h1 {
                font-size: 20px;
            }


            .sub {
                font-size: 12px;
            }


            .welcome-icon {
                width: 40px;
                height: 40px;
            }


            .welcome-icon i {
                font-size: 15px;
            }


            .field input {
                height: 44px;
                font-size: 12px;
                padding: 0 12px;
            }


            .btn-primary {
                height: 46px;
                font-size: 13px;
            }


            .bottom-links {
                font-size: 10px;
            }
        }


        /* =====================================================
           RESPONSIVE - PANTALLAS ALTAS
        ===================================================== */

        @media (min-height: 900px) {

            .login {
                min-height: 720px;
            }


            .promo {
                padding: 60px 50px;
            }


            .panel {
                padding: 60px 70px;
            }
        }


        /* =====================================================
           RESPONSIVE - PANTALLAS ANCHAS
        ===================================================== */

        @media (min-width: 1400px) {

            .login {
                max-width: 1300px;
            }


            .promo {
                padding: 65px 55px;
            }


            .promo .z img {
                width: 180px !important;
            }


            .promo h2 {
                font-size: 36px;
            }


            .panel {
                padding: 65px 75px;
            }


            .box h1 {
                font-size: 32px;
            }
        }


        /* =====================================================
           ORIENTACIÓN - PAISAJE EN MÓVIL
        ===================================================== */

        @media (max-width: 768px) and (orientation: landscape) {

            .login {
                min-height: 100vh;
            }


            .promo {
                padding: 20px 30px;
            }


            .promo .z img {
                width: 100px !important;
                margin-bottom: 12px !important;
            }


            .promo h2 {
                font-size: 20px;
            }


            .promo p {
                font-size: 11px;
                margin-bottom: 14px;
            }


            .promo ul {
                grid-template-columns: 1fr 1fr 1fr;
                gap: 6px;
            }


            .promo li {
                min-height: 40px;
                padding: 6px 10px;
            }


            .promo li i {
                width: 28px;
                height: 28px;
                flex-basis: 28px;
                font-size: 11px;
            }


            .promo li span {
                font-size: 9px;
            }


            .panel {
                padding: 20px 25px 30px;
            }


            .box h1 {
                font-size: 22px;
            }


            .field input {
                height: 44px;
            }


            .btn-primary {
                height: 46px;
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

        <!-- Elementos decorativos -->
        <span class="floating-dot dot-one"></span>
        <span class="floating-dot dot-two"></span>
        <span class="floating-dot dot-three"></span>


        <div class="z">

            <!-- =================================================
                 LOGO
            ================================================== -->

            <img src="{{ asset('images/logo-completo.png') }}" alt="Grupo Libérate" style="width:150px;margin-bottom:20px">


            <!-- =================================================
                 TITULO
            ================================================== -->

            <h2>
                Un espacio para
                <span class="highlight">
                    volver a encontrarte
                </span>
            </h2>


            <p>
                En Grupo Libérate acompañamos tu proceso de
                bienestar emocional con atención psicológica
                profesional, cercana y humana.
            </p>


            <!-- =================================================
                 BENEFICIOS
            ================================================== -->

            <ul>

                <li>

                    <i class="fa-solid fa-brain"></i>

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

<center>

            <div class="welcome-icon">
                <i class="fa-solid fa-hand-holding-heart"></i>
            </div>
</center>

<center>

            <h1>
                Bienvenido 👋
            </h1>


            <p class="sub">
                Ingresa tus credenciales para acceder
                al sistema de Grupo Libérate.
            </p>

</center>

            <!-- =================================================
                 MENSAJE OK
            ================================================== -->

            @if(session('ok'))

                <div class="alert ok">

                    <i class="fa-solid fa-circle-check"></i>

                    <span>
                        {{ session('ok') }}
                    </span>

                </div>

            @endif


            <!-- =================================================
                 ERRORES
            ================================================== -->

            @if($errors->any())

                <div class="alert error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span>
                        {{ $errors->first() }}
                    </span>

                </div>

            @endif


            <!-- =================================================
                 FORMULARIO
            ================================================== -->

            <form method="POST" action="{{ route('login') }}">

                @csrf


                <div class="field">

                    <label for="email">
                        Correo electrónico
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="tucorreo@grupoliberate.com"
                        required
                        autofocus
                    >

                </div>


                <div class="field">

                    <label for="password">
                        Contraseña
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        required
                    >

                </div>


                <div class="remember">

                    <label>

                        <input
                            type="checkbox"
                            name="remember"
                        >

                        Recordarme

                    </label>


                    <a
                        href="{{ route('password.request') }}"
                    >
                        ¿Olvidaste tu contraseña?
                    </a>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="fa-solid fa-right-to-bracket"></i>

                    Iniciar sesión

                </button>

            </form>


            <!-- =================================================
                 LINKS
            ================================================== -->

            <div class="bottom-links">

              

                <div>
                    <span class="separator"></span>
                </div>

                <div>
                    ¿Eres paciente?

                    <a
                        href="{{ route('portal.login') }}"
                    >
                        Ingresa al Portal del Paciente →
                    </a>
                </div>

            </div>


        </div>

    </div>

</div>

</body>
</html>