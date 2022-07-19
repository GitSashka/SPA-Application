<?php
    session_start();
    require_once('connect.php');
    $sqlcheck = $mysql->query("SELECT `repeat` FROM `timetable` WHERE (`id` = $_POST[id])");
    $sqlc = $sqlcheck->fetch_all();
    $sql = $mysql ->query("UPDATE `timetable` SET `group` = '$_POST[groupid]', `date` = '$_POST[date]', `lection` = '$_POST[lection]', `aud` = '$_POST[aud]', `note` = '$_POST[note]', `time` = '$_POST[time]', `repeat` = '$_POST[repeat]' WHERE (`id` = '$_POST[id]')");
    $mysql->query("UPDATE `repeats` SET `groupid` = '$_POST[groupid]', `lection` = '$_POST[lection]', `aud` = '$_POST[aud]', `time` = '$_POST[time]', `date` = '$_POST[date]', `note` = '$_POST[note]', `repeating` = '$_POST[repeat]' WHERE (`id` = '$_POST[repeatid]')");
    if ($_POST[repeat] == 0 && $sqlc[0][0] != 0){ // Занятие становится одноразовым
        $mysql->query("DELETE FROM `repeats` WHERE (`id` = '$_POST[repeatid]')");
        $mysql->query("DELETE FROM `timetable` WHERE (`repeatid` = '$_POST[repeatid]' AND `id` != '$_POST[id]' AND `date` > NOW() - INTERVAL 1 DAY)");
    }
    if ($_POST[repeat] != 0 && $sqlc[0][0] == 0){ // Занятие становится регулярным
        $mysql->query("INSERT INTO `repeats` (`groupid`, `lection`, `aud`, `time`, `date`, `note`, `repeating`, `teacherid`) VALUES ('$_POST[groupid]', '$_POST[lection]', '$_POST[aud]', '$_POST[time]', '$_POST[date]', '$_POST[note]', '$_POST[repeat]', '$_SESSION[userid]')");
        $mysql->query("DELETE FROM `timetable` WHERE (`id` = '$_POST[id]')");
    }
    if (($_POST[repeat] == 7 && $sqlc[0][0] == 14) || ($_POST[repeat] == 14 && $sqlc[0][0] == 7)){ // Занятие изменяет свою периодичность
        $mysql->query("DELETE FROM `timetable` WHERE (`repeatid` = '$_POST[repeatid]')");
    }
    // sqlc - ДО выполнения запроса
    // $_POST - ПОСЛЕ выполнения запроса
?>