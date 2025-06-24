<?php
class DataBase {
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            
            self::$conn = new mysqli("localhost", "root", "root", "SAFE_ZONE");

            
            if (self::$conn->connect_error) {
                die("Falha na conexão: " . self::$conn->connect_error);  
            }
        }
        return self::$conn;  
    }
}
?>