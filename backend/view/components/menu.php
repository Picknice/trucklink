<div class="menu-block">
    <div class="menu-content">
        <div class="menu-list">
            <a href="/"><div class="menu-item">Main</div></a>
            <a href="/<?=$user?'profile?#cargo':'login'?>"><div class="menu-item">Cargo</div></a>
            <a href="/<?=$user?'profile':'login'?>"><div class="menu-item">My office</div></a>
            <a href="/#faq"><div class="menu-item">Faq</div></a>
            <a href="/contact"><div class="menu-item">Contact</div></a>
            <? if($user){ ?>
                <a href="/signout"><div class="menu-item">Sign out</div></a>
            <? } ?>
        </div>
    </div>
</div>