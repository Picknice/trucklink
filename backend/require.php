<?php

mb_internal_encoding("UTF-8");

require_once 'controller/session.php';

require_once 'global/db.php';

require_once 'include/connect.php';

require_once 'global/functions.php';
require_once 'global/vars.php';
require_once 'global/normalize_view.php';
require_once 'global/date.php';

require_once 'controller/date.php';

require_once 'controller/image.php';

require_once 'controller/email.php';

require_once 'model/user_session.php';
require_once 'controller/user_session.php';

require_once 'model/user.php';
require_once 'controller/user.php';

require_once 'model/application.php';
require_once 'controller/application.php';

require_once 'model/Chat.php';
require_once 'controller/ChatController.php';

require_once 'model/Transport.php';

require_once 'global/telegrambotapi.php';
require_once 'global/cargoapi.php';