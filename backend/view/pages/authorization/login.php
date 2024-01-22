<?php
$user = getUser();
if($user){
    header('Location: /profile');
}
$button_log = $_REQUEST['button_log'];

if (isset($button_log)) {
    $email = $_REQUEST['user_email'];
    $password = $_REQUEST['user_password'];

    $query = UserController::log($email, $password);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? require_once __DIR__ . './../../components/meta.php' ?>
    <? require_once __DIR__ . './../../components/style.php' ?>
    <title>Trucklink — Login</title>
</head>

<body>
    <div class="wrapper registration">
        <? require_once __DIR__ . './../../components/header.php'; ?>
        <main class="main">
            <div class="container">
                <div class="registration__main">
                    <a class="registration__link-back link" onclick="history.go(-1)">
                        Back
                    </a>
                    <div class="registration__title section-title">
                        Login
                    </div>
                    <div class="registration__subtitle section-subtitle">
                        Log in to the service to view cargo applications and get information about them
                    </div>
                    <form class="registration__form" method="POST">
                        <div class="registration__form_top">
                            <div class="input__block">
                                <label class="input__title" for="user_email">
                                    Email
                                </label>
                                <input class="input<?= $query['data']['email'] ? ' _error' : null ?>" name="user_email" type="email" id="user_email">
                                <? if ($query['data']['email']): ?>
                                    <div class="_color-error">
                                        <?= $query['data']['email'] ?>
                                    </div>
                                <? endif; ?>
                            </div>
                            <div class="input__block">
                                <label class="input__title" for="user_password">
                                    Password
                                </label>
                                <div class="input-password block__password">
                                    <input class="input<?= $query['data']['password'] ? ' _error' : null ?>" name="user_password" type="password" id="user_password">
                                    <div class="password-icon">
                                        <svg class="password-icon_hide" width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g opacity="0.4">
                                                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </g>
                                        </svg>
                                        <svg class="password-icon_show" width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g opacity="0.4" clip-path="url(#clip0_173_920)">
                                                <path d="M17.94 17.94C16.2306 19.243 14.1491 19.9649 12 20C5 20 1 12 1 12C2.24389 9.68192 3.96914 7.65663 6.06 6.06003M9.9 4.24002C10.5883 4.0789 11.2931 3.99836 12 4.00003C19 4.00003 23 12 23 12C22.393 13.1356 21.6691 14.2048 20.84 15.19M14.12 14.12C13.8454 14.4148 13.5141 14.6512 13.1462 14.8151C12.7782 14.9791 12.3809 15.0673 11.9781 15.0744C11.5753 15.0815 11.1752 15.0074 10.8016 14.8565C10.4281 14.7056 10.0887 14.4811 9.80385 14.1962C9.51897 13.9113 9.29439 13.572 9.14351 13.1984C8.99262 12.8249 8.91853 12.4247 8.92563 12.0219C8.93274 11.6191 9.02091 11.2219 9.18488 10.8539C9.34884 10.4859 9.58525 10.1547 9.88 9.88003" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M1 1L23 23" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_173_920">
                                                    <rect width="24" height="24" fill="white" />
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </div>
                                </div>
                                <? if ($query['data']['password']): ?>
                                    <div class="_color-error">
                                        <?= $query['data']['password'] ?>
                                    </div>
                                <? endif; ?>
                            </div>
                        </div>
                        <button class="registration__button button" name="button_log">
                            Login
                        </button>
                        <div class="login_signup">
                            Don't have an account? <a href="/signup">Sign up</a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
        <? require_once __DIR__ . './../../components/footer.php'; ?>
    </div>
    <? require_once __DIR__ . './../../components/script.php'; ?>
</body>

</html>