<?php

class Database {

    private $hostname= "localhost";
    private $database= "makarlu-prueba2";
    private $username= "root";
    private $password= "";
    private $charset= "utf8";
    
    function  conectar() {
         
        try{
        $conexion = "mysql:host=" .$this->hostname . "; dbname=" .$this->database . "; charset=" .$this->charset;
        
        // Las preparaciones de conexion tengan seguridad y no sean emuladas 
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        $pdo = new PDO($conexion, $this->username, $this->password, $options);

        return $pdo;
    
    } catch (PDOException $e){

        echo 'Error Conexion:  ' .$e->getMessage();
        exit;

    }
    }


}
