<?php
define('APP_ROOT', dirname(dirname(__FILE__)));

$values = get_config_values();
define('DB_HOST', $values->db_host);
define('DB_USER', $values->db_user);
define('DB_PASS', $values->db_pass);
define('DB_NAME', $values->db_name);
define('HEADER_TOKEN_NAME', $values->header_token_name);
define('KEY_BEARER', $values->key_bearer);
define('KEY_TOKEN', $values->key_token);
define('TOKEN_DURATION', $values->token_duration);
unset($values);

define('ERROR_NOTFOUND', 404);
define('ERROR_FORBIDDEN', 403);
define('ERROR_PROCESS', 409);
define('ERROR_SERVER', 500);

define('URLROOT', 'http://yoursite.com/');
define('SITENAME', 'YOURSITE');

// Private Controller Types
define('CTR_PRIVATE', 'CTR_PRIVATE');
define('CTR_ADMIN', 'CONTROLLER_FOR_ADMIN');
define('CTR_ADMIN_SAME_USER', 'CONTROLLER_FOR_ADMIN_OR_SAME_USER');
