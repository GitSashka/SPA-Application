<?php
    session_start();
    $gid = $_POST['groupid'];
/*    $mysql = new mysqli('localhost', 'root', '', 'spa');*/
    require_once('connect.php');

    /*    Здесь записи в таблицу добавляютяся исходя из занятий с повторами*/
    addrows($mysql);
    function addrows($mysql){
        $sql = $mysql -> query("SELECT * FROM `repeats`");
        $repeats = $sql -> fetch_all();
/*        $date=date_create(strtotime(date('yyyy.mm.dd')));
        $date->modify('-1 day');*/
        foreach ($repeats as $line){

            $date=date_create(strtotime(date('yyyy.mm.dd')));
            $date->modify('-1 day');

            for ($i = 0; $i <= 8; $i++){

                $checkdate = date_create($line[5]);

                if ($line[7] == 7){
                    if ($checkdate -> format("D") == $date -> format('D')){
                        $sqldate = $date->format('Y-m-d');
                        $sql = $mysql -> query("SELECT `id` FROM `timetable` WHERE (`date` = '$sqldate' AND `time` = '$line[4]')");
                        $check = $sql -> fetch_all();
                        if (count($check) == 0){
                            $mysql -> query("INSERT INTO `timetable` (`group`, `date`, `lection`, `aud`, `note`, `time`, `repeat`, `repeatid`, `teacherid`) VALUES ('$line[1]', '$sqldate', '$line[2]', '$line[3]', '$line[6]', '$line[4]', '$line[7]', '$line[0]', '$line[8]')");
                        }
                    }
                }

                if ($line[7] == 14){
                    $datecheck = $checkdate; // Дата из запроса
                    for ($j = 1; $j <= 18; $j++){
                        if ($date -> format('Y-m-d') == $datecheck->format('Y-m-d')){
                            $sqldate = $date->format('Y-m-d');
                            $sql = $mysql -> query("SELECT `id` FROM `timetable` WHERE (`date` = '$sqldate' AND `time` = '$line[4]')");
                            $check = $sql -> fetch_all();
                            if (count($check) == 0){
                                $mysql -> query("INSERT INTO `timetable` (`group`, `date`, `lection`, `aud`, `note`, `time`, `repeat`, `repeatid`, `teacherid`) VALUES ('$line[1]', '$sqldate', '$line[2]', '$line[3]', '$line[6]', '$line[4]', '$line[7]', '$line[0]', '$line[8]')");
                            }
                        }
                        date_add($datecheck,date_interval_create_from_date_string("14 days"));
                    }
                }
                date_add($date, date_interval_create_from_date_string("1 day"));
            }
        }
    }

    $string = '';
    $months = [
        'января',
        'февраля',
        'марта',
        'апреля',
        'мая',
        'июня',
        'июля',
        'августа',
        'сентября',
        'октября',
        'ноября',
        'декабря'
    ];

    $sql = $mysql->query("SELECT * FROM `lections`");
    $mas = $sql->fetch_all();
    $maslections = $mas;

    $sql2 = $mysql->query("SELECT * FROM `groups` INNER JOIN `relations` ON `relations`.`groupid` = `groups`.`id` WHERE (`relations`.`userid` = ".$_SESSION['userid'].");");
    $mas2 = $sql2->fetch_all();

    if ($gid == '0'){
        $sql = $mysql->query("SELECT * FROM `timetable` INNER JOIN `lections` ON `timetable`.`lection` = `lections`.`id` INNER JOIN `groups` on `timetable`.`group` = `groups`.`id` WHERE (`timetable`.`date` >= NOW() - INTERVAL 1 DAY AND `timetable`.`teacherid` = ".$_SESSION['userid'].") ORDER BY `timetable`.`date` ASC, `timetable`.`time` ASC;"); // Должен быть -1 день
    }

    else{
        $sql = $mysql->query("SELECT * FROM `timetable` INNER JOIN `lections` ON `timetable`.`lection` = `lections`.`id` INNER JOIN `groups` on `timetable`.`group` = `groups`.`id` WHERE (`timetable`.`date` >= NOW() - INTERVAL 1 DAY AND `group` = $gid AND `timetable`.`teacherid` = ".$_SESSION['userid'].") ORDER BY `timetable`.`date` ASC, `timetable`.`time` ASC;"); // Должен быть -1 день
    }

    $mas = $sql->fetch_all();
    echogrid($maslections, $mas2, $months, $mas);

    function echogrid($maslections, $mas2 ,$months ,$mas){
        $prevdate = 0;
        foreach ($mas as $line) {
            $month = date('m', strtotime($line[2])) - 1;
            if ($prevdate != date('d-m-y',strtotime($line[2]))){
                echo "<div class='grid'><span class='bold cell'>".date('j',strtotime($line[2]))."&nbsp<span class='longdate'>".$months[$month]."</span><span class='shortdate'>".writeshortmonth($month + 1)."</span></span>
                      <span class='small lg cell'>".writeday(strtotime($line[2]))."</span>
                      <a class='cell timetable-time unselect'><img class='pen' src='img/pen.svg'>".date('H:i',strtotime($line[6]))."</a>
                      <a class='cell left timetable-link'><span class='timetable-gradelink point'><span class='timetable-longname'>".$line[11]."</span><span class='timetable-shortname'>".$line[12]."</span>, ауд. ".$line[4].", ".$line[14]; if ($line[5] != '') {echo "<span class='desc'>, ".$line[5]."</span>";} echo "</a></div>";}
            else{
                echo "<div class='grid'><span class='bold cell top'></span>
                      <span class='small lg cell top'></span>
                      <a class='cell top timetable-time unselect'><img class='pen' src='img/pen.svg'>".date('H:i',strtotime($line[6]))."</a>
                      <a class='cell top left timetable-link'><span class='timetable-gradelink point'><span class='timetable-longname'>".$line[11]."</span><span class='timetable-shortname'>".$line[12]."</span>, ауд. ".$line[4].", ".$line[14]; if ($line[5] != '') {echo ", ".$line[5];} echo "</span></a></div>";
            }
            echo "<input type='hidden' value='".$line[0]."' id='hiddenlectionid'>";
            $prevdate = date('d-m-y',strtotime($line[2]));
            writeaddform($maslections, $mas2, $line[0], $line[3], $line[12], $line[1], $line[14], $line[4], $line[2], $line[6], $line[5], $line[7], $line[8]);
        }/*$maslections, $mas2,               $id      $lectionid $lection   $groupid  $group     $aud      $date     $time     $note     $repeat   $repeatid*/
    }
    function writeday($linedate){
        if (date('Y.m.d',$linedate) == date('Y.m.d')){
            return "сегодня";
        }
        else{
            $cdate = (string)date('D',$linedate);
            switch($cdate){
                case "Mon":
                    return "Пн";
                case "Tue":
                    return "Вт";
                case "Wed":
                    return "Ср";
                case "Thu":
                    return "Чт";
                case "Fri":
                    return "Пт";
                case "Sat":
                    return "Сб";
                case "Sun":
                    return "Вс";
            }
        }
    }
    function writeshortmonth($linedate){
        switch($linedate){
            case 1:
                return "янв";
            case 2:
                return "фев";
            case 3:
                return "мар";
            case 4:
                return "апр";
            case 5:
                return "май";
            case 6:
                return "июн";
            case 7:
                return "июл";
            case 8:
                return "авг";
            case 9:
                return "сен";
            case 10:
                return "окт";
            case 11:
                return "ноя";
            case 12:
                return "дек";
        }
    
    }

    function writeaddform($maslections, $mas2, $id, $lectionid ,$lection, $groupid, $group, $aud, $date, $time, $note, $repeat, $repeatid){
        echo '<form id="editform" class="timetable-add-window added">
                        <svg class="timetable-add-arrow" width="20" height="20">
                            <polygon points="11,-1 0,10 11,21" style="fill:white;" />
                            <line x1="0" y1="10" x2="9.5" y2="0" stroke="gray" stroke-width="1" />
                            <line x1="0" y1="10" x2="9.5" y2="20" stroke="gray" stroke-width="1" />
                        </svg>
                        <div class="timetable-add-header">
                            <span>Редактировать занятие:</span>
                            <img src="img/close.svg" class="close">
                        </div>
                        <div class="timetable-add-grid">
                            <input type="text" value="'.$id.'" name="id" hidden class="formhiddenid">
                            <span>Предмет:</span><select value="'.$lectionid.'" name="lection" type="text" id="lects">'.$lection; foreach ($maslections as $line => $value){if ($value[0] == $lectionid){echo "<option value=".$value[0]." selected>".$value[2]."</option>";} else echo "<option value=".$value[0].">".$value[2]."</option>";} echo '</select>
                            <span>Группа:</span><select value="'.$groupid.'" name="groupid" type="text" id="groups">'.$group; foreach ($mas2 as $line => $value){if($value[0] == $groupid){echo "<option value=".$value[0]." selected>".$value[1]."</option>";} else echo "<option value=".$value[0].">".$value[1]."</option>";} echo '</select>
                            <span>Аудитория:</span><input value="'.$aud.'" name="aud" type="text" required>
                            <span>Дата:</span><input value="'.$date.'" name="date" type="date" required id="dates">
                            <span>Время:</span><input value="'.$time.'" name="time" type="time" list="times" required>
                            <span>Примечание:</span><input value="'.$note.'" name="note" type="text">
                            <span>Повтор:</span><div class="timetable-add-grid-radiobutts">
                                <input type="radio" id="Choice1" name="repeat" value="0" ';if ($repeat == 0){echo "checked";} echo'><label for="Choice1">один раз</label>
                                <input type="radio" id="Choice2" name="repeat" value="7" ';if ($repeat == 7){echo "checked";} echo'><label for="Choice2">еженедельно</label>
                                <input type="radio" id="Choice3" name="repeat" value="14" ';if ($repeat == 14){echo "checked";} echo'><label for="Choice3">каждые 2 недели</label>
                            </div>
                            <input type="text" value="'.$repeatid.'" name="repeatid" hidden class="formhiddenid">
                        </div>
                        <div class="timetable-add-footer">
                            <a id="delete" class="timetable-add-btn">Отменить</a>
                            <button class="change">Изменить</button>
                        </div>
                    </form>';
    }
?>