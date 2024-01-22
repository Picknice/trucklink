<?
$user = getUser();
if(!$user){
    $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'];
    header('Location: /login');
}
$application_id = (int) $_GET['id'];
$application = db()->query('SELECT a.*,tt.name as transport_type_name, sz.name as size_name, st.status_id as status_id, st.name as status, user.company_name as company_name FROM application as a LEFT JOIN transport_type as tt ON a.transport_type = tt.transport_type_id LEFT JOIN size as sz ON a.size = sz.size_id LEFT JOIN status as st ON a.status = st.status_id LEFT JOIN user ON user.user_id = a.user_id WHERE application_id=?', $application_id)->fetch_assoc();
if (!$application) {
    header('Location: /');
}
$applicationUser = getDbDate('user', 'user_id', $application['user_id'])->fetch_assoc();
$haveMap = $application['order_id'] && $application['pickup_geo'] && $application['deliver_geo'];
$needMap = isset($_GET['map']) && $haveMap;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <? require_once __DIR__ . './../components/meta.php' ?>
    <? require_once __DIR__ . './../components/style.php' ?>
    <title>Trucklink — Application</title>
</head>

<body>
    <div class="wrapper application-info">
        <? require_once __DIR__ . './../components/header.php'; ?>
        <main class="main">
            <div class="container">
                <div class="application-info__main_container">
                    <? if ($applicationUser) : ?>
                        <section class="user-info">
                            <? require_once __DIR__ . './../components/profile_avatar.php'; ?>
                            <div class="user-info__user section-title">
                                <?= $applicationUser['name'] . " " . $applicationUser['surname'] ?>
                            </div>
                            <div class="user-info__greet">
                                Hello!
                            </div>
                        </section>
                    <? endif ?>
                    <a class="application-info__link back-office__link link-color link" onclick="history.go(-1)">
                        <svg width="1.56rem" height="1.5rem" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.62988 19.2246H15.6299C18.891 19.2246 21.5449 16.5707 21.5449 13.3096C21.5449 10.0484 18.891 7.39457 15.6299 7.39457H4.62988C4.12876 7.39457 3.71488 7.80844 3.71488 8.30957C3.71488 8.8107 4.12876 9.22457 4.62988 9.22457H15.6299C17.8788 9.22457 19.7149 11.0607 19.7149 13.3096C19.7149 15.5584 17.8788 17.3946 15.6299 17.3946H7.62988C7.12876 17.3946 6.71488 17.8084 6.71488 18.3096C6.71488 18.8107 7.12876 19.2246 7.62988 19.2246Z" fill="white" stroke="white" stroke-width="0.33" />
                            <path d="M6.28339 11.4566C6.46596 11.6391 6.69866 11.7249 6.93006 11.7249C7.15705 11.7249 7.40265 11.6419 7.57873 11.4545C7.93117 11.0999 7.9305 10.517 7.57673 10.1632L5.6634 8.24988L7.57673 6.33656C7.93117 5.98212 7.93117 5.39765 7.57673 5.04321C7.22229 4.68877 6.63782 4.68877 6.28339 5.04321L3.72339 7.60321C3.36895 7.95765 3.36895 8.54212 3.72339 8.89656L6.28339 11.4566Z" fill="white" stroke="white" stroke-width="0.33" />
                        </svg>
                        <span>
                            Back to <?=($needMap ? 'application' : 'office') ?>
                        </span>
                    </a>
                    <section class="application-info__info info__list">
                        <div class="info-wrap show">
                            <? if (!$needMap): ?>
                            <div class="info__item mob-hide">
                                <div class="info__title">Status</div>
                                <div class="info__value">
                                    <div class="info__flex">
                                        <div class="status__block">
                                            <?=getStatusIcon($application['status'])?> <?= $application['status'] ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <? endif ?>
                            <div class="info__item">
                                <div class="info__title">
                                    Application for cargo from <?=$application['user_fullname']?>
                                </div>
                                <div class="info__value">
                                    <div class="info__flex">
                                        <div class="info__left">
                                            <div>ID application:</div>
                                            <? if(mb_strlen($application['company_name'])): ?><div>Company:</div><? endif ?>
                                        </div>
                                        <div class="info__right">
                                            <div><?= $application['application_id'] ?></div>
                                            <? if(mb_strlen($application['company_name'])): ?><div><?= $application['company_name']?></div><? endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <? if(!$needMap): ?>
                        <div class="details-xs show">
                            Details
                            <svg width="15" height="16" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.05982 5.59625L3.70407 1.10079C3.46907 0.903969 3.08832 0.903969 2.85272 1.10079C2.61771 1.29762 2.61771 1.61731 2.85272 1.81414L7.78365 5.9529L2.85331 10.0917C2.61831 10.2885 2.61831 10.6082 2.85331 10.8055C3.08832 11.0023 3.46966 11.0023 3.70467 10.8055L9.06041 6.31009C9.29181 6.1153 9.29181 5.79058 9.05982 5.59625Z" fill="white"></path>
                            </svg>
                        </div>
                        <div class="details-wrap show">
                            <div class="info__item info_delivery">
                                <div class="info__title">
                                    From — To
                                </div>
                                <div class="info__value delivery_wrap">
                                    <div class="info__from-to delivery_wrap">
                                        <div class="info__country delivery_from">
                                            <img class="application__flag" src="./view/static/img/flags/<?= wordLast($application['from']) ?>.png" alt="<?= wordLast($application['from']) ?>">
                                            <div>
                                                <?= $application['from'] ?>
                                            </div>
                                        </div>
                                        <div class="info">from</div>
                                        <div class="application_info"></div>
                                        <hr class="info__hr arrow">
                                        <div class="info__country delivery_to">
                                            <img class="application__flag" src="./view/static/img/flags/<?= wordLast($application['to']) ?>.png" alt="<?= wordLast($application['to']) ?>">
                                            <div>
                                                <?= $application['to'] ?>
                                            </div>
                                        </div>
                                        <div class="info">to</div>
                                    </div>
                                </div>
                            </div>
                            <div class="info__item info_date">
                                <div class="info__title">
                                    Date of download
                                </div>
                                <div class="info__value info_date_xs">
                                    <?= NormalizeView::date($application['date']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="info-xs show">
                            Info
                            <svg width="15" height="16" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.05982 5.59625L3.70407 1.10079C3.46907 0.903969 3.08832 0.903969 2.85272 1.10079C2.61771 1.29762 2.61771 1.61731 2.85272 1.81414L7.78365 5.9529L2.85331 10.0917C2.61831 10.2885 2.61831 10.6082 2.85331 10.8055C3.08832 11.0023 3.46966 11.0023 3.70467 10.8055L9.06041 6.31009C9.29181 6.1153 9.29181 5.79058 9.05982 5.59625Z" fill="white"></path>
                            </svg>
                        </div>
                        <div class="info-wrap show">
                            <div class="info__item">
                                <div class="info__title">
                                    Transport type
                                </div>
                                <div class="info__value">
                                    <div class="info__flex">
                                        <div class="info__left">
                                            <div class="hidden-pc">Transport type:</div>
                                        </div>
                                        <div class="info__right">
                                            <div><?= $application['transport_type_name'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="info__item">
                                <div class="info__title">
                                    Driver assistance in loading
                                </div>
                                <div class="info__value">
                                    <div class="info__flex">
                                        <div class="info__left">
                                            <div class="hidden-pc">Driver assistance in loading:</div>
                                        </div>
                                        <div class="info__right">
                                            <div><?= $application['loading_method'] ? 'Yes' : 'No' ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="info__item">
                                <div class="info__title">
                                    Payment
                                </div>
                                <div class="info__value">
                                    <div class="info__flex">
                                        <div class="info__left">
                                            <div class="hidden-pc">Payment:</div>
                                        </div>
                                        <div class="info__right">
                                            <div><?= NormalizeView::checkPrice($application['price'], $application['method']) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <? if ($application['size'] || $application['mass'] || $application['height']): ?>
                                <div class="info__item">
                                    <div class="info__title">
                                        Cargo dimensions:
                                    </div>
                                    <div class="info__value">
                                        <div class="info__flex">
                                            <div class="info__left">
                                                <? if ($application['size']) : ?>
                                                <div>Pallet size:</div>
                                                <div>Pallet quantity:</div>
                                                <? endif ?>
                                                <? if ($application['mass'] && strlen($application['mass'])) : ?>
                                                <div>Weight:</div>
                                                <? endif ?>
                                            </div>
                                            <div class="info__right">
                                                <? if ($application['size']) : ?>
                                                <div><?= $application['size'] == 4 ? $application['cargo_size'] : $application['size_name'] ?></div>
                                                <div><?= $application['quantity']?></div>
                                                <? endif ?>
                                                <? if ($application['mass'] && strlen($application['mass'])) : ?>
                                                <div><?= $application['mass'] ?> <sup>lbs</sup></div>
                                                <? endif ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? endif; ?>
                            <? if ($application['comment']) : ?>
                                <div class="info__item">
                                    <div class="info__title">
                                        Comment
                                    </div>
                                    <div class="info__value">
                                        <div class="info__flex">
                                            <div class="info__left">
                                                <div class="hidden-pc">Comment:</div>
                                            </div>
                                            <div class="info__right">
                                                <div><?= $application['comment'] ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? endif ?>
                            <? if ($application['order_id'] && ($application['pickup_date'] || $application['deliver_date'] || $application['miles'])): ?>
                                <div class="info__item">
                                    <div class="info__title">Tracking</div>
                                    <div class="info__value">
                                        <div class="info__flex">
                                            <div class="info__left">
                                                <? if ($application['pickup_date']): ?>
                                                <div>Loading date:</div>
                                                <? endif ?>
                                                <? if ($application['deliver_date']): ?>
                                                <div><?=($application['status'] >= 9 ? 'Delivered date:' : 'Delivery date:')?></div>
                                                <? endif ?>
                                                <? if ($application['miles']): ?>
                                                <div>Route length:</div>
                                                <? endif ?>
                                            </div>
                                            <div class="info__right">
                                                <? if ($application['pickup_date']): ?>
                                                    <div><?=$application['pickup_date']?></div>
                                                <? endif ?>
                                                <? if ($application['deliver_date']): ?>
                                                    <div><?=$application['deliver_date']?></div>
                                                <? endif ?>
                                                <? if ($application['miles']): ?>
                                                    <div><?=$application['miles']?> miles</div>
                                                <? endif ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? endif ?>
                        </div>
                        <? endif ?>
                        <? if($needMap): ?>
                            <div id="application-route-map" data-origin="<?=$application['pickup_geo']?>" data-destination="<?=$application['deliver_geo']?>" data-current="<?=$application['current_geo']?>" data-status="<?=$application['status_id']?>"></div>
                        <? endif ?>
                    </section>
                    <? if(!$needMap && ($user['user_type_id'] != 1 || ($applicationUser && $applicationUser['user_id'] == $user['user_id']))): ?>
                    <section class="application-info__interaction">
                        <div class="application-info__interaction__info pc-hide">
                            <div class="application-info__price">
                                <?= NormalizeView::checkPrice($application['price'], $application['method']) ?>
                            </div>
                            <div class="application-info__status">
                                <div class="status__block">
                                    <?=getStatusIcon($application['status'])?> <?= $application['status'] ?>
                                </div>
                            </div>
                        </div>
                        <div class="application-info__interaction_top">
                            <a class="application-info__flex" href="/application_edit?id=<?= $application_id ?>">
                                <svg width="1.19rem" height="1.19rem" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.38582 15.4531C3.90291 15.4531 3.45166 15.2869 3.12707 14.9781C2.71541 14.5902 2.51749 14.0044 2.58874 13.3711L2.88166 10.8061C2.93707 10.3231 3.22999 9.6819 3.57041 9.33356L10.07 2.45398C11.6929 0.736065 13.3871 0.688565 15.105 2.31148C16.8229 3.9344 16.8704 5.62856 15.2475 7.34648L8.74791 14.2261C8.41541 14.5823 7.79791 14.9148 7.31499 14.994L4.76582 15.4294C4.63124 15.4373 4.51249 15.4531 4.38582 15.4531ZM12.6112 2.30357C12.0017 2.30357 11.4712 2.68357 10.9329 3.25357L4.43332 10.1411C4.27499 10.3073 4.09291 10.7031 4.06124 10.9327L3.76832 13.4977C3.73666 13.759 3.79999 13.9727 3.94249 14.1073C4.08499 14.2419 4.29874 14.2894 4.55999 14.2498L7.10916 13.8144C7.33874 13.7748 7.71874 13.569 7.87707 13.4027L14.3767 6.52315C15.3583 5.47815 15.7146 4.51232 14.2817 3.16648C13.6483 2.5569 13.1021 2.30357 12.6112 2.30357Z" fill="white" />
                                    <path d="M13.7275 8.66932C13.7117 8.66932 13.6879 8.66932 13.6721 8.66932C11.2021 8.42391 9.21502 6.54766 8.83502 4.09349C8.78752 3.76891 9.00919 3.46807 9.33377 3.41266C9.65835 3.36516 9.95919 3.58682 10.0146 3.91141C10.3154 5.82724 11.8671 7.29974 13.7988 7.48974C14.1234 7.52141 14.3609 7.81432 14.3292 8.13891C14.2896 8.43974 14.0284 8.66932 13.7275 8.66932Z" fill="white" />
                                    <path d="M16.625 18.0098H2.375C2.05042 18.0098 1.78125 17.7406 1.78125 17.416C1.78125 17.0914 2.05042 16.8223 2.375 16.8223H16.625C16.9496 16.8223 17.2188 17.0914 17.2188 17.416C17.2188 17.7406 16.9496 18.0098 16.625 18.0098Z" fill="white" />
                                </svg>
                                <span>
                                    Edit
                                </span>
                            </a>
                            <div class="application-info__buttons application__buttons">
                                <a class="personal-applications_create button" href="/chat?application_id=<?= $application_id ?>">
                                    <svg width="1.56rem" height="1.5rem" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4.5 4H20.5C21.6 4 22.5 4.9 22.5 6V18C22.5 19.1 21.6 20 20.5 20H4.5C3.4 20 2.5 19.1 2.5 18V6C2.5 4.9 3.4 4 4.5 4Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M22.5 6L12.5 13L2.5 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span>
                                        Write broker
                                    </span>
                                </a>
                                <a class="personal-applications_profile button-outline" href="<?=$application['status_id'] == 1 && $application['price'] > 0 ? 'order?application_id='. $application['application_id']: ''?>">
                                    <svg width="1.56rem" height="1.5rem" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.8999 17.4201H11.3899C9.74991 17.4201 8.41991 16.0401 8.41991 14.3401C8.41991 13.9301 8.75991 13.5901 9.16991 13.5901C9.57991 13.5901 9.91991 13.9301 9.91991 14.3401C9.91991 15.2101 10.5799 15.9201 11.3899 15.9201H13.8999C14.5499 15.9201 15.0899 15.3401 15.0899 14.6401C15.0899 13.7701 14.7799 13.6001 14.2699 13.4201L10.2399 12.0001C9.45991 11.7301 8.40991 11.1501 8.40991 9.36008C8.40991 7.82008 9.61991 6.58008 11.0999 6.58008H13.6099C15.2499 6.58008 16.5799 7.96008 16.5799 9.66008C16.5799 10.0701 16.2399 10.4101 15.8299 10.4101C15.4199 10.4101 15.0799 10.0701 15.0799 9.66008C15.0799 8.79008 14.4199 8.08008 13.6099 8.08008H11.0999C10.4499 8.08008 9.90991 8.66008 9.90991 9.36008C9.90991 10.2301 10.2199 10.4001 10.7299 10.5801L14.7599 12.0001C15.5399 12.2701 16.5899 12.8501 16.5899 14.6401C16.5799 16.1701 15.3799 17.4201 13.8999 17.4201Z" fill="white" />
                                        <path d="M12.5 18.75C12.09 18.75 11.75 18.41 11.75 18V6C11.75 5.59 12.09 5.25 12.5 5.25C12.91 5.25 13.25 5.59 13.25 6V18C13.25 18.41 12.91 18.75 12.5 18.75Z" fill="white" />
                                        <path d="M12.5 22.75C6.57 22.75 1.75 17.93 1.75 12C1.75 6.07 6.57 1.25 12.5 1.25C18.43 1.25 23.25 6.07 23.25 12C23.25 17.93 18.43 22.75 12.5 22.75ZM12.5 2.75C7.4 2.75 3.25 6.9 3.25 12C3.25 17.1 7.4 21.25 12.5 21.25C17.6 21.25 21.75 17.1 21.75 12C21.75 6.9 17.6 2.75 12.5 2.75Z" fill="white" />
                                    </svg>

                                    <span>
                                        <div class="pay_order"><?=($application['pay_status'] == 2 ? 'Success' : ($application['pay_status'] == 1 ? 'Cancel' : ($application['status_id'] == 1 && $application['price'] > 0 ? 'Pay order' : 'Find price')))?></div>
                                    </span>
                                </a>
                            </div>
                            <div class="application-info__flex _red _application-delete">
                                <svg width="1.25rem" height="1.25rem" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.87492 18.4271C5.18034 18.4271 1.3645 14.6112 1.3645 9.91667C1.3645 5.22208 5.18034 1.40625 9.87492 1.40625C14.5695 1.40625 18.3853 5.22208 18.3853 9.91667C18.3853 14.6112 14.5695 18.4271 9.87492 18.4271ZM9.87492 2.59375C5.83742 2.59375 2.552 5.87917 2.552 9.91667C2.552 13.9542 5.83742 17.2396 9.87492 17.2396C13.9124 17.2396 17.1978 13.9542 17.1978 9.91667C17.1978 5.87917 13.9124 2.59375 9.87492 2.59375Z" fill="#DA4242" />
                                    <path d="M7.6345 12.7503C7.48408 12.7503 7.33366 12.6949 7.21491 12.5762C6.98533 12.3466 6.98533 11.9666 7.21491 11.737L11.6957 7.25617C11.9253 7.02659 12.3053 7.02659 12.5349 7.25617C12.7645 7.48576 12.7645 7.86575 12.5349 8.09534L8.05408 12.5762C7.94324 12.6949 7.78491 12.7503 7.6345 12.7503Z" fill="#DA4242" />
                                    <path d="M12.1153 12.7503C11.9649 12.7503 11.8145 12.6949 11.6957 12.5762L7.21491 8.09534C6.98533 7.86575 6.98533 7.48576 7.21491 7.25617C7.4445 7.02659 7.8245 7.02659 8.05408 7.25617L12.5349 11.737C12.7645 11.9666 12.7645 12.3466 12.5349 12.5762C12.4162 12.6949 12.2657 12.7503 12.1153 12.7503Z" fill="#DA4242" />
                                </svg>
                                <span>
                                    Delete
                                </span>
                            </div>
                        </div>
                        <? if ($haveMap): ?>
                        <div class="application-info__interaction__bottom">
                            <a href="application?id=<?=$application['application_id']?>&map=1">
                            <div class="application-info__link">
                                <svg width="1.56rem" height="1.5rem" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.26481 8.13415L3.26466 8.13407C2.8807 7.91009 2.75279 7.4194 2.97664 7.03565C3.2007 6.65155 3.68115 6.52409 4.07451 6.74734L4.07488 6.74755L4.07487 6.74755L12.4998 11.6231L20.8746 6.77767C21.2592 6.55335 21.7492 6.69258 21.9727 7.06515L21.973 7.06568C22.1973 7.45022 22.0581 7.94018 21.6856 8.16372L21.6849 8.16412L12.9149 13.2441L12.9109 13.2465L12.9108 13.2463C12.7774 13.3078 12.6399 13.3508 12.4998 13.3508C12.3621 13.3508 12.2232 13.319 12.0948 13.2441L3.26481 8.13415ZM3.26481 8.13415L12.0946 13.2441L3.26481 8.13415Z" fill="white" stroke="white" stroke-width="0.1" />
                                    <path d="M11.7 21.6091C11.7 22.0467 12.0624 22.4091 12.5 22.4091C12.9376 22.4091 13.3 22.0467 13.3 21.6091V12.5391C13.3 12.1014 12.9376 11.7391 12.5 11.7391C12.0624 11.7391 11.7 12.1014 11.7 12.5391V21.6091Z" fill="white" stroke="white" stroke-width="0.1" />
                                    <path d="M4.69549 4.72661L4.69564 4.72653L10.0355 1.76659C10.7246 1.38151 11.6084 1.19023 12.4911 1.19023C13.3738 1.19023 14.2601 1.38151 14.9541 1.7665C14.9541 1.76651 14.9541 1.76651 14.9541 1.76651L20.294 4.72648C21.0285 5.13169 21.6819 5.82139 22.1517 6.61732C22.6214 7.41325 22.9099 8.31943 22.9099 9.16021V12.8202C22.9099 13.2579 22.5475 13.6202 22.1099 13.6202C21.6722 13.6202 21.3099 13.2579 21.3099 12.8202V9.16021C21.3099 8.62178 21.1013 7.99557 20.7718 7.43689C20.4423 6.8782 19.9956 6.39343 19.5257 6.13404L19.5256 6.134L14.1856 3.17397L14.1854 3.17386C13.7401 2.92429 13.1243 2.79774 12.5049 2.79774C11.8855 2.79774 11.2696 2.92429 10.8243 3.17386L10.8241 3.17397L5.48419 6.13395C5.00916 6.3984 4.56242 6.88328 4.2342 7.44061C3.90598 7.99796 3.69987 8.62169 3.69987 9.16021V14.8202C3.69987 15.3587 3.90842 15.9849 4.23794 16.5436C4.56746 17.1023 5.01418 17.5871 5.48404 17.8465L5.48411 17.8465L10.8241 20.8065L10.8242 20.8065C11.2541 21.0459 11.8798 21.1677 12.5086 21.1677C13.1375 21.1677 13.7605 21.0459 14.1853 20.8066L14.1856 20.8065C14.5696 20.5932 15.0601 20.7317 15.2736 21.116C15.4869 21.4999 15.3485 21.9901 14.9645 22.2037C14.2956 22.5888 13.4167 22.8002 12.4999 22.8002C11.5829 22.8002 10.7043 22.5887 10.0356 22.214L4.69549 4.72661ZM4.69549 4.72661C3.96125 5.13677 3.30786 5.82635 2.83809 6.62104C2.36833 7.41572 2.07988 8.31941 2.07988 9.16021V14.8202C2.07988 15.661 2.36831 16.5672 2.83807 17.3632C3.30783 18.1591 3.96128 18.8488 4.69573 19.254L10.0354 22.2138L4.69549 4.72661Z" fill="white" stroke="white" stroke-width="0.1" />
                                    <path d="M15.7 18.2C15.7 20.4076 17.4924 22.2 19.7 22.2C21.9076 22.2 23.7 20.4076 23.7 18.2C23.7 15.9924 21.9076 14.2 19.7 14.2C17.4924 14.2 15.7 15.9924 15.7 18.2ZM17.3 18.2C17.3 16.8776 18.3776 15.8 19.7 15.8C21.0224 15.8 22.1 16.8776 22.1 18.2C22.1 19.5224 21.0224 20.6 19.7 20.6C18.3776 20.6 17.3 19.5224 17.3 18.2Z" fill="white" stroke="white" stroke-width="0.1" />
                                    <path d="M22.9346 22.5649C23.0945 22.7247 23.2974 22.7995 23.4999 22.7995C23.7025 22.7995 23.9054 22.7247 24.0653 22.5649C24.3748 22.2553 24.3748 21.7436 24.0653 21.4341L23.0653 20.4341C22.7558 20.1246 22.2441 20.1246 21.9346 20.4341C21.6251 20.7436 21.6251 21.2553 21.9346 21.5649L22.9346 22.5649Z" fill="white" stroke="white" stroke-width="0.1" />
                                </svg>
                                <span>
                                    Track your cargo
                                </span>
                            </div>
                            </a>
                        </div>
                        <? endif ?>
                    </section>
                    <? endif ?>
                </div>
            </div>
        </main>
        <? require_once __DIR__ . './../components/footer.php'; ?>
    </div>
    <? require_once __DIR__ . './../components/script.php'; ?>
</body>

</html>