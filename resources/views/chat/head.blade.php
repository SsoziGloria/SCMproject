<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{ config('app.name', 'Dashboard - ChocolateSCM ') }}</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    @vite(['resources/css/app.css'])<!-- Vite CSS -->
    @livewireStyles
    @wirechatStyles
    <style>
        .tw-chat {
            height: calc(100vh - 5rem);
        }

        :root {
            --wc-brand-primary: '#f59e0b';
            color: #000;
            background-color: #000;
            --green-chat: #cffdce;


            --wc-light-primary: #f59e0b;
            /* white */
            --wc-light-secondary: rgb(30, 24, 15);
            /* --color-gray-100 */
            --wc-light-accent: rgb(30, 24, 15);
            /* --color-gray-50 */
            --wc-light-border: rgb(30, 24, 15);
            /* --color-gray-200 */
            --color-white: rgb(27, 16, 16);

            --wc-dark-primary: #ececec;
            /* --color-zinc-900 */
            --wc-dark-secondary: #ffffff;
            /* --color-zinc-800 */
            --wc-dark-accent: #c27f20;
            /* --color-zinc-700 */
            --wc-dark-border: #a66d1f;
            /* --color-zinc-700 */
        }
    </style>

</head>