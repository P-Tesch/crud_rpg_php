<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            try {
                const storedTheme = localStorage.getItem("theme");
                if (storedTheme === "noturno" || storedTheme === "aurora") {
                    document.documentElement.setAttribute("data-theme", storedTheme);
                }
            } catch (e) {}
        </script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    <body class="text-base-content">
        @inertia
    </body>
</html>
