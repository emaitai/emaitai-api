<?php

class UserController {
    private $dataFile;

    public function __construct() {
        $this->dataFile = __DIR__ . '/../data/users.json';
        $this->initDataFile();
    }

    private function initDataFile() {
        $dir = dirname($this->dataFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!file_exists($this->dataFile)) {
            file_put_contents($this->dataFile, json_encode([]));
        }
    }

    private function readUsers() {
        $content = file_get_contents($this->dataFile);
        return json_decode($content, true) ?: [];
    }

    private function writeUsers($users) {
        file_put_contents($this->dataFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function getAllUsers() {
        $users = $this->readUsers();
        // Exclure le champ 'password' de la réponse
        return array_map(function($user) {
            unset($user['password']);
            return $user;
        }, $users);
    }

    public function getUserById($id) {
        $users = $this->readUsers();
        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }

    public function createUser($data) {
        $users = $this->readUsers();
        $newId = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;
        
        $newUser = [
            'id' => $newId,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $users[] = $newUser;
        $this->writeUsers($users);
        
        return $newId;
    }

    public function updateUser($id, $data) {
        $users = $this->readUsers();
        foreach ($users as &$user) {
            if ($user['id'] == $id) {
                $user['name'] = $data['name'];
                $user['email'] = $data['email'];
                $user['phone'] = $data['phone'];
                $this->writeUsers($users);
                return true;
            }
        }
        return false;
    }

    public function deleteUser($id) {
        $users = $this->readUsers();
        $users = array_filter($users, function($user) use ($id) {
            return $user['id'] != $id;
        });
        $users = array_values($users);
        $this->writeUsers($users);
        return true;
    }
}
?>
