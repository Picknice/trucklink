<?

$page_count = (int) $_GET['page'];

if ($page_count == 2) {
    die(require_once './view/pages/create/create2.php');
} else if ($page_count == 3) {
    die(require_once './view/pages/create/create3.php');
}

$user_fullname = $_REQUEST['user_fullname'];
$user_telephone = $_REQUEST['user_telephone'];
$user_email = $_REQUEST['user_email'];
$button_create = $_REQUEST['button_create'];
if(isset($_SESSION['application'])){
    if(isset($_SESSION['application']['fullname']) && mb_strlen($_SESSION['application']['fullname'])){
        $user_fullname = $_SESSION['application']['fullname'];
    }
    if(isset($_SESSION['application']['telephone']) && mb_strlen($_SESSION['application']['telephone'])){
        $user_telephone = $_SESSION['application']['telephone'];
    }
    if(isset($_SESSION['application']['email']) && mb_strlen($_SESSION['application']['email'])){
        $user_email = $_SESSION['application']['email'];
    }
}
if (isset($button_create)) {
    $query = ApplicationController::secondCreate($user_fullname, $user_telephone, $user_email);
}
if(!$user_fullname){
    $user_fullname = $_SESSION['user']['name'] . " " . $_SESSION['user']['surname'];
}
if(!$user_telephone){
    $user_telephone = $_SESSION['user']['telephone'];
}
if(!$user_email){
    $user_email = $_SESSION['user']['email'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? require_once __DIR__ . './../../components/meta.php' ?>
    <? require_once __DIR__ . './../../components/style.php' ?>
    <title>Trucklink — Contact information</title>
</head>

<body>
    <div class="wrapper applicaton-create">
        <? require_once __DIR__ . './../../components/header.php'; ?>
        <main class="main">
            <div class="container">
                <div class="applicaton-create__main">
                    <div class="applicaton-create__top">
                        <div class="applicaton-create__link">
                            <div class="applicaton-create__arrow">
                                <svg width="1rem" height="0.69rem" viewBox="0 0 16 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.195001 5.04502L4.875 0.201097C5.00092 0.0766103 5.1691 0.00790116 5.34354 0.00967715C5.51797 0.0114531 5.6848 0.0835732 5.80831 0.210599C5.93183 0.337626 6.00221 0.509464 6.00439 0.68933C6.00658 0.869197 5.94039 1.0428 5.82 1.17297L2.3025 4.813H15.3325C15.4234 4.80726 15.5145 4.82076 15.6002 4.85269C15.6859 4.88462 15.7643 4.93429 15.8307 4.99865C15.897 5.06301 15.9499 5.14069 15.986 5.22692C16.0222 5.31314 16.0408 5.40608 16.0408 5.50002C16.0408 5.59395 16.0222 5.6869 15.986 5.77312C15.9499 5.85935 15.897 5.93703 15.8307 6.00139C15.7643 6.06575 15.6859 6.11542 15.6002 6.14735C15.5145 6.17928 15.4234 6.19278 15.3325 6.18704H2.25L5.8175 9.82449C5.88537 9.8867 5.94022 9.96253 5.97868 10.0474C6.01715 10.1322 6.03843 10.2242 6.04121 10.3178C6.044 10.4114 6.02824 10.5046 5.99489 10.5917C5.96154 10.6788 5.91131 10.7579 5.84727 10.8243C5.78323 10.8907 5.70673 10.9429 5.62244 10.9778C5.53816 11.0126 5.44786 11.0294 5.35708 11.027C5.2663 11.0246 5.17695 11.0032 5.09449 10.964C5.01203 10.9247 4.93819 10.8686 4.8775 10.7989L0.197501 6.01947C0.135366 5.95554 0.0860701 5.87959 0.0524359 5.79599C0.0188007 5.71238 0.00148773 5.62276 0.00148773 5.53224C0.00148773 5.44173 0.0188007 5.3521 0.0524359 5.2685C0.0860701 5.18489 0.135366 5.10895 0.197501 5.04502H0.195001Z" fill="#9A9A9A" />
                                </svg>
                            </div>
                            <a href="/" class="applicaton-create__href link">
                                back
                            </a>
                        </div>
                        <div class="applicaton-create__title section-title">
                            Get a quote
                        </div>
                    </div>
                    <div class="applicaton-create__subtitle section-subtitle">
                        Pleae fill out your contact information
                    </div>
                    <form class="applicaton-create__form" method="POST">
                        <div class="input__block">
                            <label class="input__title" for="user_fullname">
                                First and last name:
                            </label>
                            <input class="input<?= $query['data']['fullname'] ? ' _error' : null ?>" type="text" value="<?=$user_fullname?>" id="user_fullname" placeholder="Sergey Ivanov" name="user_fullname">
                            <? if ($query['data']['fullname']) : ?>
                                <div class="_color-error">
                                    <?= $query['data']['fullname'] ?>
                                </div>
                            <? endif; ?>
                        </div>
                        <div class="input__block">
                            <label class="input__title" for="user_telephone">
                                Phone:
                            </label>
                            <div class="input__block-tel">
                                <input
                              class="input input-tel<?= $query['data']['telephone'] ? ' _error' : null ?>" 
                              type="tel" 
                              value="<?= $user_telephone ?>"
                              id="user_telephone" 
                              placeholder="+1 (ххх) ххх-хх-хх" 
                              name="user_telephone">
                                <? if ($query['data']['telephone']) : ?>
                                    <div class="_color-error">
                                        <?= $query['data']['telephone'] ?>
                                    </div>
                                <? endif; ?>
                                <div class="input-tel__flag">
                                    <!-- Here appears a country flag -->
                                    <!-- <img src="/view/static/img/flags-4x3/ac.svg" alt=""> -->
                                </div>
                            </div>
                        </div>
                        <div class="input__block">
                            <label class="input__title" for="user_email">
                                E-mail:
                            </label>
                            <input class="input<?= $query['data']['email'] ? ' _error' : null ?>" type="email" value="<?=$user_email?>" id="user_email" placeholder="info@trucklink.com" name="user_email">
                            <? if ($query['data']['email']) : ?>
                                <div class="_color-error">
                                    <?= $query['data']['email'] ?>
                                </div>
                            <? endif; ?>
                        </div>
                        <button class="applicaton-create__form_create button" name="button_create">
                            Next step: Load info
                        </button>
                    </form>
                </div>
            </div>
        </main>
        <? require_once __DIR__ . './../../components/footer.php'; ?>
    </div>
    <? require_once __DIR__ . './../../components/script.php'; ?>
</body>

</html>