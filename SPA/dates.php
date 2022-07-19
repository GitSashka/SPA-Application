<?php
    session_start();
    require_once('connect.php');
    $groupid = $_POST['group'];
    $lection = $_POST['lection'];

    $months = ['январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь'];

    $sql = $mysql->query("SELECT `date`, `teacherid`, `id` FROM `timetable` WHERE (`group` = '$groupid' AND `lection` = '$lection') ORDER BY `date` ASC");
    $mas = $sql->fetch_all();
    if (count($mas) == 0){
        goto end;
    }

    $i = 1;
    $arr = array($months[date('m',strtotime($mas[0][0]))-1]);
    foreach($mas as $line){
        if ($months[date('m',strtotime($line[0]))-1] != $arr[$i-1]){
            $arr[$i] = $months[date('m',strtotime($line[0]))-1];
            $i++;
        }
    }
?>
<div class="monthdates">
    <?
        if (count($mas) == 0){
            goto end;
        }
        $j = 0;
        foreach ($arr as $line){
            echo "<div>";  /* первый див (объединяет все ячейки) */
                echo "<div class='month unselect'>".$line."</div>";
                echo "<div>";  /* второй див (отображает множество дат) */
                    echo "<div>";
                        foreach ($mas as $line2){
                            if ($months[date('m',strtotime($line2[0]))-1] == $line){
                                echo "<span class='point date unselect' "; if ($line2[1] != $_SESSION['userid']){echo "style='color: rgb(180, 180, 180)'";}echo"><input type='hidden' value='".$line2[1]."' id='teacherid'><input type='hidden' value='".$line2[2]."' id='lectionid'><input type='hidden' value='".$line2[0]."' id='datecheck'>".date('j',strtotime($line2[0]))."</span>"; /* один спан для отображения ячейки с датой */
                            }
                        }
                    echo "</div>";
                echo "</div>";
            echo "</div>";
            $j++;
        }
        echo "</div>";
        end:
    ?>
