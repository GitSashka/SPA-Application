var group = document.querySelectorAll('.group');
var timetable = document.querySelector('.timetable');
var currentgroupid = 0;
var gradesid = 0;
var groupstudents = document.querySelectorAll('.groupstudents');
var lect = document.querySelector('#lect');
let currentsession = false;
let userAgent = navigator.userAgent.toLowerCase();
let firefox = /firefox/.test(userAgent);
let blackout = document.querySelector('.blackout');
let modalclosed = true;
let userid = 0;

$.ajax({
    url: 'checkteacherid.php',
    success: (res)=>{
        userid = res;
    }
})

Vue.directive("click-outside", {
    bind(el, binding, vnode) {
      el.clickOutsideEvent = (event) => {
        if (!(el === event.target || el.contains(event.target))) {
          vnode.context[binding.expression](event);
        }
      };
      document.body.addEventListener("click", el.clickOutsideEvent);
    },
    unbind(el) {
      document.body.removeEventListener("click", el.clickOutsideEvent);
    },
});

for (let i = 0; i < group.length; i++){
    group[i].addEventListener("click", function(){
        add.togglebtn = false;
        add.togglewind = false;
        for (let j = 0; j < group.length; j++){
            group[j].style.backgroundColor = "white";
        }
        group[i].style.backgroundColor = "#ddddff";
        currentgroupid = i;
        update(currentgroupid);
    });
}

function update(current){
    $('.timetable').empty();
    $.ajax({
        url: 'timetable.php',
        type: 'post',
        data: {groupid: group[current].value},
        success:function (result){
            let div = document.createElement('div');
            div.className = "generated-table";
            div.innerHTML = result;
            timetable.append(div);
            add.togglebtn = true;
            giveforms();
        }
    });
}

function giveforms(){
    var editbtn = document.querySelectorAll('.timetable-time');
    var added = document.querySelectorAll('.added');
    var fclosebtn = document.querySelectorAll('.added .close');

    for (let i = 0; i < added.length; i++){
        editbtn[i].addEventListener('click',function (e){
            for (let j = 0; j < added.length; j++){
                added[j].style.display = 'none';
            }
            currentform = i;
            added[i].style.display = 'block';
            add.togglewind = false;

            summonblackout();
        });
        fclosebtn[i].addEventListener('click',function (){
            added[i].style.display = 'none';
            hideblackout();
        });

        added[i].style.top = editbtn[i].getBoundingClientRect().bottom - 34 + 'px';
    }
    managelinks(added.length);
}

function summonblackout(){
    blackout.style.display = 'block';
    blackout.style.zIndex = '1';
    setTimeout(()=> {blackout.style.opacity = '1'},0);
}

function hideblackout(){
    blackout.style.opacity = '0';
    setTimeout(()=>{ blackout.style.display = 'none'; blackout.style.zIndex = '0'; },300);
}

let selectdate = 0;

function managelinks(amount){
    let link = document.querySelectorAll('.timetable-gradelink');
    let lects = document.querySelectorAll('#lects');
    let groups = document.querySelectorAll('#groups');
    let btns = document.querySelectorAll('.groupstudents');
    let ids = document.querySelectorAll('#hiddenlectionid');

    for (let i = 0; i < amount; i++){
        link[i].addEventListener('click',()=>{
            document.querySelector('#tab-btn-2').checked = true;
            document.querySelector('#lect').innerHTML = lects[i].innerHTML;
            document.querySelector('#lect').value = lects[i].value;
            selectdate = ids[i].value;
            for (let j = 0; j < btns.length; j++){
                if (btns[j].value === groups[i].value){
                    btns[j].click();
                }
            }
        })
    }
}

for (let i = 0; i < groupstudents.length; i++){
    groupstudents[i].addEventListener("click", function(){
        for (let j = 0; j < groupstudents.length; j++){
            groupstudents[j].style.backgroundColor = "white";
        }
        groupstudents[i].style.backgroundColor = "#ddddff";
        gradesid = i;
        updatestudents(gradesid);
        document.querySelector('.sa-left').style.display = "none";
        document.querySelector('.sa-right').style.display = "none";
    });
}

