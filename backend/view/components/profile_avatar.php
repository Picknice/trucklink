<label class="user-info__image" for="user_avatar">
    <? if ($user['avatar']) : ?>
        <? if (file_exists("./view/upload/{$user['avatar']}")): ?>
            <img src="./view/upload/<?= $user['avatar'] ?>" alt="<?= $user['name'] . " " . $user['surname'] ?>">
        <? else:?>
            <img src="./view/static/img/no-avatar.png" alt="Нет фото">
        <? endif; ?>
    <? else : ?>
        <img src="./view/static/img/no-avatar.png" alt="Нет фото">
    <? endif ?>
    <? if ($user['user_id'] == $_SESSION['user']['user_id']) : ?>
        <input accept="<?= implode(',', $ALLOWED_IMAGE_TYPES) ?>" type="file" id="user_avatar" hidden>
    <? endif ?>
</label>