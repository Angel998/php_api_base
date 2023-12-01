<?php

function get_model($model)
{
    // Require model file
    require_once APP_ROOT . '/models/' . $model . '.php';
    $modelWords = explode('/', $model);
    $modelName = array_pop($modelWords);
    return new $modelName();
}

/**
 * Retorna la fecha actual en formato de tiempo
 */
function getCurrentTime()
{
    return strtotime(date('Y-m-d H:i:s'));
}

/**
 * Convierte una arreglo asociativo a objeto
 */
function arrayToObject($array)
{
    return json_decode(json_encode($array), false);
}

function get_config_values()
{
    $direction = '/../../../vars/config.ini';
    if (!file_exists(APP_ROOT . $direction)) {
        serverErrorHeader(true);
    }
    $values = parse_ini_file(APP_ROOT . $direction);
    if (is_null($values)) {
        serverErrorHeader(true);
    }
    return arrayToObject($values);
}

/**
 * Retorna una clave encryptada
 */
function getHashPassword($password)
{
    $options = [
        'cost' => 11
        //,'salt' => random_bytes(22)
    ];
    return password_hash($password, PASSWORD_BCRYPT, $options);
}

/**
 * Retorna un valor en caso de que exista o un valor por defecto
 */
function getIfIsset($var, $key, $default = "")
{
    if (is_null($var)) {
        return $default;
    }

    if (is_object($var)) {
        if (!isset($var->$key)) {
            return $default;
        }
        return $var->$key;
    } else {
        if (!isset($var[$key])) {
            return $default;
        }
        return $var[$key];
    }
}

/**
 * Genera un string de forma aleatoria
 */
function getRandomString($length = 15)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Concatena una url + el sitio actual
 * site.com + "url enviada"
 */
function getSiteURL($simple_url)
{
    return URL_ROOT . $simple_url;
}
