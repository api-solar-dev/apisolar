<?php

//Connexion BDD

//Mode test localhost
$host_name = 'db';
$database = 'securite';
$user_name = 'test';
$password = $_ENV["PASSWORD_DB"];

try {
    $dbh = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
} catch (PDOException $e) {
    echo "Erreur!: " . $e->getMessage() . "<br/>";
    die();
}