let showmore = document.querySelectorAll('.showmore');
let hiddengroup = document.querySelectorAll('.group.ceased');
let hiddengroupstud = document.querySelectorAll('.groupstudents.ceased');
hiddengroup[0].style.opacity = "0";
hiddengroupstud[0].style.opacity = "0";

showmore[0].addEventListener('click',function(){
    showhidegroups(hiddengroup);
})

showmore[1].addEventListener('click',function(){
    showhidegroups(hiddengroupstud);
})

function showhidegroups(obj){
    if (obj[0].style.opacity === "0"){
        for (let i = 0; i < obj.length; i++){
            setTimeout(() => {
                obj[i].style.display = "block";
                setTimeout(() => {
                    obj[i].style.opacity = "1";
                },100);
            },100 * i);
        }
    }
    else{
        for (let i = obj.length - 1; i >= 0; i--){
            setTimeout(() => {
                obj[i].style.opacity = "0";
                setTimeout(() => {
                    obj[i].style.display = "none";
                },100);
            },100 * (obj.length - 1 - i));
        }
    }
}

lect.oninput = function(){
    updatestudents(gradesid);
}

function updatestudents(gradesid){
    $('.students-journal').empty();
    $('.monthdates').empty();
    $('.students-names').empty();
    $.ajax({
        url: 'students.php',
        type: 'post',
        data:{
            group: groupstudents[gradesid].value,
            lection: $('#lect').value,
        },
        success: function(result){
            if (result !== ''){
                $('.students-names').append(result);
                checkstudents();
                updatedates(gradesid);
            }
            else{
                senderror("Данные отсутствуют");
            }
        }
    })
}

let currentstud = -1;
let currentname = '';

function checkstudents(){
    let hidstudid = document.querySelectorAll('.hiddenstudentid');
    let studs = document.querySelectorAll('.students-names > a');
    let notewindow = document.querySelector('.notewindow');
    let close = document.querySelector('.notewindow-header > img');
    close.onclick = function(){
        notewindow.style.display = "none";
        hideblackout();
    }
    for (let i = 0; i < studs.length; i++){
        studs[i].addEventListener('click',function (){
            $('.notewindow-added').empty();
            notewindow.style.top = studs[i].getBoundingClientRect().bottom - 34 + "px";
            notewindow.style.display = "block";
            currentstud = i;
            summonblackout();
            $.ajax({
                url: 'notes.php',
                method: 'post',
                data: {
                    id: hidstudid[i].value,
                },
                success: function (result){
                    $('.notewindow-added').append(result);
                    updatenoteitems();
                }
            })
            currentname = studs[i].innerHTML.split(' ')[0];
            studname.name = changenamecase(currentname);
        })
    }
}

let studname = new Vue({
    el: '#student-name',
    data: {
        name: '',
    }
})

function changenamecase(name){
    switch (name.slice(-1)){
        case 'а':
            return name.slice(0, name.length - 1) + 'ы';
        case 'й':
            return name.slice(0, name.length - 1) + 'я';
        case 'я':
            return name.slice(0, name.length - 1) + 'и';
        default:
            return name.slice(0, name.length) + 'а';
    }
}

