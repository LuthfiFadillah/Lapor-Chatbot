<?php
/**
 * Created by IntelliJ IDEA.
 * User: ASUS-ROG
 * Date: 19/05/2018
 * Time: 14:30
 */

function connect_db(){
    try{
        $host = 'localhost';
        $database = 'lapor-chatbot-3';
        $username = 'root';
        $password = '';

        global $con_db;

        $con_db = new PDO("mysql:host=".$host.";dbname=".$database,$username,$password);

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