<?php
class User
{
    public function __construct()
    {
        //$this->db = new Database;
    }

    public function is_user_admin($id)
    {
        return false;
    }

    public function is_user_enabled($id)
    {
        return true;
    }
}
