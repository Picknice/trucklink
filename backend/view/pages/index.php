<?php
$from = $_REQUEST['from_checked'] ? $_REQUEST['from'] : null;
$to = $_REQUEST['to_checked'] ? $_REQUEST['to'] : null;
$date = $_REQUEST['date'];
$transport_type = $_REQUEST['transport_type'];

$create_button = $_REQUEST['create_button'];

if (isset($create_button)) {
    $query = ApplicationController::firstCreate($from, $to, $date, $transport_type);
}

$application_sql = "WHERE `is_deleted` = 0 ORDER BY `application_id` DESC LIMIT 10";

$applications = Application::get($application_sql);
$transport_type_db = getDbDate('transport_type');

$user = getUser();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? require_once __DIR__ . './../components/meta.php' ?>
    <? require_once __DIR__ . './../components/style.php' ?>
    <title>Trucklink — cargo service</title>
</head>

<body>
    <div class="wrapper">
        <header class="header header__main">
            <div class="container">
                <div class="header__container">
                    <div class="header__top">
                        <div class="header__menu">
                            <div class="header__burger">
                                <span></span>
                            </div>
                        </div>
                        <div class="header__logo">
                            <a href="/">
                                <svg width="7.437rem" height="2.125rem" viewBox="0 0 119 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M28.2848 9.90909V12.2955H20.7592V9.90909H28.2848ZM22.6172 6.77273H25.7024V19.0625C25.7024 19.4773 25.7649 19.7955 25.8899 20.017C26.0206 20.233 26.1911 20.3807 26.4013 20.4602C26.6115 20.5398 26.8445 20.5795 27.1002 20.5795C27.2933 20.5795 27.4695 20.5653 27.6286 20.5369C27.7933 20.5085 27.9183 20.483 28.0036 20.4602L28.5235 22.8722C28.3587 22.929 28.1229 22.9915 27.8161 23.0597C27.5149 23.1278 27.1456 23.1676 26.7081 23.179C25.9354 23.2017 25.2394 23.0852 24.62 22.8295C24.0007 22.5682 23.5092 22.1648 23.1456 21.6193C22.7877 21.0739 22.6115 20.392 22.6172 19.5739V6.77273ZM30.5051 23V9.90909H33.4965V12.0909H33.6329C33.8715 11.3352 34.2806 10.7528 34.8602 10.3438C35.4454 9.92898 36.113 9.72159 36.863 9.72159C37.0335 9.72159 37.2238 9.73011 37.434 9.74716C37.65 9.75852 37.8289 9.77841 37.971 9.80682V12.6449C37.8403 12.5994 37.6329 12.5597 37.3488 12.5256C37.0704 12.4858 36.8005 12.4659 36.5392 12.4659C35.9767 12.4659 35.471 12.5881 35.0221 12.8324C34.5789 13.071 34.2295 13.4034 33.9738 13.8295C33.7181 14.2557 33.5903 14.7472 33.5903 15.304V23H30.5051ZM48.0044 17.4943V9.90909H51.0897V23H48.0982V20.6733H47.9618C47.6664 21.4062 47.1806 22.0057 46.5044 22.4716C45.834 22.9375 45.0073 23.1705 44.0243 23.1705C43.1664 23.1705 42.4079 22.9801 41.7488 22.5994C41.0954 22.2131 40.584 21.6534 40.2147 20.9205C39.8454 20.1818 39.6607 19.2898 39.6607 18.2443V9.90909H42.7459V17.767C42.7459 18.5966 42.9732 19.2557 43.4277 19.7443C43.8823 20.233 44.4789 20.4773 45.2175 20.4773C45.6721 20.4773 46.1124 20.3665 46.5385 20.1449C46.9647 19.9233 47.3141 19.5938 47.5868 19.1562C47.8652 18.7131 48.0044 18.1591 48.0044 17.4943ZM59.6125 23.2557C58.3057 23.2557 57.1835 22.9688 56.246 22.3949C55.3142 21.821 54.5954 21.0284 54.0898 20.017C53.5898 19 53.3398 17.8295 53.3398 16.5057C53.3398 15.1761 53.5954 14.0028 54.1068 12.9858C54.6182 11.9631 55.3398 11.1676 56.2716 10.5994C57.2091 10.0256 58.317 9.73864 59.5954 9.73864C60.6579 9.73864 61.5983 9.93466 62.4165 10.3267C63.2403 10.7131 63.8966 11.2614 64.3852 11.9716C64.8738 12.6761 65.1523 13.5 65.2204 14.4432H62.2716C62.1523 13.8125 61.8682 13.2869 61.4193 12.8665C60.9761 12.4403 60.3824 12.2273 59.6381 12.2273C59.0074 12.2273 58.4534 12.3977 57.9761 12.7386C57.4988 13.0739 57.1267 13.5568 56.8596 14.1875C56.5983 14.8182 56.4676 15.5739 56.4676 16.4545C56.4676 17.3466 56.5983 18.1136 56.8596 18.7557C57.121 19.392 57.4875 19.8835 57.9591 20.2301C58.4363 20.571 58.996 20.7415 59.6381 20.7415C60.0926 20.7415 60.4988 20.6562 60.8568 20.4858C61.2204 20.3097 61.5244 20.0568 61.7687 19.7273C62.0131 19.3977 62.1807 18.9972 62.2716 18.5256H65.2204C65.1466 19.4517 64.8738 20.2727 64.4023 20.9886C63.9307 21.6989 63.2886 22.2557 62.4761 22.6591C61.6636 23.0568 60.7091 23.2557 59.6125 23.2557ZM70.2063 18.892L70.1978 15.1676H70.6921L75.3967 9.90909H79.0018L73.2148 16.3523H72.5756L70.2063 18.892ZM67.3938 23V5.54545H70.479V23H67.3938ZM75.6097 23L71.3484 17.0426L73.4279 14.8693L79.3001 23H75.6097ZM83.7831 5.54545V23H80.6979V5.54545H83.7831ZM86.5957 23V9.90909H89.6809V23H86.5957ZM88.1468 8.05114C87.6582 8.05114 87.2377 7.8892 86.8855 7.56534C86.5332 7.23579 86.3571 6.84091 86.3571 6.38068C86.3571 5.91477 86.5332 5.51989 86.8855 5.19602C87.2377 4.86648 87.6582 4.7017 88.1468 4.7017C88.6411 4.7017 89.0616 4.86648 89.4082 5.19602C89.7605 5.51989 89.9366 5.91477 89.9366 6.38068C89.9366 6.84091 89.7605 7.23579 89.4082 7.56534C89.0616 7.8892 88.6411 8.05114 88.1468 8.05114ZM95.5787 15.3295V23H92.4935V9.90909H95.4424V12.1335H95.5958C95.8969 11.4006 96.377 10.8182 97.0361 10.3864C97.7009 9.95455 98.5219 9.73864 99.4992 9.73864C100.403 9.73864 101.19 9.93182 101.86 10.3182C102.536 10.7045 103.059 11.2642 103.428 11.9972C103.803 12.7301 103.988 13.6193 103.982 14.6648V23H100.897V15.142C100.897 14.267 100.67 13.5824 100.215 13.0881C99.7662 12.5937 99.1441 12.3466 98.3486 12.3466C97.8088 12.3466 97.3287 12.4659 96.9083 12.7045C96.4935 12.9375 96.1668 13.2756 95.9282 13.7188C95.6952 14.1619 95.5787 14.6989 95.5787 15.3295ZM109.571 18.892L109.562 15.1676H110.057L114.761 9.90909H118.366L112.58 16.3523H111.94L109.571 18.892ZM106.759 23V5.54545H109.844V23H106.759ZM114.974 23L110.713 17.0426L112.793 14.8693L118.665 23H114.974Z" fill="white" />
                                    <path d="M22.855 25.447V21.2835C22.855 20.389 22.0967 19.6637 21.1614 19.6637H20.7186V4.4637C20.7186 4.32773 20.6473 4.20085 20.5287 4.12589L19.4537 3.4463C19.1722 3.26837 18.7969 3.46139 18.7969 3.78411V8.66582L18.4765 7.83932C18.0457 6.72773 16.9371 5.99 15.6975 5.99H8.29594C7.04909 5.99 5.9356 6.73629 5.51007 7.85719L5.20309 8.66582V3.78411C5.20309 3.46143 4.82774 3.26837 4.54622 3.4463L3.47115 4.12585C3.35256 4.2008 3.2812 4.32768 3.2812 4.46366V19.6637H2.8385C1.90316 19.6637 1.1449 20.3889 1.1449 21.2835V25.447H0.423364C0.189539 25.447 -1.90735e-06 25.6283 -1.90735e-06 25.852V28.0906C-1.90735e-06 28.3143 0.189539 28.4956 0.423364 28.4956H2.24995V29.5686C2.24995 30.0159 2.62908 30.3785 3.09678 30.3785H5.34069C5.80838 30.3785 6.18751 30.0159 6.18751 29.5686V28.4956H17.8125V29.5686C17.8125 30.0159 18.1917 30.3785 18.6593 30.3785H20.9032C21.3709 30.3785 21.75 30.0159 21.75 29.5686V28.4956H23.5766C23.8105 28.4956 24 28.3143 24 28.0906V25.852C24 25.6283 23.8105 25.4471 23.5766 25.4471L22.855 25.447ZM5.76405 10.9215H18.2359V14.9115H5.76405V10.9215ZM5.47404 24.3138H4.0276C3.58564 24.3138 3.22736 23.9711 3.22736 23.5484V23.5291C3.22736 23.1064 3.58564 22.7637 4.0276 22.7637H5.474C5.91595 22.7637 6.27423 23.1064 6.27423 23.5291V23.5484C6.27428 23.9711 5.916 24.3138 5.47404 24.3138ZM14.8783 24.5395H9.12164C8.65394 24.5395 8.27481 24.1768 8.27481 23.7296V18.598C8.27481 18.1508 8.65394 17.7882 9.12164 17.7882H14.8783C15.3459 17.7882 15.725 18.1508 15.725 18.598V23.7296C15.7251 24.1768 15.3459 24.5395 14.8783 24.5395ZM19.9723 24.3138H18.5259C18.084 24.3138 17.7256 23.9711 17.7256 23.5484V23.5291C17.7256 23.1064 18.084 22.7637 18.5259 22.7637H19.9723C20.4142 22.7637 20.7725 23.1064 20.7725 23.5291V23.5484C20.7725 23.9711 20.4142 24.3138 19.9723 24.3138ZM14.7495 20.7521H9.23682C9.0053 20.7521 8.81765 20.5726 8.81765 20.3512V20.343C8.81765 20.1216 9.0053 19.9422 9.23682 19.9422H14.7495C14.981 19.9422 15.1687 20.1216 15.1687 20.343V20.3512C15.1687 20.5726 14.981 20.7521 14.7495 20.7521ZM14.7495 19.1323H9.23682C9.0053 19.1323 8.81765 18.9528 8.81765 18.7314V18.7233C8.81765 18.5019 9.0053 18.3224 9.23682 18.3224H14.7495C14.981 18.3224 15.1687 18.5019 15.1687 18.7233V18.7314C15.1687 18.9528 14.981 19.1323 14.7495 19.1323ZM14.7495 23.9917H9.23682C9.0053 23.9917 8.81765 23.8122 8.81765 23.5908V23.5827C8.81765 23.3613 9.0053 23.1818 9.23682 23.1818H14.7495C14.981 23.1818 15.1687 23.3613 15.1687 23.5827V23.5908C15.1687 23.8122 14.981 23.9917 14.7495 23.9917ZM14.7495 22.3719H9.23682C9.0053 22.3719 8.81765 22.1924 8.81765 21.971V21.9629C8.81765 21.7415 9.0053 21.562 9.23682 21.562H14.7495C14.981 21.562 15.1687 21.7415 15.1687 21.9629V21.971C15.1687 22.1924 14.981 22.3719 14.7495 22.3719Z" fill="white" />
                                    <circle cx="49" cy="26.3783" r="3" stroke="white" stroke-width="2" />
                                    <circle cx="88" cy="26.3783" r="3" stroke="white" stroke-width="2" />
                                    <path d="M100 26.3783C100 28.0352 98.6569 29.3783 97 29.3783C95.3431 29.3783 94 28.0352 94 26.3783C94 24.7214 95.3431 23.3783 97 23.3783C98.6569 23.3783 100 24.7214 100 26.3783Z" stroke="white" stroke-width="2" />
                                </svg>
                            </a>
                            <span class="header-phone"><?=CONTACT_PHONE?></span>
                        </div>
                        <? require_once __DIR__ . './../components/header_log.php' ?>
                    </div>
                    <div class="header__center aniEl">
                        <h1 class="header__title section-title">
                            Get a freight shipping quote in seconds
                        </h1>
                        <div class="header__slider">
                            <div class="header__slider_arrow">
                                <svg width="0.375rem" height="0.625rem" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 1L1 5L5 9" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="header__slider_content">
                                <ul class="header__slider_list">
                                    <li class="header__slider_item">
                                        <svg width="1.875rem" height="1.875rem" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M26.25 14.375C26.2543 16.0249 25.8688 17.6524 25.125 19.125C24.243 20.8897 22.8872 22.374 21.2093 23.4116C19.5314 24.4493 17.5978 24.9993 15.625 25C13.9752 25.0043 12.3476 24.6189 10.875 23.875L3.75 26.25L6.125 19.125C5.38116 17.6524 4.9957 16.0249 5 14.375C5.00076 12.4022 5.55076 10.4686 6.5884 8.79072C7.62603 7.11285 9.11032 5.75699 10.875 4.87504C12.3476 4.1312 13.9752 3.74573 15.625 3.75004H16.25C18.8554 3.89378 21.3163 4.99349 23.1614 6.83861C25.0065 8.68373 26.1063 11.1446 26.25 13.75V14.375Z" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span>
                                            Get a quote
                                        </span>
                                    </li>
                                    <li class="header__slider_item">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M13.3999 17.4201H10.8899C9.24991 17.4201 7.91991 16.0401 7.91991 14.3401C7.91991 13.9301 8.25991 13.5901 8.66991 13.5901C9.07991 13.5901 9.41991 13.9301 9.41991 14.3401C9.41991 15.2101 10.0799 15.9201 10.8899 15.9201H13.3999C14.0499 15.9201 14.5899 15.3401 14.5899 14.6401C14.5899 13.7701 14.2799 13.6001 13.7699 13.4201L9.73991 12.0001C8.95991 11.7301 7.90991 11.1501 7.90991 9.36008C7.90991 7.82008 9.11991 6.58008 10.5999 6.58008H13.1099C14.7499 6.58008 16.0799 7.96008 16.0799 9.66008C16.0799 10.0701 15.7399 10.4101 15.3299 10.4101C14.9199 10.4101 14.5799 10.0701 14.5799 9.66008C14.5799 8.79008 13.9199 8.08008 13.1099 8.08008H10.5999C9.94991 8.08008 9.40991 8.66008 9.40991 9.36008C9.40991 10.2301 9.71991 10.4001 10.2299 10.5801L14.2599 12.0001C15.0399 12.2701 16.0899 12.8501 16.0899 14.6401C16.0799 16.1701 14.8799 17.4201 13.3999 17.4201Z" fill="white"/>
                                            <path d="M12 18.75C11.59 18.75 11.25 18.41 11.25 18V6C11.25 5.59 11.59 5.25 12 5.25C12.41 5.25 12.75 5.59 12.75 6V18C12.75 18.41 12.41 18.75 12 18.75Z" fill="white"/>
                                            <path d="M12 22.75C6.07 22.75 1.25 17.93 1.25 12C1.25 6.07 6.07 1.25 12 1.25C17.93 1.25 22.75 6.07 22.75 12C22.75 17.93 17.93 22.75 12 22.75ZM12 2.75C6.9 2.75 2.75 6.9 2.75 12C2.75 17.1 6.9 21.25 12 21.25C17.1 21.25 21.25 17.1 21.25 12C21.25 6.9 17.1 2.75 12 2.75Z" fill="white"/>
                                        </svg>
                                        <span>
                                            Best pricing
                                        </span>
                                    </li>
                                    <li class="header__slider_item">
                                        <svg width="1.875rem" height="1.875rem" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.5 13.75L26.25 2.5L15 26.25L12.5 16.25L2.5 13.75Z" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span>
                                           Live shipment visibility
                                        </span>
                                    </li>
                                </ul>
                            </div>
                            <div class="header__slider_arrow _right">
                                <svg width="0.375rem" height="0.625rem" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L5 5L1 9" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="header__subtitle section-subtitle">
                            Just fill out the information in the form below, the quote is a moment away
                        </div>
                    </div>
                    <div class="header__bottom aniEl">
                        <form class="header__form" method="POST">
                            <table class="header__table-form">
                                <tbody>
                                    <tr class="input-block__title">
                                        <th>
                                            Enter address
                                        </th>
                                        <th></th>
                                        <th>
                                            Date of download
                                        </th>
                                        <th>
                                            Vehicle size
                                        </th>
                                    </tr>
                                    <tr class="input-block__item">
                                        <td>
                                            <div class="input__block">
                                                <div class="input-block__content">
                                                    <svg class="inpImg" width="1rem" height="1rem" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_39_896)">
                                                            <path d="M1 7.63158L15 1L8.36842 15L6.89474 9.10526L1 7.63158Z" stroke="#131A24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        </g>
                                                        <defs>
                                                    </svg>
                                                    <input class="input-block__input addres-search _from_text" type="text" placeholder="From" name="from" value="<?= $from ?>">
                                                    <input class="input-block__input addres-search _from" type="text" name="from_checked" hidden>
                                                </div>
                                                <? if ($query['data']['from']) : ?>
                                                    <div class="_color-error">
                                                        <?= $query['data']['from'] ?>
                                                    </div>
                                                <? endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input__block">
                                                <div class="input-block__content addres-search">
                                                    <svg class="inpImg" width="1rem" height="1rem" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g clip-path="url(#clip0_39_896)">
                                                            <path d="M1 7.63158L15 1L8.36842 15L6.89474 9.10526L1 7.63158Z" stroke="#131A24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        </g>
                                                        <defs>
                                                    </svg>
                                                    <input class="input-block__input addres-search _to_text" type="text" placeholder="To" name="to" value="<?= $to ?>">
                                                    <input class="input-block__input addres-search _to" type="text" name="to_checked" hidden>
                                                </div>
                                                <? if ($query['data']['to']) : ?>
                                                    <div class="_color-error">
                                                        <?= $query['data']['to'] ?>
                                                    </div>
                                                <? endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input__block">
                                                <div class="input-block__content">
                                                    <svg class="inpImg" width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.9736 3.75C15.9736 3.33579 15.6378 3 15.2236 3C14.8094 3 14.4736 3.33579 14.4736 3.75V4.4501H9.52832V3.75C9.52832 3.33579 9.19253 3 8.77832 3C8.36411 3 8.02832 3.33579 8.02832 3.75V4.4501H6.36133C5.13352 4.4501 4 5.36273 4 6.65024V9.55058V16.8012C4 18.0887 5.13352 19.0013 6.36133 19.0013H17.6406C18.8684 19.0013 20.002 18.0887 20.002 16.8012V9.55058V6.65024C20.002 5.36273 18.8684 4.4501 17.6406 4.4501H15.9736V3.75ZM18.502 8.80058V6.65024C18.502 6.33596 18.1927 5.9501 17.6406 5.9501H15.9736V6.65027C15.9736 7.06449 15.6378 7.40027 15.2236 7.40027C14.8094 7.40027 14.4736 7.06449 14.4736 6.65027V5.9501H9.52832V6.65027C9.52832 7.06449 9.19253 7.40027 8.77832 7.40027C8.36411 7.40027 8.02832 7.06449 8.02832 6.65027V5.9501H6.36133C5.80932 5.9501 5.5 6.33596 5.5 6.65024V8.80058H18.502ZM5.5 10.3006H18.502V16.8012C18.502 17.1155 18.1927 17.5013 17.6406 17.5013H6.36133C5.80932 17.5013 5.5 17.1155 5.5 16.8012V10.3006Z" fill="black" />
                                                    </svg>
                                                    <div class="calendar-form">
                                                        <div class="calendar-from__active">
                                                            <input class="input input-block__input" type="hidden" placeholder="Date" name="date" value="<?= $date ?>" readonly>
                                                            <input class="input input-block__input" type="text" placeholder="Date" name="fake-date" value="<?= $date ?>" readonly>
                                                        </div>
                                                        <? include_once("$ROOT/view/components/calendar.php"); ?> 
                                                    </div>
                                                </div>
                                                <? if ($query['data']['date']) : ?>
                                                    <div class="_color-error">
                                                        <?= $query['data']['date'] ?>
                                                    </div>
                                                <? endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input__block input-search__block">
                                                <div class="input-block__content">
                                                    <svg class="inpImg" width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M16 3H1V16H16V3Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M16 8H20L23 11V16H16V8Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M5.5 21C6.88071 21 8 19.8807 8 18.5C8 17.1193 6.88071 16 5.5 16C4.11929 16 3 17.1193 3 18.5C3 19.8807 4.11929 21 5.5 21Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M18.5 21C19.8807 21 21 19.8807 21 18.5C21 17.1193 19.8807 16 18.5 16C17.1193 16 16 17.1193 16 18.5C16 19.8807 17.1193 21 18.5 21Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <input class="input-block__input input-search__value" name="transport_type" type="text" placeholder="Vehicle size" readonly>
                                                    <ul class="input-search__list">
                                                        <? foreach ($transport_type_db as $value) : ?>
                                                            <li class="input-search__item">
                                                                <label for="transport_type_<?= $value['transport_type_id'] ?>">
                                                                    <?= $value['name'] ?>
                                                                </label>
                                                                <input type="radio" id="transport_type_<?= $value['transport_type_id'] ?>" value="<?= $value['transport_type_id'] ?>" name="transport_type">
                                                            </li>
                                                        <? endforeach ?>
                                                    </ul>
                                                </div>
                                                <? if ($query['data']['transport_type']) : ?>
                                                    <div class="_color-error">
                                                        <?= $query['data']['transport_type'] ?>
                                                    </div>
                                                <? endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="header__button button" name="create_button" onclick="gtag_report_conversion()">
                                                Proceed
                                            </button>
                                            <!-- Event snippet for Proceed to receiving a quote conversion page
