<?php
if( !isset($_SESSION['application']) ){
    header("Location: /");
}else if( !isset($_SESSION['application']['telephone'])){
    header("Location: /create");
}else if( !isset($_SESSION['application']['loading_method'])){
    header("Location: /create?page=2");
}else {
    unset($_SESSION['application']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <? require_once __DIR__ . './../../components/meta.php' ?>
    <? require_once __DIR__ . './../../components/style.php' ?>
    <title>Trucklink — Application completed</title>
</head>

<body>
    <div class="wrapper applicaton-create">
        <? require_once __DIR__ . './../../components/header.php'; ?>
        <main class="main">
            <div class="container">
                <div class="applicaton-create__main">
                    <div class="applicaton-create__top">
                        <div class="applicaton-create__title section-title">
                            Information submitted
                        </div>
                    </div>
                    <div class="applicaton-create__subtitle section-subtitle">
                        You will be contacted within 15 minutes or you can sign up to track progress of your shipment
                    </div>
                    <div class="applicaton-create__ready">
                        <svg width="2.87rem" height="2.87rem" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.977974" d="M13.3991 5.75C9.16517 5.75 5.73242 9.18274 5.73242 13.4167V32.5833C5.73242 36.8172 9.16517 40.2499 13.3991 40.2499H32.5658C36.7997 40.2499 40.2324 36.8172 40.2324 32.5833V24.9166C40.2324 23.8586 39.3738 23 38.3158 23C37.2578 23 36.3991 23.8586 36.3991 24.9166V32.5833C36.3991 34.7012 34.6837 36.4166 32.5658 36.4166H13.3991C11.2812 36.4166 9.56576 34.7012 9.56576 32.5833V13.4167C9.56576 11.2987 11.2812 9.58333 13.3991 9.58333H28.7324C29.7904 9.58333 30.6491 8.72466 30.6491 7.66666C30.6491 6.60867 29.7904 5.75 28.7324 5.75H13.3991ZM40.2324 7.66666C39.7418 7.66666 39.2281 7.83917 38.8544 8.20526L22.1429 24.6176C21.6503 25.1025 21.1539 25.0681 20.7668 24.4969L18.8501 21.6813C18.2636 20.8169 17.0331 20.5658 16.1553 21.1427C15.2755 21.7197 15.0283 22.9137 15.6148 23.7781L17.5314 26.5937C19.2584 29.141 22.6374 29.4783 24.8397 27.3125L41.6105 10.8407C42.358 10.1066 42.358 8.94126 41.6105 8.20526C41.2368 7.83726 40.7231 7.66666 40.2324 7.66666Z" fill="white" />
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
        <? require_once __DIR__ . './../../components/footer.php'; ?>
    </div>
    <? require_once __DIR__ . './../../components/script.php'; ?>
</body>

</html>