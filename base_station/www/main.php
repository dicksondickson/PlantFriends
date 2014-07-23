<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('config.php');
require('classes/home.php');
require('classes/admin.php');
$home = new Home();
$admin = new Admin();