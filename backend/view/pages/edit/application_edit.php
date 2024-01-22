<?php
$user = getUser();
if(!$user){
    header('Location: /login');
}
$application_id = (int) $_GET['id'];
$application = db()->query('SELECT a.*,tt.name as transport_type_name, sz.name as size_name FROM application as a LEFT JOIN transport_type as tt ON a.transport_type = tt.transport_type_id LEFT JOIN size as sz ON a.size = sz.size_id WHERE application_id=?', $application_id)->fetch_assoc();
if(!$application){
    header('Location: /profile#cargo');
}
if($user['user_type_id'] == 1 && $user['user_id'] != $application['user_id']){
    header('Location: /profile#cargo');
}
$transport_type_db = getDbDate('transport_type');
$size_db = getDbDate('size');

$transport_type_fetch_all = $transport_type_db->fetch_all(MYSQLI_ASSOC);
$transport_type_fetch_all = is_array($transport_type_fetch_all) ? array_column($transport_type_fetch_all, null, 'transport_type_id') : [];
$size_db = $size_db->fetch_all(MYSQLI_ASSOC);
$size_db = is_array($size_db) ? array_column($size_db, null, 'size_id') : [];

$method_db = getDbDate('method');
$method_db = $method_db ? array_column($method_db->fetch_all(MYSQLI_ASSOC), null, 'method_id') : [];

