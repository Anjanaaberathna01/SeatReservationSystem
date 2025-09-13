<?php
// Database connection class
// This class handles the connection to the database and provides methods for reading and saving data.
class database
{
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $db = "seat_reservation_system";
    // Database connection and methods can be defined here
    public function connect(): mysqli|false
    {
        $conn = mysqli_connect($this->host, $this->username, $this->password, $this->db);
        return $conn;
    }
}