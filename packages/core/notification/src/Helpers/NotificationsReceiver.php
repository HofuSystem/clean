<?php
namespace Core\Notification\Helpers;

class NotificationsReceiver {

    // Constructor
    public function __construct(
        public string | null $id,  
        public string | null $fullname,  
        public string | null $email,  
        public string | null $phone,
        public string | null $token,
        public string | null $notificationId,
        public string | null $avatar   = null,
        public string | null $birthday = null,
        public string | null $for      = null,
        public string | null $lang     = null
    ) {

    }

    // Magic Getter
    public function __get($property) {
        $method = 'get'.ucfirst($property);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
    }

    // Magic Setter
    public function __set($property, $value) {
        $method = 'set'.ucfirst($property);
        if (method_exists($this, $method)) {
            $this->$property = $this->$method($value);
        }

        return $this;
    }
     // Getters
     public function getName() {
        return $this->fullname;
    }
    public function getLang() {
        return $this->lang;
    }

    public function getAvatar() {
        if($this->for == 'smtp'){
            $url = url($this->avatar ?? 'storage/system/user.png');
            $img = '<img src="'.$url.'"/>';
            return $img;
        }
        return $this->avatar;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getBirthday() {
        return $this->birthday;
    }

    public function getPhone() {
        return $this->phone;
    }
    public function getToken() {
        return $this->token;
    }

    // Setters
    public function setName($fullname) {
        $this->fullname = $fullname;
    }

    public function setAvatar($avatar) {
        $this->avatar = $avatar;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setBirthday($birthday) {
        $this->birthday = $birthday;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setToken($token) {
        $this->token = $token;
    }
    public function setLang($lang) {
        $this->lang = $lang;
    }

    public function toArray(string $for = 'email')  {
        $this->for = $for;
        return [
            'fullname'  => $this->getName(),
            'email'     => $this->getEmail(),
            'phone'     => $this->getPhone(),
            'token'     => $this->getToken(),
        ];
    }
}
