<?php
/**
 * Created by IntelliJ IDEA.
 * User: ASUS-ROG
 * Date: 19/05/2018
 * Time: 14:30
 */

function connect_db(){
    try{
        $dotenv = new Dotenv\Dotenv(realpath($_SERVER["DOCUMENT_ROOT"]).'/Lapor-Chatbot-dev/' );
        $dotenv->load();


        $connection = getenv('DB_CONNECTION','pgsql');
        $host = getenv('DB_HOST','localhost');
        $database = getenv('DB_DATABASE','lapor-chatbot');
        $username = getenv('DB_USERNAME','postgres');
        $password = getenv('DB_PASSWORD','');

        error_log($connection, 0);
        global $con_db;

        $con_db = new PDO($connection.":host=".$host.";dbname=".$database.";user=".$username.";password=".$password);

        if(!isset($con_db)){
            error_log("Connection to database failure!",0);
            die();
        } else {
            $con_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con_db;
        };

    } catch (PDOException $e) {
        error_log("Problem in database: ".$e->getMessage(), 0);
        die();
    };
};