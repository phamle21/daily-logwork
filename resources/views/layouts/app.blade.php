<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report</title>
    @livewireStyles
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100">
    {{ $slot }}
    @livewireScripts
</body>
</html>