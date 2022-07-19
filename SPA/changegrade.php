<?php
    require_once('connect.php');
    $lectid = $_POST['lectid'];
    $studid = $_POST['studid'];
    $grade = $_POST['gradeid'];
    $sql = $mysql->query("SELECT * FROM `grades` WHERE (`lectionid` = '$lectid' AND `studentid` = '$studid')");
    $res = $sql->fetch_all();
    if (count($res) > 0){
        $mysql->query("UPDATE `grades` SET `grade` = '$grade' WHERE (`lectionid` = '$lectid' AND `studentid` = '$studid')");
    }
    else{
        $mysql->query("INSERT INTO `grades` (`lectionid`, `studentid`, `grade`) VALUES ('$lectid', '$studid', '$grade')");
    }
    echo "lect: ".$lectid." stud: ".$studid." ".$grade;
?>