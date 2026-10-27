<?php
class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($email, $password) {
        $email = trim($email);
        $password = trim($password);

        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                return true;
            }
        }
        return false;
    }
}
?>