$button_save = $_REQUEST['button_save'];
if (isset($button_save)) {
    $from = $_REQUEST['from_checked'] ? $_REQUEST['from'] : null;
    $to = $_REQUEST['to_checked'] ? $_REQUEST['to'] : null;
    $date = $_REQUEST['date'];
    $transport_type = $_REQUEST['transport_type'];
    $loading_method = $_REQUEST['loading_method'];
    $size = $_REQUEST['size'];
    $cargo_size = $_REQUEST['cargo_size'];
    $photo = isset($_FILES['photo']) && strlen($_FILES['photo']['tmp_name']) ? $_FILES['photo'] : '';
    $mass = $_REQUEST['mass'];
    $price = $_REQUEST['price'];
    $comment = $_REQUEST['comment'];
    $method = $_REQUEST['method'];
    $quantity = $_REQUEST['quantity'];
    $query = ApplicationController::edit($from, $to, $date, $transport_type, $loading_method, $size, $cargo_size, $photo, $mass, $price, $method, $comment, $quantity, $application_id);
    $application = db()->query('SELECT a.*,tt.name as transport_type_name, sz.name as size_name FROM application as a LEFT JOIN transport_type as tt ON a.transport_type = tt.transport_type_id LEFT JOIN size as sz ON a.size = sz.size_id WHERE application_id=?', $application_id)->fetch_assoc();
}
$from = $application['from'];
$to = $application['to'];
$date = $application['date'];
$transport_type = $application['transport_type'];
$loading_method = $application['loading_method'];
$size = $application['size'];
$cargo_size =  $application['cargo_size'];
$photo = strval($application['photo']);
$mass = $application['mass'];
$price = $application['price'];
$comment = $application['comment'];
$method = $application['method'];
$date_create = $application['date_created'];
$quantity = $application['quantity'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? require_once __DIR__ . './../../components/meta.php' ?>
    <? require_once __DIR__ . './../../components/style.php' ?>
    <title>Trucklink — Application edit</title>
</head>

<body>
    <div class="wrapper applicaton-create application-edit">
        <? require_once __DIR__ . './../../components/header.php'; ?>
        <main class="main">
            <div class="container">
                <div class="applicaton-create__main">
                    <div class="applicaton-create__top">
                        <div class="applicaton-create__title section-title">
                            EDIT CARGO
                        </div>
                    </div>
                    <div class="applicaton-create__subtitle section-subtitle">
                        ID <?= $application_id ?>
                    </div>
                    <form class="applicaton-create__form" method="POST" enctype="multipart/form-data">
                        <div class="input__block">
                            <label class="input__title" for="from">
                                From:
                            </label>
                            <div class="input__block_style">
                                <svg width="1.125rem" height="1.125rem" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.25 8.25L16.5 1.5L9.75 15.75L8.25 9.75L2.25 8.25Z" stroke="#7F858E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <input class="input addres-search _from_text<?= $query['data']['from'] ? ' _error' : '' ?>" type="text" name="from" id="from" placeholder="Enter the city of departure" value="<?= $application['from'] ?>">
                                <input class="input addres-search _from" type="text" name="from_checked" value="<?= $application['from'] ?>" hidden>
                            </div>
                        </div>
                        <div class="input__block">
                            <label class="input__title" for="to">
                                To:
                            </label>
                            <div class="input__block_style">
                                <svg width="1.125rem" height="1.125rem" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.25 8.25L16.5 1.5L9.75 15.75L8.25 9.75L2.25 8.25Z" stroke="#7F858E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <input class="input addres-search _to_text<?= $query['data']['to'] ? ' _error' : '' ?>" type="text" id="to" name="to" placeholder="Enter the delivery city" value="<?= $application['to'] ?>">
                                <input class="input addres-search _to" type="text" name="to_checked" value="<?= $application['to'] ?>" hidden>
                            </div>
                        </div>
                        <div class="input__block">
                            <label class="input__title" for="date">
                                Date of download:
                            </label>
                            <div class="input__block_style">
                                <svg width="1.75rem" height="1.75rem" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18.661 4.37461C18.661 3.87755 18.258 3.47461 17.761 3.47461C17.2639 3.47461 16.861 3.87755 16.861 4.37461V5.16639H11.1414V4.37461C11.1414 3.87755 10.7385 3.47461 10.2414 3.47461C9.74437 3.47461 9.34143 3.87755 9.34143 4.37461V5.16639H7.42161C5.9779 5.16639 4.64172 6.23991 4.64172 7.75822V11.142V19.601C4.64172 21.1193 5.9779 22.1928 7.42161 22.1928H20.5808C22.0245 22.1928 23.3607 21.1193 23.3607 19.601V11.142V7.75822C23.3607 6.23991 22.0245 5.16639 20.5808 5.16639H18.661V4.37461ZM21.5607 10.242V7.75822C21.5607 7.40779 21.2136 6.96639 20.5808 6.96639H18.661V7.75826C18.661 8.25532 18.258 8.65826 17.761 8.65826C17.2639 8.65826 16.861 8.25532 16.861 7.75826V6.96639H11.1414V7.75826C11.1414 8.25532 10.7385 8.65826 10.2414 8.65826C9.74437 8.65826 9.34143 8.25532 9.34143 7.75826V6.96639H7.42161C6.78886 6.96639 6.44172 7.40779 6.44172 7.75822V10.242H21.5607ZM6.44172 12.042H21.5607V19.601C21.5607 19.9514 21.2136 20.3928 20.5808 20.3928H7.42161C6.78886 20.3928 6.44172 19.9514 6.44172 19.601V12.042Z" fill="#7F858E" />
                                </svg>
                                <div class="calendar-form">
                                    <div class="calendar-from__active">
                                        <input type="text" class="input" placeholder="Date of download" id="date" name="date" value="<?= DateView::normalizeDate($application['date'], true) ?>" readonly>
                                    </div>
                                    <div class="calendar">
                                        <div class="calendar__month">
                                            <div class="calendar__arrow_left calendar__month_left">
                                                <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M7 12L2 6.49997L7 1" stroke="#2D2D41" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </div>
                                            <div class="calendar__month_text calendar__text"></div>
                                            <div class="calendar__arrow_right calendar__month_right">
                                                <svg width="8" height="13" viewBox="0 0 8 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 12L6 6.49997L1 1" stroke="#2D2D41" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="calendar__weekday">
                                            <li class="calendar__weekday_item">
                                                mn
                                            </li>
                                            <li class="calendar__weekday_item">
                                                ts
                                            </li>
                                            <li class="calendar__weekday_item">
                                                wd
                                            </li>
                                            <li class="calendar__weekday_item">
                                                th
                                            </li>
                                            <li class="calendar__weekday_item">
                                                fr
                                            </li>
                                            <li class="calendar__weekday_item">
                                                st
                                            </li>
                                            <li class="calendar__weekday_item">
                                                sn
                                            </li>
                                        </ul>
                                        <ul class="calendar__day">
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                            <li class="calendar__day_item"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="select">
                            <div class="select__title">
                                Transport type:
                            </div>
                            <div class="select__content">
                                <div class="select-block">
                                    <input class="select__input<?= $query['data']['transport_type'] ? ' _error' : '' ?>" type="text" value="<?=$transport_type_fetch_all[$transport_type]['name']?>" placeholder="Choose a transport type" readonly>
                                    <div class="select-icon">
                                        <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.742422 0.276188C0.374922 0.632552 0.374922 1.2071 0.742422 1.56346L6.97492 7.6071C7.26742 7.89073 7.73992 7.89073 8.03242 7.6071L14.2649 1.56346C14.6324 1.2071 14.6324 0.632551 14.2649 0.276187C13.8974 -0.0801763 13.3049 -0.0801762 12.9374 0.276187L7.49992 5.54164L2.06242 0.268916C1.70242 -0.0801745 1.10242 -0.0801757 0.742422 0.276188Z" fill="#6E7B8B" />
                                        </svg>
                                    </div>
                                </div>
                                <ul class="select__options">
                                    <? foreach ($transport_type_fetch_all as $k => $value) : ?>
                                        <li class="select__option">
                                            <label for="<?= "transport_type_" . $value['transport_type_id'] ?>">
                                                <?= $value['name'] ?>
                                            </label>
                                            <input type="radio" name="transport_type" id="<?= "transport_type_" . $value['transport_type_id'] ?>" <?= $application['transport_type'] == $value['transport_type_id'] ? 'checked' : '' ?> value="<?= $value['transport_type_id'] ?>" hidden<?=$k == $transport_type ? ' checked':''?>>
                                        </li>
                                    <? endforeach ?>
                                </ul>
                            </div>
                        </div>
                        <div class="select">
                            <div class="select__title">
                                Driver assistance in loading:
                            </div>
                            <div class="select__content">
                                <div class="select-block">
                                    <input class="select__input<?= $query['data']['loading_method'] ? ' _error' : '' ?>" type="text" value="<?=$loading_method !== null ? ($loading_method == '1' ? 'Yes' : 'No') : ''?>" placeholder="Choose an option" readonly>
                                    <div class="select-icon">
                                        <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.742422 0.276188C0.374922 0.632552 0.374922 1.2071 0.742422 1.56346L6.97492 7.6071C7.26742 7.89073 7.73992 7.89073 8.03242 7.6071L14.2649 1.56346C14.6324 1.2071 14.6324 0.632551 14.2649 0.276187C13.8974 -0.0801763 13.3049 -0.0801762 12.9374 0.276187L7.49992 5.54164L2.06242 0.268916C1.70242 -0.0801745 1.10242 -0.0801757 0.742422 0.276188Z" fill="#6E7B8B" />
                                        </svg>
                                    </div>
                                </div>
                                <ul class="select__options">
                                    <li class="select__option">
                                        <label for="loading_method_1">
                                            Yes
                                        </label>
                                        <input type="radio" name="loading_method" id="loading_method_1" value="1" hidden<?=$loading_method == '1' ? ' checked':''?>>
                                    </li>
                                    <li class="select__option">
                                        <label for="loading_method_0">
                                            No
                                        </label>
                                        <input type="radio" name="loading_method" id="loading_method_0" value="0" hidden<?=$loading_method == '0' ? ' checked':''?>>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="input__block pallets">
                            <div class="select select__cargo-size-method" <?=$size=='4'? 'style="display: none;"':''?>>
                                <div class="select__title">
                                    Cargo size:
                                </div>
                                <div class="select__content">
                                    <div class="select-block">
                                        <input class="select__input<?=$query['data']['size']? ' _error':''?>" type="text" placeholder="Select pallet size" readonly<?=$size!== null ? ' value="'. $size_db[$size]['name'] . '"' : ''?>>
                                        <div class="select-icon">
                                            <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.742422 0.276188C0.374922 0.632552 0.374922 1.2071 0.742422 1.56346L6.97492 7.6071C7.26742 7.89073 7.73992 7.89073 8.03242 7.6071L14.2649 1.56346C14.6324 1.2071 14.6324 0.632551 14.2649 0.276187C13.8974 -0.0801763 13.3049 -0.0801762 12.9374 0.276187L7.49992 5.54164L2.06242 0.268916C1.70242 -0.0801745 1.10242 -0.0801757 0.742422 0.276188Z" fill="#6E7B8B" />
                                            </svg>
                                        </div>
                                    </div>
                                    <ul class="select__options">
                                        <? foreach ($size_db as $k => $value) : ?>
                                            <li class="select__option">
                                                <label for="<?= "size_" . $value['size_id'] ?>">
                                                    <?= $value['name'] ?>
                                                </label>
                                                <input type="radio" name="size" id="<?= "size_" . $value['size_id'] ?>" value="<?= $value['size_id'] ?>" hidden<?=$size !== null && $k == $size ? ' checked': ''?>>
                                            </li>
                                        <? endforeach ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="input__block input__block__cargo-size" <?=$size!='4'? 'style="display: none;"':''?>>
                                <label class="input__title" for="cargo_size">
                                    Cargo size:
                                </label>
                                <div class="input-wrapper input-wrapper-cargo-size">
                                    <div class="go-back">back</div>
                                    <input class="input input-cargo-size-mask<?=$query['data']['cargo_size']? ' _error':''?>" type="text" id="cargo_size" placeholder="Enter cargo size" name="cargo_size" value="<?=$cargo_size?>">
                                </div>
                            </div>
                            <div class="input__block quantity">
                                <label class="input__title" for="quantity">
                                    Quantity:
                                </label>
                                <div class="input-wrapper input-wrapper-quantity">
                                    <input class="input-mask-number input<?=$query['data']['quantity']? ' _error':''?>" type="number" id="quantity" name="quantity" placeholder="1" value="<?=$quantity!=null?$quantity:''?>">
                                </div>
                            </div>
                        </div>
                        <div class="input__block">
                            <label class="input__title" for="mass">
                                Total weight of cargo:
                            </label>
                            <div class="input-wrapper input-wrapper-mass">
                                <input class="input<?=$query['data']['mass']? ' _error':''?>" type="number" id="mass" name="mass" placeholder="Enter the weight of the cargo" value="<?=$mass!=null?$mass:''?>">
                                <div class="input-meassure">lbs</div>
                            </div>
                        </div>
                        <div class="input__block input__block__price" <?=$method!='3'? 'style="display: none;"':''?>>
                            <label class="input__title" for="price">
                                Price:
                            </label>
                            <div class="input-wrapper input-wrapper-price">
                                <div class="go-back">back</div>
                                <input class="input<?=$query['data']['price']? ' _error':''?>" type="number" id="price" placeholder="Enter cost" name="price" value="<?=$price?>">
                                <div class="input-meassure">$</div>
                            </div>
                        </div>
                        <div class="select select__payment-method" <?=$method=='3'? 'style="display: none;"':''?>>
                            <div class="select__title">
                                Payment method:
                            </div>
                            <div class="select__content">
                                <div class="select-block">
                                    <input class="select__input<?=$query['data']['method']? ' _error':''?>" " type="text" placeholder="Select a Payment Method" readonly<?=$method!== null ? ' value="'. $method_db[$method]['name'] . '"' : ''?>>
                                    <div class="select-icon">
                                        <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.742422 0.276188C0.374922 0.632552 0.374922 1.2071 0.742422 1.56346L6.97492 7.6071C7.26742 7.89073 7.73992 7.89073 8.03242 7.6071L14.2649 1.56346C14.6324 1.2071 14.6324 0.632551 14.2649 0.276187C13.8974 -0.0801763 13.3049 -0.0801762 12.9374 0.276187L7.49992 5.54164L2.06242 0.268916C1.70242 -0.0801745 1.10242 -0.0801757 0.742422 0.276188Z" fill="#6E7B8B" />
                                        </svg>
                                    </div>
                                </div>
                                <ul class="select__options">
                                    <? foreach ($method_db as $k => $value) : ?>
                                        <li class="select__option">
                                            <label for="<?= "method_" . $value['method_id'] ?>">
                                                <?= $value['name'] ?>
                                            </label>
                                            <input type="radio" name="method" id="<?= "method_" . $value['method_id'] ?>" value="<?= $value['method_id'] ?>" hidden<?=$method !== null && $k == $method ? ' checked': ''?>>
                                        </li>
                                    <? endforeach ?>
                                </ul>

                            </div>
                        </div>
                        <label class="input__block input__block_file" for="photo">
                            <div class="input__title">
                                Cargo photo:
                            </div>
                            <div class="input__block_file_content input <?= $query['data']['photo'] ? ' _error' : null ?>">
                                <svg class="input__block_file_icon" width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.06 17.25C11.65 17.25 11.31 16.91 11.31 16.5V11.5C11.31 11.09 11.65 10.75 12.06 10.75C12.47 10.75 12.81 11.09 12.81 11.5V16.5C12.81 16.91 12.47 17.25 12.06 17.25Z" fill="#7F858E" />
                                    <path d="M14.5 14.75H9.5C9.09 14.75 8.75 14.41 8.75 14C8.75 13.59 9.09 13.25 9.5 13.25H14.5C14.91 13.25 15.25 13.59 15.25 14C15.25 14.41 14.91 14.75 14.5 14.75Z" fill="#7F858E" />
                                    <path d="M17 22.75H7C2.59 22.75 1.25 21.41 1.25 17V7C1.25 2.59 2.59 1.25 7 1.25H8.5C10.25 1.25 10.8 1.82 11.5 2.75L13 4.75C13.33 5.19 13.38 5.25 14 5.25H17C21.41 5.25 22.75 6.59 22.75 11V17C22.75 21.41 21.41 22.75 17 22.75ZM7 2.75C3.43 2.75 2.75 3.43 2.75 7V17C2.75 20.57 3.43 21.25 7 21.25H17C20.57 21.25 21.25 20.57 21.25 17V11C21.25 7.43 20.57 6.75 17 6.75H14C12.72 6.75 12.3 6.31 11.8 5.65L10.3 3.65C9.78 2.96 9.63 2.75 8.5 2.75H7V2.75Z" fill="#7F858E" />
                                </svg>
                                <span class="input__block_file_content_text">
                                    <?=strlen($photo) ? $photo : 'Click to add'?>
                                </span>
                            </div>
                            <? if ($query['data']['photo']) : ?>
                                <div class="_color-error">
                                    <?= $query['data']['photo'] ?>
                                </div>
                            <? endif; ?>
                            <input type="file" accept="<?= implode(',', $ALLOWED_IMAGE_TYPES) ?>" id="photo" name="photo" hidden>
                        </label>
                        <div class="input__block input__block_comment">
                            <label class="input__title" for="comment">
                                Comment:
                            </label>
                            <input class="input" type="text" id="comment" placeholder="Enter your comment about the cargo" name="comment" value="<?=$comment?>">
                        </div>
                        <button class="applicaton-create__form_create button" name="button_save">
                            Save
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