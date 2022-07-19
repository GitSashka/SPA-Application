<?php
    session_start();
    if (!$_SESSION['authorized']){
        echo "<script type=\"text/javascript\">location.href=\"login.php\"</script>";
    }
    /*$mysql = mysqli_connect('localhost', 'root', '', 'spa');*/
    require_once('connect.php');
?>

<!doctype html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Главная</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style/normalize.css">
    <link rel="stylesheet" href="style/style.css">
    <link type="Image/x-icon" href="/img/pen.svg" rel="icon">

</head>
<body>
    <?php
        // Вывод имени и фамилии в заголовке
        $id = $_SESSION['userid'];
        $sql = $mysql->query("SELECT * FROM `users` WHERE (`id` = '$id')");
        $mas = $sql->fetch_all();
        $str = $mas[0][3]." ".$mas[0][4];
        // Запросы для добавления опций в окне добавления занятий
        $sql = $mysql->query("SELECT * FROM `lections`");
        $mas = $sql->fetch_all();
        $maslections = $mas;
    ?>
    <header>
        <span class="user">Вы авторизованы как <?=$str?></span>
        <form method="post">
            <input type="submit" value="Выйти" name="exit">
        </form>
    </header>
    <main>
        <div class="blackout"></div>
        <div class="blackouts">
            <transition name="fade">
                <div class="desktopblackout" v-if="showup" v-on:click="disappear()"></div>
            </transition>
        </div>
        <div class="tabs">
            <input type="radio" name="tab-btn" id="tab-btn-1" value="" checked>
            <label for="tab-btn-1">Расписание</label>
            <input type="radio" name="tab-btn" id="tab-btn-2" value="">
            <label for="tab-btn-2">Студенты</label>
            <div id="content-1">
                <!-- Здесь вывод всех групп, доступных преподавателю -->
                <div class="groups">
                    <button class='group small' name='groupid' value='0'>Все группы</button>
                    <?php
                    $sql2 = $mysql->query("SELECT * FROM `groups` INNER JOIN `relations` ON `relations`.`groupid` = `groups`.`id` WHERE (`relations`.`userid` = $id);");
                    $mas2 = $sql2->fetch_all();
                    for ($i = 0; $i < count($mas2); $i++){
                        if ($i > 2){
                            echo "<button class='group ceased' name='groupsdtid' value='".$mas2[$i][0]."'>".$mas2[$i][1]."</button>";
                        }
                        else{
                            echo "<button class='group' name='groupsdtid' value='".$mas2[$i][0]."'>".$mas2[$i][1]."</button>";
                        }
                    }
                    echo "<button class='showmore point'>...</button>";
                    ?>
                </div>
                <div class="timetable">
                    <!-- Сюда загужается таблица из бд через ajax -->
                </div>
                <div class="timetable-add">
                    <form id="addform" v-show="togglewind" class="timetable-add-window">
                        <svg class="timetable-add-arrow" width="20" height="20">
                            <polygon points="11,-1 0,10 11,21" style="fill:white;" />
                            <line x1="0" y1="10" x2="9.5" y2="0" stroke="gray" stroke-width="1" />
                            <line x1="0" y1="10" x2="9.5" y2="20" stroke="gray" stroke-width="1" />
                        </svg>
                        <div class="timetable-add-header">
                            <span>Добавить занятие:</span>
                            <img src="img/close.svg" class="close" @click="togglewindow">
                        </div>
                        <div class="timetable-add-grid">
                            <span>Предмет:</span><select name="lection" type="text" id="lects"><?php foreach ($maslections as $line => $value){echo "<option value=".$value[0].">".$value[2]."</option>";} ?></select>
                            <span>Группа:</span><select name="groupid" type="text" id="groups"><?php foreach ($mas2 as $line => $value){echo "<option value=".$value[0].">".$value[1]."</option>";} ?></select>
                            <span>Аудитория:</span><input name="aud" type="text" required>
                            <span>Дата:</span><input name="date" type="date" required id="dates">
                            <span>Время:</span><input name="time" type="time" list="times" required>
                            <span>Примечание:</span><input name="note" type="text">
                            <span>Повтор:</span><div class="timetable-add-grid-radiobutts">
                                <input type="radio" id="Choice1" name="repeat" value="0" checked><label for="Choice1">один раз</label>
                                <input type="radio" id="Choice2" name="repeat" value="7"><label for="Choice2">еженедельно</label>
                                <input type="radio" id="Choice3" name="repeat" value="14"><label for="Choice3">каждые 2 недели</label>
                            </div>
                        </div>
                        <div class="timetable-add-footer">
                            <a class="timetable-add-btn" @click="togglewind = !togglewind; hideblackout()">Отменить</a>
                            <button class="confirm">Добавить</button>
                        </div>
                    </form>
                    <a class="timetable-add-btn unselect" v-show="togglebtn" @click='togglewindow'>Добавить занятия</a>
                </div>
                <div class="modal-window-wrapper">
                    <div class="modal-window" v-if="showmodal" v-click-outside="hide">
                        <div class="modal-header">
                            <span>Внимание</span>
                            <img class="point" src="/img/close.svg" alt="X" @click="desktopblackout.showup = false; hide()">
                        </div>
                        <div class="modal-content">
                            <span v-if="!grouplectwarning">У вас уже запланировано занятие на это время. Выберите одно из следующих действий:</span>
                            <span v-if="grouplectwarning">У этой группы уже есть занятие на это время. Выберите одно из следующих действий:</span>
                        </div>
                        <div class="modal-footer">
                            <button class="modal-button" @click="addanyway()" v-if="!grouplectwarning">Провести параллельно</button>
                            <button class="modal-button" @click="changelect()">Изменить занятие</button>
                            <button class="modal-button" @click="close()">Отмена</button>
                        </div>
                    </div>
                </div>
              <!-- Место для отладки -->
              <!-- <button onclick="modal.show(); desktopblackout.showup = true;">showmodal</button> -->
            </div>
            <div id="content-2">
                <div class="modal-confirm" v-if="showmodalconf" v-click-outside="close">
                    <div class="modal-confirm-header"><span>Подтвердите действие</span><img @click="close()" src="img/close.svg" class="point"></div>
                    <div class="modal-confirm-content"><span>Вы не проводили эту лекцию, выберите одно из следующих действий:</span></div>
                    <div class="modal-confirm-footer">
                        <button class="modal-button" @click="changegrade()">Изменить оценку</button>
                        <button id="cancel" class="modal-button" @click="close()">Отмена</button>
                    </div>
                </div>
                <div class="groups">
                    <?php
                        // $sql2 = $mysql->query("SELECT * FROM `groups` INNER JOIN `relations` ON `relations`.`groupid` = `groups`.`id` WHERE (`relations`.`userid` = $id);");
                        $sql2 = $mysql->query("SELECT * FROM `groups`");
                        $mas2 = $sql2->fetch_all();
                        for ($i = 0; $i < count($mas2); $i++){
                            if ($i > 2){
                                echo "<button class='groupstudents ceased' name='groupsdtid' value='".$mas2[$i][0]."'>".$mas2[$i][1]."</button>";
                            }
                            else{
                                echo "<button class='groupstudents' name='groupsdtid' value='".$mas2[$i][0]."'>".$mas2[$i][1]."</button>";
                            }
                        }
                        echo "<button class='showmore point'>...</button>";
                    ?>
                    <div class="lectfield">
                        <select name="lect" type="text" id="lect" class="lect"><?php foreach ($maslections as $line => $value){echo "<option value=".$value[0].">".$value[2]."</option>";} ?></select>
                    </div>
                </div>
                <div class="students">
                    <div class="scroll-arrow">
                        <img class="sa-left point unselect" src="img/play_arrow.svg" onmousedown="salmd()">
                    </div>
                    <div class="students-days">

                    </div>
                    <div class="scroll-arrow">
                        <img class="sa-right point unselect" src="img/play_arrow.svg" onmousedown="sarmd()">
                    </div>
                    <div class="students-names"></div>
                    <div class="journal-wrapper">
                        <div class="students-journal">

                        </div>
                    </div>
                    <div></div>
                </div>
                <div class="notewindow timetable-add-window">
                    <svg class="timetable-add-arrow" width="20" height="20">
                        <polygon points="10,0 0,10 10,20" style="fill:white;" />
                        <line x1="0" y1="10" x2="9.5" y2="0" stroke="gray" stroke-width="1" />
                        <line x1="0" y1="10" x2="9.5" y2="20" stroke="gray" stroke-width="1" />
                    </svg>
                    <div class="notewindow-header"><span id="student-name">Напоминания для {{ name }}:</span><img width="20" height="20" class="point" src="img/close.svg"></div>
                    <div class="notewindow-added"></div>
                    <div class="notewindow-footer"><a class="point">Добавить...</a></div>
                </div>

                <div v-if="show" class="gradewindow" id="grade">
                    <div class="gradewindow-header">
                        <span>Отметка:</span>
                        <img class="point" @click="show = false" src="img/close.svg" width="18">
                    </div>
                    <svg class="timetable-add-arrow" width="20" height="20">
                        <polygon points="11,-1 0,10 11,21" style="fill:white;" />
                        <line x1="0" y1="10" x2="9.5" y2="0" stroke="gray" stroke-width="1" />
                        <line x1="0" y1="10" x2="9.5" y2="20" stroke="gray" stroke-width="1" />
                    </svg>
                    <div class="gradewindow-grades">
                        <a><img class='point' src="img/circle.svg"></a>
                        <a><img class='point' src="img/circle_filled_green.svg"></a>
                        <div></div>
                        <a><img class='point' src="img/circle_filled_yellow.svg"></a>
                        <a><img class='point' src="img/circle_filled_orange.svg"></a>
                        <a><img class='point' src="img/done.svg"></a>
                        <div></div>
                        <div></div>
                        <div></div>
                        <a><img class='point' src="img/close_red.svg"></a>
                        <a class='point'>5</a>
                        <a class='point'>4</a>
                        <a class='point'>3</a>
                        <a class='point'>2</a>
                        <a class='point'>1</a>
                    </div>
                </div>
                <!-- Ещё одно место для отладки -->
                <!-- <button onclick="modalconfirm.open()">showmodal</button> -->
            </div>
    </main>
    <script src="script/vue.js"></script>
    <script src="script/jquery-3.6.0.js"></script>
    <script src="script/smthnotmine.js"></script>
    <!-- <script type="module" src="script/v-click-outside.js"></script> -->
    <script src="script/script.js"></script>
    <datalist id="times">
        <option label="0 Пара">07:30</option>
        <option label="1 Пара">09:00</option>
        <option label="2 Пара">10:40</option>
        <option label="3 Пара">12:30</option>
        <option label="4 Пара">14:00</option>
        <option label="5 Пара">15:30</option>
    </datalist>
</body>
</html>
<?php
    if (isset($_POST['exit'])){
        $_SESSION['authorized'] = false;
        echo "<script type=\"text/javascript\">location.href=\"login.php\"</script>";
    }
?>

