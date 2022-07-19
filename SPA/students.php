<?php
    require_once ('connect.php');
    $group = $_POST['group'];
    $lection = $_POST['lection'];

    $sql = $mysql->query("SELECT * FROM `students` WHERE (`groupid` = '$group')");
    $mas = $sql->fetch_all();
    foreach ($mas as $line){
        $sql = $mysql->query("SELECT * FROM `notes` WHERE (`studentid` = $line[0])");
        $res = $sql->fetch_all();
        if (count($res) == 0){
            echo "<input class='hiddenstudentid' type='hidden' name='studentid' value='".$line[0]."'><a class='point'>".$line[2]." ".$line[3]."</a>";
        }
        else{
            echo "<input class='hiddenstudentid' type='hidden' name='studentid' value='".$line[0]."'><a class='point'>".$line[2]." ".$line[3]."  <img src='img/warning.svg'></a>";
        }
    }
?>