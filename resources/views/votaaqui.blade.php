<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VotaAqui - O teu voto Online</title>
    <meta name="description" content="Vote no teu participante favorito do Reality Show Estrelas do LIV">
    <meta name="keywords" content="votação, reality show, estrelas do liv, participantes, voto online">

    <!-- Favicons -->
    <link href="{{asset('votaaqui/assets/img/favicon.png')}}" rel="icon">
    <link href="{{asset('votaaqui/assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="{{asset('votaaqui/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('votaaqui/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{asset('votaaqui/assets/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">
    <link href="{{asset('votaaqui/assets/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{asset('votaaqui/assets/css/main.css')}}" rel="stylesheet">

    <!-- VotaAqui Custom CSS -->
    <link href="{{asset('css/votaaqui.css')}}" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/css/votaaqui.css', 'resources/js/app.js'])
</head>
<body class="speakers-page">
    <div id="app"></div>

    <!-- Bootstrap JS -->
    <script src="{{asset('votaaqui/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('votaaqui/assets/vendor/php-email-form/validate.js')}}"></script>
    <script src="{{asset('votaaqui/assets/vendor/purecounter/purecounter_vanilla.js')}}"></script>
    <script src="{{asset('votaaqui/assets/vendor/swiper/swiper-bundle.min.js')}}"></script>
    <script src="{{asset('votaaqui/assets/vendor/glightbox/js/glightbox.min.js')}}"></script>

    <!-- Main JS File -->
    <script src="{{asset('votaaqui/assets/js/main.js')}}"></script>
</body>
</html>
