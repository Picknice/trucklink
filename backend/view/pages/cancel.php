<!DOCTYPE html>
<html lang="en">
<head>
    <? require_once __DIR__ . './../components/meta.php' ?>
    <? require_once __DIR__ . './../components/style.php' ?>
    <title>Trucklink — Payment cancel</title>
</head>

<body>
<div class="wrapper applicaton-create">
    <? require_once __DIR__ . './../components/header.php'; ?>
    <main class="main">
        <div class="container">
            <div class="applicaton-create__main">
                <div class="applicaton-create__top">
                    <div class="applicaton-create__title section-title">
                        Payment failed
                    </div>
                </div>
                <div class="applicaton-create__subtitle section-subtitle">
                    Sorry!
                </div>
                <div class="applicaton-create__ready">
                    <svg width="59" height="59" viewBox="0 0 59 59" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M29.0786 9.27148C18.0432 9.27148 9.27148 18.0432 9.27148 29.0786C9.27148 40.114 18.0432 48.8858 29.0786 48.8858C40.114 48.8858 48.8858 40.114 48.8858 29.0786C48.8858 18.0432 40.114 9.27148 29.0786 9.27148ZM29.0786 46.0562C19.741 46.0562 12.1011 38.4163 12.1011 29.0786C12.1011 19.741 19.741 12.1011 29.0786 12.1011C38.4163 12.1011 46.0562 19.741 46.0562 29.0786C46.0562 38.4163 38.4163 46.0562 29.0786 46.0562Z" fill="#EF3939"/>
                        <path d="M36.5559 38.7705L29.0786 31.2931L21.6013 38.7705L19.3857 36.555L26.8631 29.0776L19.3857 21.6003L21.6013 19.3848L29.0786 26.8621L36.5559 19.3848L38.7715 21.6003L31.2941 29.0776L38.7715 36.555L36.5559 38.7705Z" fill="#EF3939"/>
                    </svg>
                    <? if ($_SESSION['user']['user_id']) : ?>
                        <a class="applicaton-create__form_create button" href="/profile?id=<?= $_SESSION['user']['user_id'] ?>">
                            Personal Area
                        </a>
                    <? else : ?>
                        <a class="applicaton-create__form_create button" href="/signup">
                            Sign up
                        </a>
                        <a class="applicaton-create__href link" href="/login">
                            Login
                        </a>
                    <? endif ?>
                </div>
            </div>
        </div>
    </main>
    <? require_once __DIR__ . './../components/footer.php'; ?>
</div>
<? require_once __DIR__ . './../components/script.php'; ?>
</body>

</html>