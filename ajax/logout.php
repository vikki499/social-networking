<?php

    include_once "../includes/session.php";
    $session = new Session();

    $jsonMessage = array();

    if (!$session->isLoggedIn()) {
        $jsonMessage['status'] = "Error";
        $jsonMessage['data'] = 'Illegal_entry';
        die(json_encode($jsonMessage));
    }
    $session->logout();

    $jsonMessage['status'] = "Success";
    $jsonMessage['data'] = 'Logged out';
    die(json_encode($jsonMessage));

?>