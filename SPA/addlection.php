<?php
    session_start();
    require_once('connect.php');
    $lection = $_POST['lection'];
    $group = $_POST['groupid'];
    $aud = $_POST['aud'];
    $date = $_POST['date'];
    $note = $_POST['note'];
    $time = $_POST['time'];
    $repeat = $_POST['repeat'];
    $teacherid = $_SESSION['userid'];
    if ($repeat == 0){
        $sql = $mysql -> query("INSERT INTO `timetable` (`group`, `date`, `lection`, `aud`, `note`, `time`, `repeat`, `repeatid`, `teacherid`) VALUES ('$group', '$date', '$lection', '$aud', '$note', '$time', '0', '0', '$teacherid')");
    }
    else{
        $sql = $mysql -> query("INSERT INTO `repeats` (`groupid`, `lection`, `aud`, `time`, `date`, `note`, `repeating`, `teacherid`) VALUES ('$group', '$lection', '$aud', '$time', '$date', '$note', '$repeat', '$teacherid')");
    }
    if ($sql){
        echo "Данные успешно добавлены";
    }
    else{
        echo "Ошибка добавления данных. Проверьте правильность написания входных значений";
    }
?>