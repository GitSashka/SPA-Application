<?php
    require_once('connect.php');
    $groupid = $_POST['group'];
    $lection = $_POST['lection'];
    $sql = $mysql->query("SELECT `id` FROM `timetable` WHERE (`group` = '$groupid' AND `lection` = '$lection') ORDER BY `date` ASC");
    $lections = $sql->fetch_all();
    $sql = $mysql->query("SELECT `id` FROM `students` WHERE (`groupid` = '$groupid')");
    $students = $sql->fetch_all();
    for ($i = 0; $i < count($students); $i++){
        for ($j = 0; $j < count($lections); $j++){
            $sql = $mysql->query("SELECT `grade` FROM `grades` WHERE (`studentid` = ".$students[$i][0]." AND `lectionid` = ".$lections[$j][0].")");
            $res = $sql->fetch_all();
            if (count($res) != 0){
                echo "<form class='journal-grade point unselect'><input id='hiddenstudid' type='hidden' name='studentid' value='".$students[$i][0]."'><input id='hiddenlectid' type='hidden' name='timetableid' value='".$lections[$j][0]."'>".getimg($res[0][0])."</form>";
            }
            else{
                echo "<form class='journal-grade point unselect'><input id='hiddenstudid' type='hidden' name='studentid' value='".$students[$i][0]."'><input id='hiddenlectid' type='hidden' name='timetableid' value='".$lections[$j][0]."'><img width='18' src='img/circle.svg'></form>";
            }
        }
    }
    function getimg($number){
        switch ($number){
            case "11":
                return "1";
            case "10":
                return "2";
            case "9":
                return "3";
            case "8":
                return "4";
            case "7":
                return "5";
            case "6":
                return "<img src='img/close_red.svg' width='18'>";
            case "5":
                return "<img src='img/done.svg' width='18'>";
            case "4":
                return "<img src='img/circle_filled_orange.svg' width='18'>";
            case "3":
                return "<img src='img/circle_filled_yellow.svg' width='18'>";
            case "2":
                return "<img src='img/circle_filled_green.svg' width='18'>";
            case "1":
                return "<img src='img/circle.svg' width='18'>";
        }
    }
?>