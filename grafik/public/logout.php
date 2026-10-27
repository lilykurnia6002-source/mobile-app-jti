<?php
class Session {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function destroy() {
        session_unset();
        session_destroy();
    }
}
?>