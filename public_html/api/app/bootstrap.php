<?php

    require_once 'helpers/get_values.php';

    // Helpers
    require_once 'helpers/server_methods.php';
    require_once 'helpers/validations.php';

    require_once 'config/config.php';

    // Autoload Core Libraries
    spl_autoload_register(function($className) {
        require_once 'libraries/'. $className .'.php';
    });