function updatenoteitems(){
    // Функционал кнопки редактирования
    let hidstudid = document.querySelectorAll('.hiddenstudentid');
    let edit = document.querySelectorAll('.notewindow-grid > div > a');
    let text = document.querySelectorAll('.notewindow-grid > textarea');
    let ids = document.querySelectorAll('.notewindow-grid > input');
    for (let i = 0; i < edit.length; i++){
        edit[i].addEventListener('click',function (){
            text[i].disabled = !text[i].disabled;
            text[i].focus();
        })
        let allowcall = true;
        text[i].addEventListener("keydown",function (e) {
            if (e.keyCode === 13){
                editnote();
                allowcall = false;
            }
        })
        text[i].addEventListener("blur",function (e) {
            if (allowcall){
                editnote();
            }
            else{
                allowcall = true;
            }

        })
        function editnote(){
            $.ajax({
                url: 'editnotes.php',
                type: 'post',
                data: {
                    id: ids[i].value,
                    note: text[i].value,
                },
                success: function (result) {
                    $('.notewindow-added').empty();
                    $.ajax({
                        url: 'notes.php',
                        method: 'post',
                        data: {
                            id: hidstudid[currentstud].value,
                        },
                        success: function (result) {
                            $('.notewindow-added').append(result);
                            updatenoteitems();
                        }
                    })
                }
            })
        }
    }
    // Функционал кнопки добавления
    let add = document.querySelector('.notewindow-footer > a');
    add.onclick = function(){
        $.ajax({
            url: 'addnotes.php',
            type: 'post',
            data: {
                studid: hidstudid[currentstud].value,
            },
            success: function () {
                $('.notewindow-added').empty();
                $.ajax({
                    url: 'notes.php',
                    method: 'post',
                    data: {
                        id: hidstudid[currentstud].value,
                    },
                    success: function (result) {
                        $('.notewindow-added').append(result);
                        updatenoteitems();
                    }
                })
            }
        })
    }
    // Функционал кнопки удаления
    let del = document.querySelectorAll('.notewindow-grid > img')
    for (let i = 0; i < del.length; i++){
        del[i].addEventListener('click',function(){
            $.ajax({
                url: 'delnotes.php',
                method: 'post',
                data: {
                    id: ids[i].value,
                },
                success: function () {
                    $('.notewindow-added').empty();
                    $.ajax({
                        url: 'notes.php',
                        method: 'post',
                        data: {
                            id: hidstudid[currentstud].value,
                        },
                        success: function (result) {
                            $('.notewindow-added').append(result);
                            updatenoteitems();
                        }
                    })
                }
            })
        })
    }

}

function updatedates(gradesid){
    $('.students-days').empty();
    $.ajax({
        url: 'dates.php',
        type: 'post',
        data:{
            group: groupstudents[gradesid].value,
            lection: lect.value,
        },
        success: function(result){
            if (result !== ''){
                $('.students-days').append(result);
                updatejournal(gradesid);
            }
            else{
                senderror("Данные отсутствуют");
            }
        }
    })
}

function updatejournal(gradesid){
    $('.students-journal').empty();
    $.ajax({
        url: 'journal.php',
        type: 'post',
        data:{
            group: groupstudents[gradesid].value,
            lection: lect.value,
        },
        success: function(result){
            $('.students-journal').append(result);
            scr.scrollLeft(document.querySelector('.students-journal').getBoundingClientRect().width);
            jou.scrollLeft(document.querySelector('.students-journal').getBoundingClientRect().width);
            managing();
            setgrades();
            document.querySelector('.sa-left').style.display = "block";
            document.querySelector('.sa-right').style.display = "block";
        }
    })
}

function clickdate(){
    let dates = document.querySelectorAll('#datecheck');
    let clickdates = document.querySelectorAll('.date');
    let checkid = document.querySelectorAll('#lectionid');
    let clickcheck = true;
    if (selectdate === 0){
        let curdate = new Date().toISOString().split('T')[0];
        for (let i = 0; i < dates.length; i++){
            if (dates[i].value === curdate){
                clickdates[i].click();
                jou.scrollLeft(i * 40 - 80);
                scr.scrollLeft(i * 40 - 80);
                clickcheck = false;
            }
        }
        if (clickcheck){
            jou.scrollLeft(dates.length * 40);
            scr.scrollLeft(dates.length * 40);
        }
    }
    else{
        let dateline = document.querySelectorAll('.date');
        for (let j = 0; j < dates.length; j++) {
            if (checkid[j].value === selectdate) {
                if (dateline[j].style.fontWeight !== "500"){
                    dateline[j].click();
                    jou.scrollLeft(j * 40 - 80);
                    scr.scrollLeft(j * 40 - 80);
                    selectdate = 0;
                }
            }
        }
    }
}

