<?php

/**
 * Valida un correo electronico, si pasa la validacion retorna uns string vacio
 * de lo contrario retorna el error si no es nulo
 */
function validateEmail($email, $error = null)
{
    $is_email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!is_null($error)) {
        if ($is_email) {
            return '';
        }
        return $error;
    }
    return $is_email;
}

function isValidPassword($password)
{
    return (
        !is_null($password) &&
        !empty($password) &&
        strlen($password) > 5
    );
}

function strOnlyNumbers($string)
{
    return preg_replace("/[^0-9]/", "", $string);
}

function strRemoveSpaces($string)
{
    $string = preg_replace('/\s+/', '', $string);
    return $string;
}
