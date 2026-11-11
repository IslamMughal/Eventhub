<?php
require 'includes/functions.php';
log_action('logout');
session_destroy();
header('Location: index.php');