function managing(){
    var grades = document.querySelectorAll('.journal-grade');
    var dates = document.querySelectorAll('.date');
    let months = document.querySelectorAll('.month');
    let monthblock = document.querySelectorAll('.monthdates > div');
    let teacherid = document.querySelectorAll('#teacherid');
    let prev = -1;
    let re = false;
    for (let i = 0; i < dates.length; i++){
        dates[i].addEventListener("click",function (){
            for (let j = 0; j < grades.length; j++){
                grades[j].style.backgroundColor = "unset";
            }
            for (let j = 0; j < dates.length; j++){
                dates[j].style.borderBottom = "0px solid #0067d5";
                dates[j].style.backgroundImage = "linear-gradient(to top, rgba(229, 229, 255, 0.0), rgba(0, 103, 213, 0.0))";
                dates[j].style.fontWeight = "400";
                dates[j].style.color = "#6e6e6e";
                if (teacherid[j].value != userid){
                    dates[j].style.color = "rgb(180, 180, 180)";
                }
            }
            if (prev != i || re){
                dates[i].style.backgroundImage = "linear-gradient(to top, rgba(229, 229, 255, 1), rgba(229, 229, 255, 1)";
                dates[i].style.fontWeight = "500";
                dates[i].style.color = "#0067d5";
                var studlength = document.querySelectorAll('.students-names > a').length;
                var start = i;
                dates[i].style.borderBottom = "5px solid #0067d5";
                for (var j = 0; j < studlength; j++){
                   grades[start].style.backgroundColor = "rgb(229,229,255)";
                   start += dates.length;
                }
                re = false;
            }
            else{
                re = true;
            }
            prev = i;
        })
    }
    // tut
    for (let i = 0; i < months.length; i++){
        if (months[i].offsetWidth === monthblock[i].offsetWidth){
            months[i].style.fontSize = "8.5px";
            months[i].style.lineHeight = "20px";
        }
    }
    document.querySelector('.students-journal').style.width = document.querySelector('.monthdates').offsetWidth + "px";
    clickdate();
}

let currentgrade = -1;
let lectid;
let studid;
let currentgradeid = -1;

function setgrades(){
    let grades = document.querySelectorAll('.journal-grade');
    let gradewindow = document.querySelector('#grade');
    let close = document.querySelector('.gradewindow-header > .point');
    lectid = document.querySelectorAll('#hiddenlectid');
    studid = document.querySelectorAll('#hiddenstudid');
    close.onclick = function(){
        gradewindow.style.display = "none";
    }
    for (let i = 0; i < grades.length; i++){
        grades[i].addEventListener('click',function (){
            gradewindow.style.display = "block";
            gradewindow.style.top = grades[i].getBoundingClientRect().top - 8 + "px";
            gradewindow.style.left = grades[i].getBoundingClientRect().left + 43 + "px";
            currentgrade = i;
        })
    }
    if (!currentsession){
        let options = document.querySelectorAll('.gradewindow-grades > a');
        for (let i = 0; i < options.length; i++){
            options[i].addEventListener('click',function(){
                let bool = false;
                $.ajax({
                    url: 'checkgrade.php',
                    method: 'post',
                    data: {
                        id: lectid[currentgrade].value,
                    },
                    success:(res)=>{
                        currentgradeid = i + 1;
                        if (res === '1'){
                            bool = true;
                        }
                        if (!bool){
                            if (lectid[previousgrade].value === lectid[currentgrade].value){
                                bool = true;
                            }
                            else{
                                modalconfirm.open();
                            }
                        }
                        if (bool){
                            $.ajax({
                                url: 'changegrade.php',
                                method: 'post',
                                data: {
                                    lectid: lectid[currentgrade].value,
                                    studid: studid[currentgrade].value,
                                    gradeid: (i + 1),
                                },
                                success:function (){
                                    $('.journal-grade')[currentgrade].innerHTML = '';
                                    $('.journal-grade')[currentgrade].insertAdjacentHTML('afterbegin',updateicon(i+1));
                                    gradewindow.style.display = 'none';
                                    /*alert(lectid[currentgrade].value + ' ' + studid[currentgrade].value);*/
                                }
                            })   
                        }
                    }
                })
            })
        }
        currentsession = true;
    }
}

function updateicon(number){
    switch (number){
        case 11:
            return "1";
        case 10:
            return "2";
        case 9:
            return "3";
        case 8:
            return "4";
        case 7:
            return "5";
        case 6:
            return "<img src='img/close_red.svg' width='18'>";
        case 5:
            return "<img src='img/done.svg' width='18'>";
        case 4:
            return "<img src='img/circle_filled_orange.svg' width='18'>";
        case 3:
            return "<img src='img/circle_filled_yellow.svg' width='18'>";
        case 2:
            return "<img src='img/circle_filled_green.svg' width='18'>";
        case 1:
            return "<img src='img/circle.svg' width='18'>";
    }
}

