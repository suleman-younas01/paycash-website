<?php
/////////////////////////
///////ZOTEC FRAMEWORK
//////info@zotecsoft.com
////////////////////////
if ($_SESSION['users']['type'] == 'admin'):
    unset($_SESSION["user_data"]);
    session_destroy();
    header("Location: /admin/login");
else:
    unset($_SESSION["user_data"]);
    session_destroy();
    header("Location: /login");
endif;

?>