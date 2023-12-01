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
        $data = arrayToObject($data);
        foreach ($data as &$value) {
            if (is_string($value)) {
                $value = filter_var(trim($value), FILTER_SANITIZE_STRING);
            }
        }
    }
    return ($data && is_object($data)) ? $data : null;
}

function errorLog($strError = "")
{
    $filePath = APP_ROOT . '/log/error.log';
    $data_help = "User: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("F j, Y, g:i a") . ": ";
    $fileContent = "\n" . $data_help . $strError;
    writeInFile($filePath, $fileContent);
}

function processLog($strLog = "")
{
    $filePath = APP_ROOT . '/log/process.log';
    $data_help = "User: " . $_SERVER['REMOTE_ADDR'] . ' - ' . date("F j, Y, g:i a") . ": ";
    $fileContent = "\n" . $data_help . $strLog;
    writeInFile($filePath, $fileContent);
}

/**
 * Concatena el contenido enviado hacia un archivo
 */
function writeInFile($filePath, $fileContent = "")
{
    file_put_contents($filePath, $fileContent, FILE_APPEND | LOCK_EX);
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