var currentform = 0;

$('body').on('click', '#delete', function(e) {
    e.preventDefault();
    var added = document.querySelectorAll('.added');
    $.ajax({
        type: 'post',
        url: 'delete.php',
        data: $(added[currentform]).serialize(),
    })
        .done(function(){
            update(currentgroupid)
            add.togglewind = false;
            hideblackout();
        })
})

$serial = null;
let currentlectid = 0;
let currentrepeatid = 0;

$('body').on('submit', '#addform', function(e) {
    e.preventDefault();
    $temp = $(this).find("input[name=date]").val();
    if (new Date($temp).getDay() !== 0) {
        $serial = $(this).serialize();
        $.ajax({ // Форма добавления
            type: 'post',
            url: 'checktimetable.php',
            data: $serial,
        })
            .done(function (res) {
                if (res.split(' ')[0] === '-1'){
                    modal.edit = false;
                    modal.grouplectwarning = true;
                    currentlectid = res.split(' ')[1];
                    currentrepeatid = res.split(' ')[2];
                    modal.show();
                }
                else if (res === '0'){
                    add.togglewind = false;
                    $.ajax({
                        type: 'post',
                        url: 'addlection.php',
                        data: $serial,
                    })
                    .done(()=>{
                        update(currentgroupid);
                    });
                    hideblackout();
                }
                else if (res.split(' ')[0] === '1'){
                    modal.edit = false;
                    modal.grouplectwarning = false;
                    currentlectid = res.split(' ')[1];
                    currentrepeatid = res.split(' ')[2];
                    modal.show();
                }
            });
    }
    else{
        senderror("Нельзя ставить занятия на воскресенье");
    }
})

$('body').on('submit', '.added', function(e) {
    e.preventDefault();
    $temp = $(this).find("input[name=date]").val();
    if (new Date($temp).getDay() !== 0){
        $serial = $(this).serialize();

        $.ajax({ // Форма редактирования
            type: 'post',
            url: 'checktimetable.php',
            data: $serial,
        })
        .done((res)=>{
            if (res.split(' ')[0] === '-1'){
                modal.edit = true;
                modal.grouplectwarning = true;
                currentlectid = res.split(' ')[1];
                currentrepeatid = res.split(' ')[2];
                modal.show();
            }
            else if (res === '0'){
                add.togglewind = false;
                $.ajax({
                    type: 'post',
                    url: 'edit.php',
                    data: $serial,
                })
                setTimeout(()=>{ update(currentgroupid) },10);
                hideblackout();
            }
            else if (res.split(' ')[0] === '1'){
                modal.edit = true;
                modal.grouplectwarning = false;
                currentlectid = res.split(' ')[1];
                currentrepeatid = res.split(' ')[2];
                modal.show();
            }
        })
    }
    else{
        senderror("Нельзя ставить занятия на воскресенье");
    }
})

let desktopblackout = new Vue({
    el: '.blackouts',
    data(){
        return{
            showup: false,
        }
    },
    methods: {
        disappear: ()=>{
            desktopblackout.showup = false;
        }
    }
})

var add = new Vue({
    el: '.timetable-add',
    data() {
        return{
            togglewind: false,
            togglebtn: false,
        }
    },
    methods: {
        togglewindow(){
            this.togglewind = !this.togglewind;
            hideadded();
            if (this.togglewind){
                summonblackout();
                setTimeout(()=>{document.querySelector(".timetable-add").scrollIntoView(true)},0);
            }
            else{
                hideblackout();
            }
        }
    }
})

let previousgrade = 0;

let modalconfirm = new Vue({
    el: '.modal-confirm',
    data() {
        return{
            showmodalconf: false,
        }
    },
    methods: {
        close(){
            this.showmodalconf = false;
            hideblackout();
            desktopblackout.disappear();
        },
        open(){
            this.showmodalconf = true;
            summonblackout();
            desktopblackout.showup = true;
        },
        changegrade(){
            let gradewindow = document.querySelector('#grade');
            $.ajax({
                url: 'changegrade.php',
                method: 'post',
                data: {
                    lectid: lectid[currentgrade].value,
                    studid: studid[currentgrade].value,
                    gradeid: (currentgradeid),
                },
                success:function (){
                    $('.journal-grade')[currentgrade].innerHTML = '';
                    $('.journal-grade')[currentgrade].insertAdjacentHTML('afterbegin',updateicon(currentgradeid));
                    gradewindow.style.display = 'none';
                }
            })
            previousgrade = currentgrade;
            this.close();
        }
    }
})

