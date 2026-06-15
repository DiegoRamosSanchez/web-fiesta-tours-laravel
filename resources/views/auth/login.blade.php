<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Fiesta Tours</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* ── FONDO DIAGONAL ── */
        .bg-image {
            position: fixed;
            inset: 0;
            z-index: 0;
        }
        .bg-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .bg-diagonal {
            position: fixed;
            inset: 0;
            z-index: 1;
        }
        .bg-diagonal svg {
            width: 100%;
            height: 100%;
        }

        /* ── TARJETA ── */
        .wrapper {
            position: relative;
            z-index: 2;
            display: flex;
            width: 820px;
            min-height: 520px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0,0,0,.2);
        }

        /* ── PANEL IZQUIERDO ── */
        .left {
            flex: 1;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.8rem;
        }
        .left-img {
            position: absolute;
            inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            z-index: 0;
        }
        .left-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0,0,0,.4) 0%,
                rgba(0,0,0,.05) 45%,
                rgba(0,0,0,.65) 100%
            );
            z-index: 1;
        }
        .left-top, .left-bottom { position: relative; z-index: 2; }
        .left-top { display: flex; justify-content: space-between; align-items: center; }

        .badge-works {
            font-size: 12px;
            font-weight: 500;
            color: rgba(255,255,255,.92);
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            padding: 5px 16px;
            border-radius: 999px;
        }

        .left-bottom { display: flex; align-items: center; gap: 10px; }
        .avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; color: #fff; font-weight: 600; flex-shrink: 0;
        }
        .author-name { font-size: 13px; color: #fff; font-weight: 500; }
        .author-role { font-size: 11px; color: rgba(255,255,255,.5); margin-top: 2px; }
        .nav-btns { display: flex; gap: 6px; margin-left: auto; }
        .nav-btn {
            width: 30px; height: 30px;
            border-radius: 50%;
            border: 1px solid rgba(255,255,255,.3);
            background: rgba(255,255,255,.1);
            color: rgba(255,255,255,.85);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; font-size: 14px;
        }

        /* ── PANEL DERECHO ── */
        .right {
            width: 340px;
            background: #fff;
            padding: 2.2rem 2.4rem 2rem;
            display: flex;
            flex-direction: column;
        }
        .right-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.4rem;
        }
        .brand { font-size: 14px; font-weight: 700; color: #0f172a; letter-spacing: .8px; }

        h1 { font-size: 28px; font-weight: 700; color: #0f172a; margin-bottom: 6px; letter-spacing: -.5px; }
        .subtitle { font-size: 13px; color: #64748b; margin-bottom: 2rem; }

        .error-box {
            background: #fee2e2; border: 1px solid #fca5a5;
            color: #991b1b; padding: .7rem 1rem;
            border-radius: 8px; font-size: .82rem; margin-bottom: 1rem;
        }

        label {
            display: block; font-size: 11px; font-weight: 600;
            color: #64748b; text-transform: uppercase;
            letter-spacing: .6px; margin-bottom: 5px;
        }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 10px 13px;
            border: 1px solid #e2e8f0; border-radius: 8px;
            background: #f8fafc; color: #0f172a;
            font-size: 13px; outline: none;
            margin-bottom: 1.1rem;
            transition: border-color .15s, background .15s;
        }
        input:focus { border-color: #6366f1; background: #fff; }

        .forgot {
            font-size: 12px; color: #6366f1;
            text-align: right; margin-top: -8px;
            margin-bottom: 2rem; text-decoration: none; display: block;
        }
        .forgot:hover { text-decoration: underline; }

        .btn-login {
            width: 100%; padding: 12px;
            background: #e63232; border: none;
            border-radius: 8px; color: #fff;
            font-size: 15px; font-weight: 600;
            cursor: pointer; letter-spacing: .2px;
            transition: background .15s, transform .1s;
        }
        .btn-login:hover  { background: #c42a2a; }
        .btn-login:active { transform: scale(.98); }
    </style>
</head>
<body>

    {{-- FONDO: imagen completa --}}
    <div class="bg-image">
        <img src="https://res.cloudinary.com/dlgeap8h0/image/upload/v1781105098/iStock-2210631585_ef4mer.jpg"
             alt="">
    </div>

    {{-- FONDO: máscara diagonal blanca desde la derecha --}}
    <div class="bg-diagonal">
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <polygon points="62,0 100,0 100,100 52,100" fill="white"/>
        </svg>
    </div>

    {{-- TARJETA --}}
    <div class="wrapper">

        {{-- Panel izquierdo --}}
        <div class="left">
            <img class="left-img"
                 src="https://res.cloudinary.com/dlgeap8h0/image/upload/v1781105098/iStock-2210631585_ef4mer.jpg"
                 alt="Paisaje Fiesta Tours">
            <div class="left-overlay"></div>

            <div class="left-top">
                <span class="badge-works">Destinos Seleccionados</span>
            </div>

            <div class="left-bottom">
                <div class="avatar">FT</div>
                <div>
                    <div class="author-name">Fiesta Tours</div>
                    <div class="author-role">Viajes &amp; Experiencias</div>
                </div>
                <div class="nav-btns">
                    <button class="nav-btn">&#8592;</button>
                    <button class="nav-btn">&#8594;</button>
                </div>
            </div>
        </div>

        {{-- Panel derecho --}}
        <div class="right">
            <div class="right-top">
                <span class="brand">FIESTA TOURS</span>
            </div>

            <h1>Bienvenido</h1>
            <p class="subtitle">Ingresa a tu cuenta para continuar</p>

            @if($errors->any())
                <div class="error-box">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="correo@ejemplo.com"
                       required autofocus>

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required>

                <a href="#" class="forgot">¿Olvidaste tu contraseña?</a>

                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>
        </div>

    </div>

</body>
</html>
