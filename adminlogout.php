<?php
session_name('admin_session'); // Set a unique session name for admins
session_start();
session_unset();
session_destroy();
header("Location: admin_login.php?message=loggedout");
exit;
?>