let modal = new Vue({
    el: '.modal-window',
    data() {
        return{
            showmodal: false,
            grouplectwarning: false,
            edit: false,
        }
    },
    methods:{
        hide(){
            if (this.showmodal){
                this.showmodal = false;
            }
            blackout.style.display = "unset";
            setTimeout(()=>{ modalclosed = true; },1);
            desktopblackout.disappear();
        },
        show(){
            blackout.style.display = "fixed";
            setTimeout(()=>{ modal.showmodal = true; },0);
            desktopblackout.showup = true;
            modalclosed = false;
        },
        changelect(){
            if (this.edit){
                $.ajax({
                    method: 'post',
                    url: 'delete.php',
                    data: {
                        id: currentlectid,
                        repeatid: currentrepeatid,
                    },
                })
                .done(()=>{
                    $.ajax({
                        method: 'post',
                        url: 'edit.php',
                        data: $serial,
                    })
                    .done(()=>{
                        desktopblackout.disappear();
                        modal.hide();
                        setTimeout(()=>{ update(currentgroupid) },10);
                    })
                    hideblackout();
                })
            }
            else{
                $.ajax({
                    method: 'post',
                    url: 'delete.php',
                    data: {
                        id: currentlectid,
                        repeatid: currentrepeatid,
                    },
                })
            .done(()=>{
                $.ajax({
                    method: 'post',
                    url: 'addlection.php',
                    data: $serial,
                })
                .done(()=>{
                    desktopblackout.disappear();
                    modal.hide();
                    setTimeout(()=>{ update(currentgroupid); },50);
                })
                hideblackout();
            })
            }

        },
        addanyway(){
            if (this.edit){
                $.ajax({
                    method: 'post',
                    url: 'edit.php',
                    data: $serial,
                    success: ()=>{
                        modal.hide();
                        desktopblackout.disappear();
                        setTimeout(()=>{ update(currentgroupid) },50);
                    }
                })
                hideblackout();
            }
            else{
                $.ajax({
                    method: 'post',
                    url: 'addanyway.php',
                    data: $serial,
                    success: (res)=>{
                        if (res != ''){
                            senderror(res);
                        }
                        modal.hide();
                        desktopblackout.disappear();
                    }
                })
                .done(()=>{
                    update(currentgroupid);
                })
                hideblackout();  
            } 
        },
        close(){
            modal.hide();
            desktopblackout.disappear();
        }
    }
})

function hideadded(){
    let added = document.querySelectorAll('.added');
    for (let i = 0; i < added.length; i++){
        added[i].style.display = 'none';
    }
}

let jou = $(".journal-wrapper");
let scr = $(".students-days");
scr.mousedown(function () {
    let startX = this.scrollLeft + event.pageX;
    scr.mousemove(function () {
        scr.scrollLeft(startX - event.pageX);
        jou.scrollLeft(startX - event.pageX);
        return false;
    });
});

let sal = $(".sa-left");
let sar = $(".sa-right");
let click = true;

sal.click(function (){
    if (click){
        scr.scrollLeft(scr.scrollLeft() - 40);
        jou.scrollLeft(jou.scrollLeft() - 40);
    }
})

sar.click(function (){
    if (click){
        scr.scrollLeft(scr.scrollLeft() + 40);
        jou.scrollLeft(jou.scrollLeft() + 40);
    }
})

let sint;
let sint2;
let stim;
let stim2;

function salmd(){
    if (firefox){
        stim = setTimeout(() =>{
        sint2 = setInterval(() => jou.scrollLeft(jou.scrollLeft() - 7),5);
        sint = setInterval(() => scr.scrollLeft(scr.scrollLeft() - 7),5);
        click = false;},100);
    }
    else{
        stim = setTimeout(() =>{
        sint2 = setInterval(() => jou.scrollLeft(jou.scrollLeft() - 2),5);
        sint = setInterval(() => scr.scrollLeft(scr.scrollLeft() - 2),5);
        click = false;},100);
    }
}

