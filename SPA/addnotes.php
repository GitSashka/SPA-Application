<?php
    require_once ('connect.php');
    $studentid = $_POST['studid'];
    $mysql->query("INSERT INTO `notes` (`studentid`, `date`, `note`) VALUES ('$studentid', NOW(), '')");

?>