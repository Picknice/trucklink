<?php
$auth = getUser();
?>
<? if ($auth) : ?>
    <div class="header__log">
        <? if($uri_short !== '/profile'){ ?>
            <a class="header__log_href" href="/profile" style="margin-right: 1.5rem;">
                <svg width="1.5rem" height="1.37rem" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 18V16.3333C19 15.4493 18.6313 14.6014 17.9749 13.9763C17.3185 13.3512 16.4283 13 15.5 13H8.5C7.57174 13 6.6815 13.3512 6.02513 13.9763C5.36875 14.6014 5 15.4493 5 16.3333V18" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12.5 9C14.433 9 16 7.433 16 5.5C16 3.567 14.433 2 12.5 2C10.567 2 9 3.567 9 5.5C9 7.433 10.567 9 12.5 9Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>
                    Profile
                </span>
            </a>
        <? } ?>
        <a class="header__log_href" href="/signout">
            <svg width="1.5rem" height="1.37rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H9" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M16 17L21 12L16 7" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 12H9" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Sign out
        </a>
    </div>
<? else : ?>
    <div class="header__log">
        <? if($uri_short !== '/login' && $uri_short !== '/signup'): ?>
        <a class="header__log_href" href="/login" style="margin-right: 1.5rem;">
            <svg width="1.5rem" height="1.37rem" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 18V16.3333C19 15.4493 18.6313 14.6014 17.9749 13.9763C17.3185 13.3512 16.4283 13 15.5 13H8.5C7.57174 13 6.6815 13.3512 6.02513 13.9763C5.36875 14.6014 5 15.4493 5 16.3333V18" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12.5 9C14.433 9 16 7.433 16 5.5C16 3.567 14.433 2 12.5 2C10.567 2 9 3.567 9 5.5C9 7.433 10.567 9 12.5 9Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span>
                Login
            </span>
        </a>
        <? endif ?>
        <? if($uri_short !== '/login' && $uri_short !== '/signup'): ?>
        <a class="header__log_href" href="/signup">
            <svg width="1.5rem" height="1.37rem" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 18V16.3333C19 15.4493 18.6313 14.6014 17.9749 13.9763C17.3185 13.3512 16.4283 13 15.5 13H8.5C7.57174 13 6.6815 13.3512 6.02513 13.9763C5.36875 14.6014 5 15.4493 5 16.3333V18" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M12.5 9C14.433 9 16 7.433 16 5.5C16 3.567 14.433 2 12.5 2C10.567 2 9 3.567 9 5.5C9 7.433 10.567 9 12.5 9Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span>
                Sign up
            </span>
        </a>
        <? endif ?>
    </div>
<? endif ?>