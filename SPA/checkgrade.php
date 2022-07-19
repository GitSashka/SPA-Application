<?php
    session_start();
    require_once('connect.php');
    $sql = $mysql->query("SELECT `teacherid` FROM `timetable` WHERE (`id` = '$_POST[id]')");
    $res = $sql->fetch_all();
    if ($res[0][0] == $_SESSION['userid']){
        echo "1";
    }
    else{
        echo "0";
    }
?>