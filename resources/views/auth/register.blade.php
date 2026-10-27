<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Join GigPulse</title>

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

        .intro,
        .hint {
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

        .hint {
            margin: 6px 0 0;
            font-size: 12px;
        }

        .login-link {
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
        <section class="card" aria-labelledby="register-heading">
            <h1 id="register-heading">Join the crowd.</h1>

            <p class="intro">
                Create your account and discover your next good night.
            </p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <label for="name">Name</label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    maxlength="100"
                    autocomplete="name"
                    required
                    autofocus
                    aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                    @error('name')
                        aria-describedby="name-error"
                    @enderror
                >

                @error('name')
                    <p class="error" id="name-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror

                <label for="email">Email address</label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    maxlength="255"
                    autocomplete="email"
                    required
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
                    minlength="12"
                    maxlength="72"
                    autocomplete="new-password"
                    required
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    aria-describedby="password-hint{{ $errors->has('password') ? ' password-error' : '' }}"
                >

                <p class="hint" id="password-hint">
                    Use between 12 and 72 characters.
                </p>

                @error('password')
                    <p class="error" id="password-error" role="alert">
                        {{ $message }}
                    </p>
                @enderror

                <label for="password_confirmation">
                    Confirm password
                </label>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    minlength="12"
                    maxlength="72"
                    autocomplete="new-password"
                    required
                    aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                    @error('password_confirmation')
                        aria-describedby="password-confirmation-error"
                    @enderror
                >

                @error('password_confirmation')
                    <p
                        class="error"
                        id="password-confirmation-error"
                        role="alert"
                    >
                        {{ $message }}
                    </p>
                @enderror

                <button class="submit-button" type="submit">
                    Create account →
                </button>
            </form>

            <p class="login-link">
                Already have an account?
                <a href="{{ route('login') }}">Sign in</a>
            </p>
        </section>
    </main>
</body>
</html>