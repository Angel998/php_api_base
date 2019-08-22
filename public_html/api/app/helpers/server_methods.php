<?php
function getRequestMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}

function getClientIP()
{
    return $_SERVER['REMOTE_ADDR'];
}

// Headers de respuesta
function jsonHeader()
{
    header('Content-Type: application/json');
}

function notFoundHeader($endRequest = false)
{
    header("HTTP/1.0 404 Not Found");
    if ($endRequest) {
        die();
    }
}

function notAuthorizedHeader($endRequest = false)
{
    header("HTTP/1.0 403 Forbidden");
    if ($endRequest) {
        die();
    }
}

function processErrorHeader($endRequest = false)
{
    header("HTTP/1.0 409 Conflict");
    if ($endRequest) {
        die();
    }
}

function serverErrorHeader($endRequest = false)
{
    header("HTTP/1.0 500 Internal Error");
    if ($endRequest) {
        die();
    }
}

function is_get_request()
{
    return getRequestMethod() == "GET";
}

function is_post_request()
{
    return getRequestMethod() == "POST";
}

function is_put_request()
{
    return getRequestMethod() == "PUT";
}

function is_delete_request()
{
    return getRequestMethod() == "DELETE";
}

function sendJsonData($data, $die = false)
{
    jsonHeader();
    echo json_encode($data);
    if ($die) {
        die();
    }
}

function getJsonData($json_identifier = null)
{
    $data = null;

    if (
        !is_null($json_identifier) &&
        isset($_POST[$json_identifier])
    ) {
        $data = json_decode($_POST[$json_identifier]);
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
    }

    if (!is_null($data)) {
        $data = array_to_obj($data);
        foreach ($data as &$value) {
            if (is_string($value)) {
                $value = filter_var(trim($value), FILTER_SANITIZE_STRING);
            }
        }
    }
    return ($data && is_object($data)) ? $data : null;
}

function errorLog($error)
{
    $file_url = APP_ROOT . '/log/error.log';
    $data_help = "User: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("F j, Y, g:i a") . ": ";
    $file_content = "\n" . $data_help . $error;
    file_put_contents($file_url, $file_content, FILE_APPEND | LOCK_EX);
}

function processLog($str)
{
    $file_url = APP_ROOT . '/log/process.log';
    $data_help = "User: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("F j, Y, g:i a") . ": ";
    $file_content = "\n" . $data_help . $str;
    file_put_contents($file_url, $file_content, FILE_APPEND | LOCK_EX);
}

function sendResponse($data = null, $error = null)
{
    if (!is_null($error)) {
        switch ($error) {
            case ERROR_NOTFOUND:
                notFoundHeader();
                break;

            case ERROR_FORBIDDEN:
                notAuthorizedHeader();
                break;

            case ERROR_PROCESS:
                processErrorHeader();
                break;

            case ERROR_SERVER:
                serverErrorHeader();
                break;
        }
    }
    if (!is_null($data)) {
        jsonHeader();
        echo json_encode($data);
    }
    die();
}


if (!function_exists('apache_request_headers')) {
    function apache_request_headers()
    {
        $arh = array();
        $rx_http = '/\AHTTP_/';
        foreach ($_SERVER as $key => $val) {
            if (preg_match($rx_http, $key)) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
                // do some nasty string manipulations to restore the original letter case
                // this should work in most cases
                $rx_matches = explode('_', $arh_key);
                if (count($rx_matches) > 0 and strlen($arh_key) > 2) {
                    foreach ($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
                    $arh_key = implode('-', $rx_matches);
                }
                $arh[$arh_key] = $val;
            }
        }
        return ($arh);
    }
}
