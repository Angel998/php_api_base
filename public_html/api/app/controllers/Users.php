<?php
class Users extends Controller
{
    private $users = [
        [
            "id" => 1,
            "name" => "Frank"
        ],
        [
            "id" => 2,
            "name" => "Carlos"
        ],
        [
            "id" => 3,
            "name" => "Jonh"
        ]
    ];

    public function __construct()
    {
        $this->initController();
    }

    // @path api/users/get
    public function get()
    {
        $this->useGetRequest();
        $this->response($this->users);
    }

    // @path api/users/get_one/:id
    public function get_one($id)
    {
        $this->useGetRequest();
        $findedUser = null;
        foreach ($this->users as $user) {
            if ($user['id'] == $id) {
                $findedUser = $user;
                break;
            }
        }
        if (is_null($findedUser)) {
            $this->response(null, ERROR_NOTFOUND);
        }
        $this->response($findedUser);
    }
}
