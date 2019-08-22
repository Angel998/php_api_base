<?php
/*
    App Core Class
    Creates URL & loads core controller
    URL FORMAT - /controller/method/params 
    */
class Core
{
    protected $valid_request_methods = [
        'GET',
        'POST',
        'DELETE',
        'PUT'
    ];

    protected $valid_ip_address = [
        '127.0.0.1'
    ];

    protected $currentController = 'Users';
    protected $currentMethod = 'get';
    protected $params = [];

    public function __construct()
    {
        $this->initController();

        $url = $this->getUrl();
        if (file_exists('../app/controllers/' . ucfirst($url[0]) . '.php')) {
            // if exsists, set as controller
            $this->currentController = ucfirst($url[0]);
            unset($url[0]);
        } else {
            notFoundHeader(true);
        }
        require_once '../app/controllers/' . $this->currentController . '.php';
        // Instantiate controller class
        $this->currentController = new $this->currentController;

        // Check function
        if (!isset($url[1])) {
            $url[1] = strtolower(getRequestMethod());
        }
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            } else {
                notFoundHeader(true);
            }
        }

        //Get params
        $this->params = $url ? array_values($url) : [];

        // Call a callback with array of params
        $this->validateParametersInFunction();
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function initController()
    {
        $method = getRequestMethod();
        if (!in_array($method, $this->valid_request_methods)) {
            notFoundHeader(true);
        }
        // Validacion de IP
        // $client_ip = getClientIP();
        // if (!in_array($client_ip, $this->valid_ip_address)) {
        //     processLog("IP Cliente invalido: $client_ip");
        //     notFoundHeader(true);
        // }
    }

    public function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = preg_replace("/[^\x20-\x7E]/", "", $url);
            $url = explode('/', $url);
            return $url;
        }
    }

    private function validateParametersInFunction()
    {
        $func = new ReflectionMethod($this->currentController, $this->currentMethod);
        $count_parameter_required = $func->getNumberOfParameters();

        if (count($this->params) != $count_parameter_required) {
            notFoundHeader(true);
        }
    }
}
