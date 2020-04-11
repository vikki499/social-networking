<?php

    include_once "../includes/session.php";

    $session = new Session();

    $email = $_POST['email'];
    $passwd = $_POST['password'];

    $response = $session->login($email, $passwd);

    die(json_encode($response));

?>