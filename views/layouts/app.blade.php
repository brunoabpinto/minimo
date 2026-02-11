<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Minimo</title>
    <link rel="stylesheet" href="/styles/app.css?v=20260211b" />
</head>

<body>
    @include('components.header')
    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>
    @include('components.footer')
</body>

</html>
