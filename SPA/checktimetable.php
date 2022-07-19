<?php
    session_start();
    require_once('connect.php');
    $group = $_POST['groupid'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $id = $_POST['id'];
    $sql = $mysql->query("SELECT `id`, `repeatid` FROM `timetable` WHERE (`group` = '$group' AND `date` = '$date' AND `time` = '$time')");
    $res = $sql->fetch_all();
    if (count($res) != 0){
        if ($res[0][0] == $id){
            echo '0';
            goto end;
        }
        echo "-1 ".$res[0][0]." ".$res[1][0];
        goto end;
    }
    $sql = $mysql->query("SELECT `id`, `repeatid` FROM `timetable` WHERE (`date` = '$date' AND `time` = '$time' AND `teacherid` = '$_SESSION[userid]')");
    $res = $sql->fetch_all();
    if (count($res) == 0){
        echo '0';
    }
    else{
        echo "1 ".$res[0][0]." ".$res[1][0];
    }
    end:
?>