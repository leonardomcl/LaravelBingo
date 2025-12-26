<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/js/app.js'])
    @endif
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-start {
            width: 96%;
            max-width: 700px;
            margin-top: 4px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="card card-start p-4 pb-5 bg-white w-100">
        <div class="text-center mb-2">
            <h3 class="card-title text-danger mb-2">🎄 Bingo de Natal</h3>
        </div>

        <form action="{{ route('bingo.auth') }}" method="POST">
            @csrf <div class="mb-3">
                <label for="name" class="form-label">Seu Nome ou Apelido:</label>
                <input type="text" name="name" id="username" class="form-control form-control-lg"
                    placeholder="Ex: João" required autofocus>
            </div>

            <button type="submit" class="mt-3 btn btn-primary btn-lg w-100">
                Entrar no Jogo <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>

</html>
