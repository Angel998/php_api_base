<?php 

    function empty_json_params($json_data, $params = []) {
        $empty_json = true;
        foreach($params as $p) {
            $empty_json = !isset($json_data->$p);
            if ($empty_json == false && is_string($json_data->$p)) {
                $empty_json = empty($json_data->$p);
            }
            if ($empty_json) {
                break;
            }
        }
        return $empty_json;
    }

    function set_empty_json_params($json_data, $params = []) {
        foreach($params as $p) {
            if (!isset($json_data->$p)) {
                $json_data->$p = null;
            }
        }
        return $json_data;
    }

    function is_empty_array($array) {
        if (is_object($array)) {
            $array = (array)$array;
        }
        $array = array_filter($array);
        return empty($array);
    }

    function isEmail($email, $error = null) {
        $is_email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!is_null($error)) {
            if ($is_email) {
                return '';
            }
            return $error;
        } 
        return $is_email;
    }

    function isValidPassword($password) {
        return (
            !is_null($password) &&
            !empty($password) && 
            strlen($password) > 5 
        );
    }

    function onlyNumbers($string) {
        return preg_replace("/[^0-9]/", "", $string);
    }

    function removeSpaces($string) {
        $string = preg_replace('/\s+/', '', $string);
        return $string;
    }
