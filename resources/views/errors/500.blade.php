<!DOCTYPE html>
<html lang="en">
<head>
    <title>Staff Management</title>
    <meta charset="utf-8"/>
    <meta name="description" content=""/>
    <meta name="keywords" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="" />
    <meta property="og:url" content=""/>
    <meta property="og:site_name" content="" />
    <link rel="canonical" href=""/>
    <link rel="shortcut icon" href="{{asset('assets/img/favicon.png')}}"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700"/>
    <link href="{{asset('assets/css/plugins.bundle.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css"/>
</head>

<body  id="kt_body"  class="app-blank" >
<script>
    let defaultThemeMode = "light";
    let themeMode;

    if ( document.documentElement ) {
        if ( document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if ( localStorage.getItem("data-bs-theme") !== null ) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }
</script>

<div class="d-flex flex-column flex-root" id="kt_app_root">
    <div class="d-flex flex-column flex-column-fluid">
        <div class="d-flex flex-row-fluid flex-column flex-column-fluid text-center p-10 py-lg-20">
            <!--begin::Logo-->
            <a href="{{route('login')}}" class="pt-lg-20 mb-12">
                <img alt="Logo" src="{{asset('assets/img/favicon.png')}}" class="theme-light-show h-60px h-lg-60px"/>
                <img alt="Logo" src="{{asset('assets/img/logo_s.svg')}}" class="theme-dark-show h-60px h-lg-60px"/>
            </a>

            <h1 class="fw-bold fs-2qx text-gray-800 mb-7">
                Ошибка сервера!
            </h1>

            <div class="fs-3 fw-semibold text-muted mb-10">
                Чтобы всё наладить, пиши в IT: <br/>
                <a href="mailto:it@metak.az" class="link-primary fw-bold">it@metak.az</a>, они разберутся!
            </div>

            <div class="text-center mb-10">
                <a href="{{url()->previous()}}" class="btn btn-lg btn-primary fw-bold">
                    Вернуться
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
