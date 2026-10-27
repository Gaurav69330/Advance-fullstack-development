<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign In | GigPulse</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f6f4ec;
            color: #20231e;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        main {
            max-width: 480px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .card {
            padding: 30px;
            background: #fffef9;
            border: 1px solid #d9dccf;
            border-radius: 10px;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 34px;
            line-height: 1.2;
        }

        .intro {
            color: #686b60;
        }

        .card label {
            display: block;
            margin-top: 18px;
            font-size: 14px;
            font-weight: bold;
        }

        .card input {
            display: block;
            width: 100%;
            min-height: 46px;
            margin-top: 6px;
            padding: 10px 12px;
            border: 1px solid #c8cdbd;
            border-radius: 5px;
            background: white;
            color: #20231e;
            font: inherit;
        }

        .card input[aria-invalid="true"] {
            border-color: #a83232;
        }

        .submit-button {
            width: 100%;
            min-height: 46px;
            margin-top: 25px;
            padding: 12px;
            border: 0;
            border-radius: 5px;
            background: #20231e;
            color: white;
            font: inherit;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-button:hover {
            background: #344b38;
        }

        .error {
            margin: 6px 0 0;
            color: #a12626;
            font-size: 13px;
        }

        .success {
            padding: 12px;
            border: 1px solid #bbc9a4;
            border-radius: 5px;
            background: #e8ecdf;
        }

        .register-link {
            margin-bottom: 0;
            text-align: center;
            font-size: 14px;
        }

        .card a {
            color: #435d21;
        }

        :focus-visible {
            outline: 3px solid #637c26;
            outline-offset: 3px;
        }

        @media (max-width: 480px) {
            main {
                margin: 30px auto;
            }

            .card {
                padding: 22px;
            }
        }
    </style>
</head>

<body>
    @include('partials.header')

    <main>
        <section class="card" aria-labelledby="login-heading">
            <h1 id="login-heading">Welcome back.</h1>

            <p class="intro">
                Sign in and find your next good night.
            </p>

            @if (session('success'))
                <p class="success" role="status">
                    {{ session('success') }}
                </p>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <label for="email">Email address</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    maxlength="255"
                    autocomplete="email"
                    required
                    autofocus
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    @error('email')
                        aria-describedby="email-error"
                    @enderror
                >

                @error('email')
                    <p class="error" id="email-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror

                <label for="password">Password</label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    @error('password')
                        aria-describedby="password-error"
                    @enderror
                >

                @error('password')
                    <p class="error" id="password-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror

                <button class="submit-button" type="submit">
                    Sign in →
                </button>
            </form>

            <p class="register-link">
                New to GigPulse?
                <a href="{{ route('register') }}">Create an account</a>
            </p>
        </section>
    </main>
</body>
</html>