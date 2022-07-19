<?php
    require_once ('connect.php');
    $id = $_POST['id'];
    $mysql -> query("DELETE FROM `notes` WHERE `id` = '$id'");

?>