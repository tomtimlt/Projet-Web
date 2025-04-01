<?php

class UserController {
    private $userModel;
    
    public function __construct($userModel) {
        $this->userModel = $userModel;
    }
    
    public function profile() {
        include_once 'views/profile/view.php';
    }
}
?>