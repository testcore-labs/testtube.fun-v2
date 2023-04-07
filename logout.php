<?php
ob_start();
session_set_cookie_params(2592000);
session_start();
unset($_SESSION['user']);
header("Location: /");