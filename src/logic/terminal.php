<?php
declare(strict_types=1);
    $input = trim($_POST["command"] ?? "");
    if ($input != "")
    {
        //$_SESSION["history"][count($_SESSION["history"])] = $input;
        $_SESSION["history"] ??= [];
        $_SESSION["history"][] =  $_SESSION["currentDirectory"] . ">" . $input;
    }
?>
