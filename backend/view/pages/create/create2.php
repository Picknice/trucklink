<?php
$size_db = getDbDate('size');
$size_db = $size_db ? array_column($size_db->fetch_all(MYSQLI_ASSOC), null, 'size_id') : [];
$method_db = getDbDate('method');
$method_db = $method_db ? array_column($method_db->fetch_all(MYSQLI_ASSOC), null, 'method_id') : [];

$loading_method = $_REQUEST['loading_method'];
$size = $_REQUEST['size'];
$cargo_size = $_REQUEST['cargo_size'];
$photo = $_FILES['photo'];
$mass = $_REQUEST['mass'];
$price = $_REQUEST['price'];
$method = $_REQUEST['method'];
$comment = $_REQUEST['comment'];
$quantity = $_REQUEST['quantity'];

$button_create = $_REQUEST['button_create'];

if($size != 4){
    $cargo_size = '';
}
if( !isset($_SESSION['application']) ){
    header("Location: /");
}else if( !isset($_SESSION['application']['telephone'])){
    header("Location: /create");
}
$recaptcha = false;
if(isset($_POST['g-recaptcha-response'])){
    $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, [
        'secret' => RECAPTCHA_PRIVATE,
        'response' => $_POST['g-recaptcha-response']
    ]);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($curl);
    if($res){
        $json = json_decode($res, true);
        $recaptcha = isset($json['success']) && $json['success'] == 1;
    }
}
if (isset($button_create) && $recaptcha) {
    if(!$quantity){
        $quantity = 1;
    }
    $query = ApplicationController::thirdCreate($loading_method, $size, $cargo_size, $photo, $mass, $price, $comment, $method, $quantity);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? require_once __DIR__ . './../../components/meta.php' ?>
    <? require_once __DIR__ . './../../components/style.php' ?>
    <title>Trucklink — Load information</title>
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
                            <a href="/create?page=1" class="applicaton-create__href link">
                                back
                            </a>
                        </div>
                        <div class="applicaton-create__title section-title">
                            Load information
                        </div>
                    </div>
                    <div class="applicaton-create__subtitle section-subtitle">
                        Please give us additional information that can help us get an accurate quote for you
                    </div>
                    <form class="applicaton-create__form" method="POST" enctype="multipart/form-data">
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
                              <input class="input-mask-number input<?=$query['data']['mass']? ' _error':''?>" type="number" id="mass" name="mass" placeholder="Enter the weight of the cargo" value="<?=$mass!=null?$mass:''?>">
                              <div class="input-meassure">lbs</div>
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
                                    Click to add
                                </span>
                            </div>
                            <? if ($query['data']['photo']) : ?>
                                <div class="_color-error">
                                    <?= $query['data']['photo'] ?>
                                </div>
                            <? endif; ?>
                            <input type="file" accept="<?= implode(',', $ALLOWED_IMAGE_TYPES) ?>" id="photo" name="photo" hidden>
                        </label>
                        <div class="select">
                            <div class="select__title">
                                Driver assistance in loading:
                            </div>
                            <div class="select__content">
                                <div class="select-block">
                                    <input class="select__input<?= $query['data']['loading_method'] ? ' _error' : '' ?>" type="text" placeholder="Choose an option"<?=$loading_method!==null?' value="'.($loading_method==='1'?'Yes': 'No').'"':''?> readonly>
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
                        <div class="input__block input__block__price" <?=$method!='3'? 'style="display: none;"':''?>>
                            <label class="input__title" for="price">
                                Price:
                            </label>
                            <div class="input-wrapper input-wrapper-price">
                                <div class="go-back">back</div>
                                <input class="input-mask-number input<?=$query['data']['price']? ' _error':''?>" type="number" id="price" placeholder="Enter cost" name="price" value="<?=$price?>">
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
                        <div class="input__block input__block_comment">
                            <label class="input__title" for="comment">
                                Comment:
                            </label>
                            <input class="input" type="text" id="comment" placeholder="Enter your comment about the cargo" name="comment" value="<?=$comment?>">
                        </div>
                        <div class="recaptcha">
                            <div class="g-recaptcha<?=isset($button_create) && !$recaptcha ? ' _error':''?>" data-theme="dark" data-sitekey="<?=RECAPTCHA_PUBLIC?>"></div>
                        </div>
                        <button class="applicaton-create__form_create button" name="button_create">
                            Next step: Finish
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