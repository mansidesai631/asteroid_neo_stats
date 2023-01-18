<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Vue Js Page</title>

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.css">

    </head>
    <body>

        <div id="app">
            <neostats-component resource="{{ route('getneodata') }}"/>
        </div>

        <script>
            window.Laravel = <?php echo json_encode([
                '_token' => csrf_token(),
            ]); ?>
        </script>
        <script src="{{ asset('js/app.js') }}"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>

    </body>
</html>