function sarmd(){
    if (firefox){
        stim2 = setTimeout(() =>{
        sint2 = setInterval(() => jou.scrollLeft(jou.scrollLeft() + 7),5);
        sint = setInterval(() => scr.scrollLeft(scr.scrollLeft() + 7),5);
        click = false;
        },100);
    }
    else{
        stim2 = setTimeout(() =>{
        sint2 = setInterval(() => jou.scrollLeft(jou.scrollLeft() + 3),5);
        sint = setInterval(() => scr.scrollLeft(scr.scrollLeft() + 3),5);
        click = false;
        },100);
    }
}

$(window).mouseup(function (){
    scr.off("mousemove");
    clearTimeout(stim);
    clearTimeout(stim2);
    clearAllInterval();
    setTimeout(()=> { click = true },1);
});

function senderror(string){
    $('.error').remove();
    let div = document.createElement("div");
    div.className = "error";
    div.innerHTML = string;
    div.addEventListener('click',()=>{
        $('.error').remove();
    })
    document.querySelector("body").append(div);
    setTimeout(()=> { div.style.top = "5px"; },300);
    setTimeout(()=> { div.style.top = "-100px"; },4000);
    setTimeout(()=> { $('.error').remove(); },4300);
}

document.onclick = (e)=>{
    let editbtn = document.querySelectorAll('.timetable-time');
    let added = document.querySelectorAll('.added');

    for (let i = 0; i < added.length; i++){
        let insideform = e.composedPath().includes(added[i]);
        let insideedit = e.composedPath().includes(editbtn[i]);
    
        if (!insideform && !insideedit){
            added[i].style.display = 'none';
        }
    }

    const insideadd = e.composedPath().includes(document.querySelector(".timetable-add > a"));
    const insideaddwind = e.composedPath().includes(document.querySelector("#addform"));
    const insideradbutts = e.composedPath().includes(document.querySelector(".timetable-add-grid-radiobutts"));
    const insidemodal = e.composedPath().includes(document.querySelector(".modal-window"));
    const onblackout = e.composedPath().includes(document.querySelector(".desktopblackout"));
    const onbutton = e.composedPath().includes(document.querySelectorAll(".modal-button")[2]);
    const onclose = e.composedPath().includes(document.querySelector(".modal-header > img"));
    const oncancel = e.composedPath().includes(document.querySelector("#cancel"));
    if (!insideadd && !insideaddwind && !insideradbutts && !insidemodal && !onblackout && !onbutton && !onclose && !oncancel){
        if (modalclosed){
            add.togglewind = false;
        }
    }

    if (onblackout){
        modalconfirm.showmodalconf = false;
    }

    let studs = document.querySelectorAll('.students-names > a');
    let notewind = document.querySelector('.notewindow');
    const insidenotes = e.composedPath().includes(notewind);
    let instud = false;
    for (let i = 0; i < studs.length; i++){
        if (e.composedPath().includes(studs[i])){
            instud = true;
        }
    }
    if (!insidenotes && !instud){
        notewind.style.display = 'none';
    }

    let wrap = document.querySelector('.journal-wrapper');
    let grade = document.querySelector('.gradewindow');
    const insidewrap = e.composedPath().includes(wrap);
    const insidegrade = e.composedPath().includes(grade);
    if (!insidewrap && !insidegrade){
        grade.style.display = 'none';
    }

    blackout.addEventListener('click',()=>{
        for (let i = 0; i < added.length; i++){
            added[i].style.display = 'none';
        }
        hideblackout();
    })

}

let ttaddbtn = document.querySelector('.timetable-add-btn');
ttaddbtn.addEventListener('click',()=>{
    if (document.querySelector("#addform") != null)
    window.scrollTo(0, document.querySelector("#addform").getBoundingClientRect().top);
})

let tabbtn1 = document.querySelector('#tab-btn-1');
let tabbtn2 = document.querySelector('#tab-btn-2');

window.addEventListener('resize',()=>{
    hideblackout();
})

// tabbtn1.addEventListener('click',()=>{
//     history.pushState(null, null, "schedule");
// });

// tabbtn2.addEventListener('click',()=>{
//     history.pushState(null, null, "students");
// });







