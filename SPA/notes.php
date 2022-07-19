<?php
    require_once('connect.php');
    $id = $_POST['id'];
    $sql = $mysql->query("SELECT * FROM `notes` WHERE (`studentid` = $id)");
    $res = $sql->fetch_all();
    $if = 0;
    foreach ($res as $line){
        echo "<form class='notewindow-grid'>";
            echo "<input type='hidden' value='".$line[0]."'>";
            echo "<div><a class='point'>".date('j',strtotime($line[2]))." ".writeday(strtotime($line[2]))."<img style='margin-left: 2px; margin-bottom: -3px' src='img/pen.svg' width='16'></a></div>";
            echo "<textarea cols='25' disabled>".$line[3]."</textarea>";
            echo "<img class='point' src='img/close_red.svg' width='18'>";
        echo "</form>";
        if ($if < count($res) - 1){
            echo "<div class='notewindow-line'></div>";
            $if++;
        }
    }
function writeday($linedate){
    $cdate = (string)date('n',$linedate);
    switch($cdate){
        case "1":
            return "янв";
        case "2":
            return "фев";
        case "3":
            return "мар";
        case "4":
            return "апр";
        case "5":
            return "май";
        case "6":
            return "июн";
        case "7":
            return "июл";
        case "8":
            return "авг";
        case "9":
            return "сен";
        case "10":
            return "окт";
        case "11":
            return "ноя";
        case "12":
            return "дек";
    }

}
?>