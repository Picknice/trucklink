<!DOCTYPE html>
<html lang="en">

<head>
    <? require_once __DIR__ . './../components/meta.php' ?>
    <? require_once __DIR__ . './../components/style.php' ?>
    <title>Trucklink — Payment passed</title>
</head>

<body>
<div class="wrapper applicaton-create">
    <? require_once __DIR__ . './../components/header.php'; ?>
    <main class="main">
        <div class="container">
            <div class="applicaton-create__main">
                <div class="applicaton-create__top">
                    <div class="applicaton-create__title section-title">
                        Paid successfully
                    </div>
                </div>
                <div class="applicaton-create__subtitle section-subtitle">
                    Thanks
                </div>
                <div class="applicaton-create__ready">
                    <svg width="38" height="36" viewBox="0 0 38 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.977974" d="M8.39909 0.75C4.16517 0.75 0.732422 4.18274 0.732422 8.41665V27.5833C0.732422 31.8172 4.16517 35.2499 8.39909 35.2499H27.5658C31.7997 35.2499 35.2324 31.8172 35.2324 27.5833V19.9166C35.2324 18.8586 34.3738 18 33.3158 18C32.2578 18 31.3991 18.8586 31.3991 19.9166V27.5833C31.3991 29.7012 29.6837 31.4166 27.5658 31.4166H8.39909C6.28118 31.4166 4.56576 29.7012 4.56576 27.5833V8.41665C4.56576 6.29874 6.28118 4.58333 8.39909 4.58333H23.7324C24.7904 4.58333 25.6491 3.72466 25.6491 2.66666C25.6491 1.60867 24.7904 0.75 23.7324 0.75H8.39909ZM35.2324 2.66666C34.7418 2.66666 34.2281 2.83917 33.8544 3.20526L17.1429 19.6176C16.6503 20.1025 16.1539 20.0681 15.7668 19.4969L13.8501 16.6813C13.2636 15.8169 12.0331 15.5658 11.1553 16.1427C10.2755 16.7197 10.0283 17.9137 10.6148 18.7781L12.5314 21.5937C14.2584 24.141 17.6374 24.4783 19.8397 22.3125L36.6105 5.84067C37.358 5.10659 37.358 3.94126 36.6105 3.20526C36.2368 2.83726 35.7231 2.66666 35.2324 2.66666Z" fill="#77FF85"/>
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