In your html page, add the snippet and call gtag_report_conversion when someone clicks on the chosen link or button. -->
                                            <script>
                                                function gtag_report_conversion(url) {
                                                    var callback = function () {
                                                        if (typeof(url) != 'undefined') {
                                                            window.location = url;
                                                        }
                                                    };
                                                    gtag('event', 'conversion', {
                                                        'send_to': 'AW-536230408/qVP1CMD23oYYEIj02P8B',
                                                        'value': 100.0,
                                                        'currency': 'USD',
                                                        'event_callback': callback
                                                    });
                                                    return false;
                                                }
                                            </script>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="header__hiw">
                        <div class="head">how it works</div>
                        <div class="steps">
                            <div class="step">
                                <div class="step-row">
                                    <div class="step-name">1 — step</div>
                                    <div class="step-arrow">
                                        ..................
                                        <svg width="14" height="23" viewBox="0 0 14 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.917314 0.612687C1.05762 0.472027 1.22429 0.360429 1.40779 0.284284C1.5913 0.20814 1.78802 0.168945 1.98669 0.168945C2.18536 0.168945 2.38208 0.20814 2.56558 0.284284C2.74908 0.360429 2.91576 0.472027 3.05606 0.612687L13.0973 10.6539C13.2093 10.7657 13.2982 10.8985 13.3588 11.0447C13.4195 11.1909 13.4507 11.3476 13.4507 11.5058C13.4507 11.6641 13.4195 11.8208 13.3588 11.9669C13.2982 12.1131 13.2093 12.2459 13.0973 12.3577L3.05606 22.3989C2.46398 22.991 1.5094 22.991 0.917314 22.3989C0.32523 21.8069 0.32523 20.8523 0.917314 20.2602L9.66565 11.4998L0.905231 2.73935C0.325232 2.15935 0.32523 1.19269 0.917314 0.612687Z" fill="white"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="step-row">
                                    <div class="block">
                                        <div>Fill out pick up - delivery</div>
                                        <div>destination information</div>
                                    </div>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-row">
                                    <div class="step-name">2 — step</div>
                                    <div class="step-arrow">
                                        ..................
                                        <svg width="14" height="23" viewBox="0 0 14 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0.917314 0.612687C1.05762 0.472027 1.22429 0.360429 1.40779 0.284284C1.5913 0.20814 1.78802 0.168945 1.98669 0.168945C2.18536 0.168945 2.38208 0.20814 2.56558 0.284284C2.74908 0.360429 2.91576 0.472027 3.05606 0.612687L13.0973 10.6539C13.2093 10.7657 13.2982 10.8985 13.3588 11.0447C13.4195 11.1909 13.4507 11.3476 13.4507 11.5058C13.4507 11.6641 13.4195 11.8208 13.3588 11.9669C13.2982 12.1131 13.2093 12.2459 13.0973 12.3577L3.05606 22.3989C2.46398 22.991 1.5094 22.991 0.917314 22.3989C0.32523 21.8069 0.32523 20.8523 0.917314 20.2602L9.66565 11.4998L0.905231 2.73935C0.325232 2.15935 0.32523 1.19269 0.917314 0.612687Z" fill="white"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="step-row">
                                    <div class="block">
                                        <div>Fill out cargo</div>
                                        <div>information</div>
                                    </div>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-row">
                                    <div class="step-name">3 — Finish</div>
                                </div>
                                <div class="step-row">
                                    <div class="block">
                                        <div>Receive live quote from</div>
                                        <div>our trusted carriers</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <main class="main">
            <section class="recently-loads">
                <div class="container">
                    <div class="recently-loads__title section-title aniEl">
                        Recently added loads
                    </div>
                    <div class="recently-loads__subtitle section-subtitle aniEl">
                        Our service offerings include freight forwarding, international shipping and customs brokerage, freight transportation management,
                        warehousing services, and more.
                    </div>
                    <table class="application__table aniEl">
                        <tbody>
                            <tr class="application__item_title">
                                <th>
                                    Pickup<span style="margin-left: 40%;">Delivery</span>
                                </th>
                                <th>
                                    Date of download
                                </th>
                                <th>
                                    Transport
                                </th>
                                <th colspan="2">
                                    Quote
                                </th>
                            </tr>
                            <? foreach ($applications as $application) : ?>
                                <tr class="application__item">
                                    <td>
                                        <div class="application__route">
                                            <div class="application__way">
                                                <div class="application__from" data-title="<?= $application['from'] ?>">
                                                    <img class="application__flag" src="/view/static/img/flags/<?= wordLast($application['from']) ?>.png" alt="<?= wordLast($application['from']) ?>">
                                                    <span>
                                                    <?=$application['from']?>
                                                    </span>
                                                </div>
                                                <div class="info">from</div>
                                                <span class="application_info"></span>
                                                <span class="application__arrow">
                                                    →
                                                </span>
                                                <div class="application__to" data-title="<?= $application['to'] ?>">
                                                    <img class="application__flag" src="/view/static/img/flags/<?= wordLast($application['to']) ?>.png" alt="<?= wordLast($application['to']) ?>">
                                                    <span>
                                                        <?=$application['to']?>
                                                    </span>
                                                </div>
                                                <div class="info">to</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="application__date">
                                            <?= NormalizeView::date($application['date']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="application__transport">
                                            <?= getDbDate('transport_type', 'transport_type_id', $application['transport_type'])->fetch_assoc()['name'] ?>
                                        </div>
                                    </td>
                                    <td class="application__payment_td">
                                        <div class="application__payment">
                                            <?= NormalizeView::checkPrice($application['price'], $application['method']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a class="application__payment_link" href="/application?id=<?= $application['application_id'] ?>">
                                            <span class="for-pc">
                                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            Details
                                            <span class="for-xs">
                                                <svg width="22" height="17" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M0.625 5.5C0.625 5.5 3.125 0.5 7.5 0.5C11.875 0.5 14.375 5.5 14.375 5.5C14.375 5.5 11.875 10.5 7.5 10.5C3.125 10.5 0.625 5.5 0.625 5.5Z" stroke="#798293" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <path d="M7.5 7.375C8.53553 7.375 9.375 6.53553 9.375 5.5C9.375 4.46447 8.53553 3.625 7.5 3.625C6.46447 3.625 5.625 4.46447 5.625 5.5C5.625 6.53553 6.46447 7.375 7.5 7.375Z" stroke="#798293" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <svg width="22" height="17" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6.47632 5.64117L1.12058 1.14572C0.885572 0.948891 0.504823 0.948891 0.269223 1.14572C0.0342164 1.34254 0.0342164 1.66224 0.269223 1.85906L5.20016 5.99782L0.269817 10.1366C0.0348105 10.3334 0.0348105 10.6531 0.269817 10.8504C0.504824 11.0473 0.886165 11.0473 1.12117 10.8504L6.47692 6.35501C6.70831 6.16022 6.70831 5.8355 6.47632 5.64117Z" fill="#798293"/>
                                                </svg>
                                            </span>
                                        </a>
                                    </td>
                                </tr>
                                </tr>
                            <? endforeach ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <section class="transport-nearby">
                <div class="container">
                    <div class="transport-nearby__title section-title aniEl">
                        Available transport nearby
                    </div>
                    <div class="transport-nearby__subtitle section-subtitle aniEl">
                        Book a truck that works for you
                    </div>
                    <a class="transport-nearby__button button aniEl" href="https://cnu.cargoetl.com/map/">
                        Check capacity
                    </a>
                    <ul class="transport-nearby__list">
                        <li class="transport-nearby__item aniEl">
                            <div class="transport-nearby__icon">
                                <svg width="2.12rem" height="2.62rem" viewBox="0 0 34 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17 40C17 40 32 32.4 32 21V7.7L17 2L2 7.7V21C2 32.4 17 40 17 40Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="transport-nearby__item_title aniEl">
                                Guarantee
                            </div>
                            <div class="transport-nearby__item_subtitle aniEl">
                                Safe, reliable transportation of your cargo
                            </div>
                        </li>
                        <li class="transport-nearby__item aniEl">
                            <div class="transport-nearby__icon">
                                <svg width="3rem" height="3rem" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M31 9H6V31H31V9Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M31 17H37.2857L42 22.25V31H31V17Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M13 39C15.2091 39 17 37.2091 17 35C17 32.7909 15.2091 31 13 31C10.7909 31 9 32.7909 9 35C9 37.2091 10.7909 39 13 39Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M35 39C37.2091 39 39 37.2091 39 35C39 32.7909 37.2091 31 35 31C32.7909 31 31 32.7909 31 35C31 37.2091 32.7909 39 35 39Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="transport-nearby__item_title aniEl">
                                Extensive fleet
                            </div>
                            <div class="transport-nearby__item_subtitle aniEl">
                                Always find a truck for your needs
                            </div>
                        </li>
                        <li class="transport-nearby__item aniEl">
                            <div class="transport-nearby__icon">
                                <svg width="3rem" height="3rem" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7 35V23.8C7 19.3444 8.79107 15.0712 11.9792 11.9206C15.1673 8.77 19.4913 7 24 7C28.5087 7 32.8327 8.77 36.0208 11.9206C39.2089 15.0712 41 19.3444 41 23.8V35" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M41 37.2857C41 38.2708 40.602 39.2155 39.8935 39.9121C39.185 40.6087 38.2242 41 37.2222 41H35.3333C34.3314 41 33.3705 40.6087 32.662 39.9121C31.9536 39.2155 31.5556 38.2708 31.5556 37.2857V31.7143C31.5556 30.7292 31.9536 29.7845 32.662 29.0879C33.3705 28.3913 34.3314 28 35.3333 28H41V37.2857ZM7 37.2857C7 38.2708 7.39801 39.2155 8.10649 39.9121C8.81496 40.6087 9.77585 41 10.7778 41H12.6667C13.6686 41 14.6295 40.6087 15.338 39.9121C16.0464 39.2155 16.4444 38.2708 16.4444 37.2857V31.7143C16.4444 30.7292 16.0464 29.7845 15.338 29.0879C14.6295 28.3913 13.6686 28 12.6667 28H7V37.2857Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="transport-nearby__item_title aniEl">
                               Support
                            </div>
                            <div class="transport-nearby__item_subtitle aniEl">
                                24/7 support
                            </div>
                        </li>
                        <li class="transport-nearby__item aniEl">
                            <div class="transport-nearby__icon">
                                <svg width="3rem" height="3rem" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.1451 41.9998H9.65804C8.68787 41.9998 7.75743 41.6205 7.07142 40.9454C6.3854 40.2702 6 39.3546 6 38.3998V25.7999C6 24.8451 6.3854 23.9294 7.07142 23.2543C7.75743 22.5792 8.68787 22.1999 9.65804 22.1999H15.1451M27.9483 18.5999V11.4C27.9483 9.96781 27.3702 8.5943 26.3411 7.58161C25.3121 6.56892 23.9165 6 22.4612 6L15.1451 22.1999V41.9998H35.7765C36.6587 42.0096 37.5147 41.7053 38.187 41.143C38.8592 40.5807 39.3022 39.7982 39.4345 38.9398L41.9586 22.7399C42.0382 22.2239 42.0028 21.6971 41.8549 21.196C41.7071 20.6948 41.4503 20.2312 41.1023 19.8374C40.7544 19.4436 40.3236 19.129 39.8398 18.9153C39.356 18.7016 38.8308 18.594 38.3005 18.5999H27.9483Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="transport-nearby__item_title aniEl">
                                Reputation
                            </div>
                            <div class="transport-nearby__item_subtitle aniEl">
                                more than 15 years of successful work with customer reviews
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
            <section class="about-company">
                <div class="container">
                    <div class="about-company__title section-title aniEl">
                        About company
                    </div>
                    <div class="about-company__subtitle section-subtitle aniEl">
                        <p>The trucking industry is a vital part of the American economy, responsible for transporting billions of dollars' worth of goods each year. However, the industry has long been plagued by inefficiencies and high costs. Trucklink is a new state-of-the-art technology that promises to revolutionize the trucking industry by connecting shippers directly to shippers, eliminating brokers and lowering prices due to the absence of the middleman.</p>
                        <p>Trucklink is an online marketplace that allows shippers to find and book available trucks without the need for a broker. This direct connection between shipper and trucker eliminates the middleman and saves on costs. In addition, Trucklink offers a number of features that make it easy for shippers to find the right truck for their load. For example, shippers can search for trucks by location, weight capacity, and type of load. And once a trucker is booked, they can track their shipment in real-time using GPS tracking.</p>
                        <p>Trucklink is currently being piloted by a number of major companies in the trucking industry, and if successful, it has the potential to change the way goods are shipped across America.</p>
                    </div>
                    <!--
                    <ul class="about-company__people">
                        <li class="about-company__people_item aniEl">
                            <div class="about-company__people_img">
                                <img src="/view/static/img/company_people_1.jpg" alt="">
                            </div>
                            <div class="about-company__people_name">
                                Alex Mokhov
                            </div>
                            <div class="about-company__people_role">
                                <a href="mailto:alex@etlgroupllc.com">alex@etlgroupllc.com</a>
                            </div>
                        </li>
                        <li class="about-company__people_item aniEl">
                            <div class="about-company__people_img">
                                <img src="/view/static/img/company_people_2.png" alt="">
                            </div>
                            <div class="about-company__people_name">
                                Omon Safarov
                            </div>
                            <div class="about-company__people_role">
                                <a href="mailto:amon@etlgroupllc.com">amon@etlgroupllc.com</a>
                            </div>
                        </li>
                        <li class="about-company__people_item aniEl">
                            <div class="about-company__people_img">
                                <img src="/view/static/img/company_people_3.jpg" alt="">
                            </div>
                            <div class="about-company__people_name">
                                Sergey Perekrestow
                            </div>
                            <div class="about-company__people_role">
                                <a href="mailto:aideweb.host@gmail.com">aideweb.host@gmail.com</a>
                            </div>
                        </li>
                    </ul>
                    -->
                </div>
            </section>
            <section id="faq" class="frequent-questions">
                <div class="container">
                    <div class="frequent-questions__title section-title aniEl">
                        FAQ
                    </div>
                    <ul class="questions__list aniEl">
                        <li class="questions__item">
                            <div class="questions__item_question">
                                <div class="questions__item_title">
                                    What is Trucklink?
                                </div>
                                <svg width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="questions__item_answer">Trucklink is a truck-sharing network that connects truck owners with businesses and individuals who need to hire trucks for short-term projects. Benefits of using Trucklink include reduced costs, convenience, and access to a wide variety of trucks. To sign up for Trucklink, visit the website and create an account.</div>
                        </li>
                        <li class="questions__item">
                            <div class="questions__item_question">
                                <div class="questions__item_title">
                                    How do I use Trucklink?
                                </div>
                                <svg width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="questions__item_answer">
                                Once you have created an account, you can browse the available trucks in your area. Select the truck you want to hire and enter your pickup and drop-off locations. You will then be able to view the cost and schedule of the trip. Once you have confirmed the details, you can pay for the trip online.
                            </div>
                        </li>
                        <li class="questions__item">
                            <div class="questions__item_question">
                                <div class="questions__item_title">
                                    How much does Trucklink cost?
                                </div>
                                <svg width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="questions__item_answer">The cost of hiring a truck through Trucklink varies depending on the size and type of truck you need, as well as the distance between pickup and drop-off locations. You can get a quote for specific trips by entering your information on the Trucklink website.</div>
                        </li>
                        <li class="questions__item">
                            <div class="questions__item_question">
                                <div class="questions__item_title">
                                    Is Trucklink available in my area?
                                </div>
                                <svg width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="questions__item_answer">Trucklink is available in most major metropolitan areas in the United States. To check if it is available in your area, visit the website and enter your zip code or city name.</div>
                        </li>
                        <li class="questions__item">
                            <div class="questions__item_question">
                                <div class="questions__item_title">
                                    How do I contact Trucklink customer service?
                                </div>
                                <svg width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="questions__item_answer">If you have any questions or problems using Trucklink, please contact customer service by email or phone. Customer service representatives are available Monday through Friday from 9am to 5pm EST.</div>
                        </li>
                        <li class="questions__item">
                            <div class="questions__item_question">
                                <div class="questions__item_title">
                                    How much does Trucklink cost?
                                </div>
                                <svg width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6 9L12 15L18 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="questions__item_answer">
                                Trucklink is free for shippers, users who need to find short term contractors. We take percentage off of the total cost of the job that goes to the carrier.
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
            <? require_once __DIR__ . './../components/footer.php'; ?>
        </main>
    </div>
    <? require_once __DIR__ . './../components/script.php'; ?>
</body>

</html>