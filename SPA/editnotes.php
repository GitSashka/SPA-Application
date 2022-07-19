<?php
    $id = $_POST['id'];
    $note = $_POST['note'];
    require_once ('connect.php');
    $mysql->query("UPDATE `notes` SET `note` = '".$note."' WHERE (`id` = '$id')");
?>