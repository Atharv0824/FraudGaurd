<?php
/**
 * FraudGuard - User Registration Alias
 * Redirects to the registration tab of login.php
 */
header('Location: login.php?mode=register');
exit;
