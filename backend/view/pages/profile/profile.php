<?
$user = getUser();
if(!$user){
    header('Location: /login');
}
$user = db("SELECT * FROM user WHERE user_id=?", $user['user_id'])->fetch_assoc();
$user_id = $user['user_id'];
$application_sql = "WHERE `is_deleted` = 0 AND `user_id` = '$user_id' ORDER BY `application_id` DESC LIMIT 10";

$applications = Application::get($application_sql);
$status_db = getDbDate('status')->fetch_all(MYSQLI_ASSOC);

$transport_type_db = getDbDate('transport_type');
$method_db = getDbDate('method');

foreach($status_db as &$row){
    $row['icon'] = getStatusIcon($row['name']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? require_once __DIR__ . './../../components/meta.php' ?>
    <? require_once __DIR__ . './../../components/style.php' ?>
    <title>Trucklink — Profile</title>
</head>

<body>
    <div class="wrapper personal-applications">
        <? require_once __DIR__ . './../../components/header.php'; ?>
        <main class="main">
            <div class="container">
                <div class="personal-applications__main_container">
                    <section class="user-info">
                        <? require_once __DIR__ . './../../components/profile_avatar.php'; ?>
                        <div class="user-info__user section-title">
                            <?= $user['name'] . " " . $user['surname'] ?>
                        </div>
                        <div class="user-info__greet">
                            Hello!
                        </div>
                    </section>
                    <div class="personal-applications__buttons application__buttons">
                        <a class="personal-applications_profile button-outline" href="/profile_info">
                            <svg width="1.5rem" height="1.375rem" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 18V16.3333C19 15.4493 18.6313 14.6014 17.9749 13.9763C17.3185 13.3512 16.4283 13 15.5 13H8.5C7.57174 13 6.6815 13.3512 6.02513 13.9763C5.36875 14.6014 5 15.4493 5 16.3333V18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12.5 9C14.433 9 16 7.433 16 5.5C16 3.567 14.433 2 12.5 2C10.567 2 9 3.567 9 5.5C9 7.433 10.567 9 12.5 9Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>
                                My profile
                            </span>
                        </a>
                        <a class="personal-applications_create button" href="/">
                            <svg width="1.56rem" height="1.5rem" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.5 22C18.0228 22 22.5 17.5228 22.5 12C22.5 6.47715 18.0228 2 12.5 2C6.97715 2 2.5 6.47715 2.5 12C2.5 17.5228 6.97715 22 12.5 22Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M12.5 8V16" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8.5 12H16.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>
                                Add cargo
                            </span>
                        </a>
                    </div>
                    <div id="cargo" class="tabless">
                        <div class="filters-xs">
                            <svg width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.4015 0H0.841662C0.162812 0 -0.236236 0.765086 0.152723 1.32181C0.160211 1.33242 -0.0777558 1.00836 5.70839 8.88432C5.83819 9.07212 5.90678 9.29188 5.90678 9.52025V17.1109C5.90678 17.849 6.75293 18.2561 7.3267 17.822L9.79299 15.9628C10.1333 15.7101 10.3364 15.3065 10.3364 14.8825V9.52025C10.3364 9.29188 10.4049 9.07212 10.5347 8.88432C16.3164 1.01441 16.0829 1.33235 16.0904 1.32181C16.4792 0.765332 16.0806 0 15.4015 0V0ZM9.6807 8.26541C9.44161 8.59085 9.28169 9.04459 9.28169 9.52021V14.8824C9.28169 14.9743 9.23757 15.0618 9.16374 15.1164C9.09881 15.1642 9.5906 14.7946 6.96144 16.7765V9.52025C6.96144 9.07286 6.82598 8.64249 6.56974 8.27568C6.56337 8.26657 6.74193 8.50978 2.94374 3.33979H13.2994L9.6807 8.26541ZM14.0742 2.2851H2.16895L1.265 1.05466H14.9781L14.0742 2.2851Z" fill="white"/>
                            </svg>
                            Filter
                            <svg width="15" height="16" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.05982 5.59625L3.70407 1.10079C3.46907 0.903969 3.08832 0.903969 2.85272 1.10079C2.61771 1.29762 2.61771 1.61731 2.85272 1.81414L7.78365 5.9529L2.85331 10.0917C2.61831 10.2885 2.61831 10.6082 2.85331 10.8055C3.08832 11.0023 3.46966 11.0023 3.70467 10.8055L9.06041 6.31009C9.29181 6.1153 9.29181 5.79058 9.05982 5.59625Z" fill="white"/>
                            </svg>
                        </div>
                        <div class="filterss aniEl">
                            <div class="status">
                                <div class="select">
                                    <div class="select__content">
                                        <div class="select-block">
                                            <input class="select__input select__input_filter" placeholder="Status" type="text" readonly>
                                            <div class="select-icon">
                                                <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.742422 0.276188C0.374922 0.632552 0.374922 1.2071 0.742422 1.56346L6.97492 7.6071C7.26742 7.89073 7.73992 7.89073 8.03242 7.6071L14.2649 1.56346C14.6324 1.2071 14.6324 0.632551 14.2649 0.276187C13.8974 -0.0801763 13.3049 -0.0801762 12.9374 0.276187L7.49992 5.54164L2.06242 0.268916C1.70242 -0.0801745 1.10242 -0.0801757 0.742422 0.276188Z" fill="#6E7B8B" />
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="select__options">
                                            <? foreach ($status_db as $value) : ?>
                                                <li class="select__option application-filter_select__option status_option">
                                                    <label for="<?= "status_" . $value['status_id'] ?>">
                                                        <?=$value['icon']?> <?= $value['name'] ?>
                                                    </label>
                                                    <input type="radio" name="status" id="<?= "status_" . $value['status_id'] ?>" value="<?= $value['status_id'] ?>" hidden>
                                                </li>
                                            <? endforeach ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="city__from">
                                <div class="city__inner">
                                    <div class="cit_from">
                                        <svg width="1.125rem" height="1.125rem" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.25 8.25L16.5 1.5L9.75 15.75L8.25 9.75L2.25 8.25Z" stroke="#6E7B8B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <input class="input__city _city addres-search _from_text" placeholder="Sending city" type="text">
                                        <input class="input-block__input addres-search _from" type="text" name="from" hidden>
                                    </div>
                                    <span>&#8594;</span>
                                    <div class="cit_to">
                                        <svg width="1.125rem" height="1.125rem" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.25 8.25L16.5 1.5L9.75 15.75L8.25 9.75L2.25 8.25Z" stroke="#6E7B8B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <input class="input__city1 addres-search _to_text _city" placeholder="Delivery city" type="text">
                                        <input class="addres-search _to" type="text" name="to" hidden>
                                    </div>
                                </div>
                            </div>
                            <div class="select__dates">
                                <div class="dates__inner">
                                    <div class="calendar-form">
                                        <div class="calendar-from__active">
                                            <div class="calendar-badge">1</div>
                                            <svg width="19" height="17" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M15.9736 3.75C15.9736 3.33579 15.6378 3 15.2236 3C14.8094 3 14.4736 3.33579 14.4736 3.75V4.4501H9.52832V3.75C9.52832 3.33579 9.19253 3 8.77832 3C8.36411 3 8.02832 3.33579 8.02832 3.75V4.4501H6.36133C5.13352 4.4501 4 5.36273 4 6.65024V9.55058V16.8012C4 18.0887 5.13352 19.0013 6.36133 19.0013H17.6406C18.8684 19.0013 20.002 18.0887 20.002 16.8012V9.55058V6.65024C20.002 5.36273 18.8684 4.4501 17.6406 4.4501H15.9736V3.75ZM18.502 8.80058V6.65024C18.502 6.33596 18.1927 5.9501 17.6406 5.9501H15.9736V6.65027C15.9736 7.06449 15.6378 7.40027 15.2236 7.40027C14.8094 7.40027 14.4736 7.06449 14.4736 6.65027V5.9501H9.52832V6.65027C9.52832 7.06449 9.19253 7.40027 8.77832 7.40027C8.36411 7.40027 8.02832 7.06449 8.02832 6.65027V5.9501H6.36133C5.80932 5.9501 5.5 6.33596 5.5 6.65024V8.80058H18.502ZM5.5 10.3006H18.502V16.8012C18.502 17.1155 18.1927 17.5013 17.6406 17.5013H6.36133C5.80932 17.5013 5.5 17.1155 5.5 16.8012V10.3006Z" fill="white" />
                                            </svg>
                                            <input class="input input_dates" type="hidden" name="begin_date" value="">
                                            <input class="input input_dates" type="hidden" name="end_date" value="">
                                        </div>
                                        <? include_once("$ROOT/view/components/calendar.php"); ?> 
                                    </div>
                                </div>
                            </div>
                            <div class="select__cars">
                                <div class="select">
                                    <div class="select__content">
                                        <div class="select-block">
                                            <input class="select__input select__input_filter" type="text" placeholder="Vehicle size" readonly>
                                            <div class="select-icon">
                                                <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.742422 0.276188C0.374922 0.632552 0.374922 1.2071 0.742422 1.56346L6.97492 7.6071C7.26742 7.89073 7.73992 7.89073 8.03242 7.6071L14.2649 1.56346C14.6324 1.2071 14.6324 0.632551 14.2649 0.276187C13.8974 -0.0801763 13.3049 -0.0801762 12.9374 0.276187L7.49992 5.54164L2.06242 0.268916C1.70242 -0.0801745 1.10242 -0.0801757 0.742422 0.276188Z" fill="#6E7B8B" />
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="select__options">
                                            <? foreach ($transport_type_db as $value) : ?>
                                                <li class="select__option application-filter_select__option">
                                                    <label for="<?= "transport_type_" . $value['transport_type_id'] ?>">
                                                        <?= $value['name'] ?>
                                                    </label>
                                                    <input type="radio" name="transport_type" id="<?= "transport_type_" . $value['transport_type_id'] ?>" value="<?= $value['transport_type_id'] ?>" hidden>
                                                </li>
                                            <? endforeach ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="payments_filter">
                                <div class="select">
                                    <div class="select__content">
                                        <div class="select-block select-block__payment-status">
                                            <input class="select__input select__input_filter _price" type="text" placeholder="Payment method" readonly>
                                            <div class="select-icon">
                                                <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.742422 0.276188C0.374922 0.632552 0.374922 1.2071 0.742422 1.56346L6.97492 7.6071C7.26742 7.89073 7.73992 7.89073 8.03242 7.6071L14.2649 1.56346C14.6324 1.2071 14.6324 0.632551 14.2649 0.276187C13.8974 -0.0801763 13.3049 -0.0801762 12.9374 0.276187L7.49992 5.54164L2.06242 0.268916C1.70242 -0.0801745 1.10242 -0.0801757 0.742422 0.276188Z" fill="#6E7B8B" />
                                                </svg>
                                            </div>
                                        </div>
                                        <ul class="select__options">
                                            <? foreach ($method_db as $value) : ?>
                                                <li class="select__option">
                                                    <label for="<?= "method_" . $value['method_id'] ?>">
                                                        <?= $value['name'] ?>
                                                    </label>
                                                    <input type="radio" name="method" id="<?= "method_" . $value['method_id'] ?>" value="<?= $value['method_id'] ?>" hidden>
                                                </li>
                                            <? endforeach ?>
                                            <? /*
                                            <? foreach ($price_select as $value) : ?>
                                                <? foreach ($price_select as $value) : ?>
                                                    <li class="select__option">
                                                        <label for="<?= "price_" . $value['id'] ?>">
                                                            <?= $value['name'] ?>
                                                        </label>
                                                        <input type="radio" name="price" id="<?= "price_" . $value['id'] ?>" value="<?= $value['id'] ?>" hidden>
                                                    </li>
                                                <? endforeach ?>
                                            <? endforeach ?>
                                            */ ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="payments_filter-price">
                                <span class="price-prefix">$</span>
                                <input type="number" class="price-from input-mask-number" placeholder="From">
                                <span class="price-prefix">$</span>
                                <input type="number" class="price-to input-mask-number" placeholder="To">
                                <div class="price-close">X</div>
                            </div>
                            <div class="filters_clear">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.9375 12.4375H1.1875M16.8125 9.9375C16.8125 13.6875 13.315 16.8125 9 16.8125C4.3125 16.8125 1.1875 12.4375 1.1875 12.4375V16.8125M13.0625 5.5625H16.8125M1.1875 8.0625C1.1875 4.3125 4.685 1.1875 9 1.1875C13.6875 1.1875 16.8125 5.5625 16.8125 5.5625V1.1875" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <button class="button__filter button">
                                <div class="button_fil_inner">
                                    <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.4998 10.0837C17.4998 14.4587 13.9582 18.0003 9.58317 18.0003C5.20817 18.0003 1.6665 14.4587 1.6665 10.0837C1.6665 5.70866 5.20817 2.16699 9.58317 2.16699" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M18.3332 18.8337L16.6665 17.167" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12.0833 5.60824C11.7916 4.69157 12.1333 3.5499 13.0999 3.24157C13.6083 3.0749 14.2333 3.21657 14.5916 3.70824C14.9249 3.1999 15.5749 3.08324 16.0749 3.24157C17.0416 3.5499 17.3833 4.69157 17.0916 5.60824C16.6333 7.06657 15.0333 7.8249 14.5916 7.8249C14.1416 7.8249 12.5583 7.08324 12.0833 5.60824Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span>Apply</span>
                                </div>
                            </button>
                        </div>
                        <div class="orders_table"></div>
                    </div>
                </div>
            </div>
        </main>
        <? require_once __DIR__ . './../../components/footer.php'; ?>
    </div>
    <? require_once __DIR__ . './../../components/script.php'; ?>
    <!-- Multiple date selections -->
    <!-- Our scripts -->
    <script src="/view/pages/profile/profile.js"></script>
    <link rel="stylesheet" href="/view/pages/profile/profile.css">
    <!-- Стили для Google Search -->
    <style>
      .pac-container {
        width: 300px !important;
      }
    </style>
</body>

</html>