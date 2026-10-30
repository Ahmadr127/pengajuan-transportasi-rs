<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Transportasi RSAzra') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="@if($hideHeader ?? false) overflow-hidden bg-white @else bg-white text-slate-800 antialiased @endif" style="@if($hideHeader ?? false) height:100vh; @else min-height:100vh; @endif">
    <div class="@if($hideHeader ?? false) d-flex @else d-flex flex-column @endif" style="@if($hideHeader ?? false) height:100vh; @else min-height:100vh; @endif">

        @unless($hideHeader ?? false)
            <header class="shadow-lg" style="background-color:#059669;">
                <div class="container-xl px-4 py-3 d-flex align-items-center justify-content-between" style="max-width:72rem;">
                    <div class="min-w-0">
                        <div class="text-xxs text-uppercase tracking-widest fw-600" style="color:rgba(209,250,229,0.8);">
                            RS Azra
                        </div>
                    </div>
                </div>
            </header>
        @endunless

        <div class="@if($hideHeader ?? false) flex-grow-1 d-flex w-100 @else flex-grow-1 d-flex align-items-center justify-content-center px-3 px-sm-4 py-5 @endif">
            @if($hideHeader ?? false)
                {{ $slot }}
            @else
                <div class="w-100" style="max-width:28rem;">
                    <div class="mb-4 text-center">
                        {{-- header content placeholder --}}
                    </div>

                    <div class="bg-white shadow-xl border border-slate-200 rounded p-4 p-sm-4 transition">
                        {{ $slot }}
                    </div>

                    <div class="mt-4 text-center text-xs text-slate-400">
                        {{ config('app.name', 'Laravel') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
