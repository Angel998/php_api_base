<?php
/*
    PDO Database class
    COnnect to database
    Create prepared statements
     */

class Database
{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh; //DB Handler
    private $stmt;
    private $error;
    private $stmt_params = [];

    public function __construct()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e;
            $this->reportError();
            serverErrorHeader(true);
        }
    }

    // Prepare statement with query
    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
        $this->stmt_params = [];
    }

    // Bind values
    public function bind($param, $value, $type = null)
    {
        if (is_null($this->stmt)) {
            return;
        }
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        array_push($this->stmt_params, "($param => $value)");
        try {
            $this->stmt->bindValue($param, $value, $type);
        } catch (PDOException $e) {
            $this->error = $e;
            $this->reportError();
        } catch (Exception $e) {
            $this->error = $e;
            $this->reportError();
        }
    }

    // Execute the prepared statement
    public function execute()
    {
        if (is_null($this->stmt)) {
            return false;
        }
        try {
            $success = $this->stmt->execute();
            $this->error = null;
            return $success;
        } catch (PDOException $e) {
            $this->error = $e;
        } catch (Exception $e) {
            $this->error = $e;
        }
        $this->reportError();
        return false;
    }

    // Get result set as array of objects
    public function resultSet()
    {
        $results = null;
        if ($this->execute()) {
            try {
                $results = $this->stmt->fetchAll(PDO::FETCH_OBJ);
                $this->stmt->closeCursor();
            } catch (PDOException $e) {
                $this->error = $e;
                $this->reportError();
            } catch (Exception $e) {
                $this->error = $e;
                $this->reportError();
            }
        }
        return is_null($results) ? [] : $results;
    }

    // Get single record as object
    public function single()
    {
        if ($this->execute()) {
            $result = null;
            try {
                $result = $this->stmt->fetch(PDO::FETCH_OBJ);
                $this->stmt->closeCursor();
            } catch (PDOException $e) {
                $this->error = $e;
                $this->reportError();
            }
            return $result ? $result : null;
        }
        return null;
    }

    public function newId()
    {
        $result = $this->single();
        return isset($result->id) ? $result->id : null;
    }

    public function rowCount()
    {
        return is_null($this->stmt) ? 0 : $this->stmt->rowCount();
    }

    public function success()
    {
        $this->execute();
        return $this->rowCount() > 0;
    }

    private function reportError()
    {
        if (is_null($this->error)) {
            return;
        }
        $query = is_null($this->stmt) ? "null" : $this->stmt->queryString;
        $str_params = "";
        foreach ($this->stmt_params as $param) {
            $str_params .= $param . " - ";
        }
        errorLog("Database: error -> " . $this->error->getMessage() . " : query -> " . $query . " : params: " . $str_params);
    }
}
