<?php
    require_once ('connect.php');
    $mysql->query("DELETE FROM `timetable` WHERE (`id` = '$_POST[id]')");
    if ($_POST['repeatid'] != 0){
        $mysql->query("DELETE FROM `repeats` WHERE (`id` = '$_POST[repeatid]')");
        $mysql->query("DELETE FROM `timetable` WHERE (`repeatid` = '$_POST[repeatid]' AND `date` > NOW() - INTERVAL 1 DAY)");
    }
?>