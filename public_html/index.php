<?php
// Front controller: every page request comes through here (see .htaccess).
define('MP_PUBLIC', __DIR__);
require __DIR__ . '/../private/bootstrap.php';
require __DIR__ . '/../private/routes.php';
