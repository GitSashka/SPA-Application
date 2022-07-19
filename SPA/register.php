<?php
    session_start();
    $mysql = new mysqli('127.0.0.1', 'root', '', 'spa');
?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Регистрация</title>
        <link rel="stylesheet" href="style/logreg.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    </head>
    <body>
    <div class="login">
        <form class="reg-field" method="post">
            <div class="login-field-input">
                <span>Введите своё имя:</span>
                <input type="text" placeholder="Иван" name="fname">
            </div>
            <div class="login-field-input">
                <span>Введите свою фамилию:</span>
                <input type="text" placeholder="Иванов" name="lname">
            </div>
            <div class="login-field-input">
                <span>Введите почту:</span>
                <input type="text" placeholder="example@mail.ru" name="email">
            </div>
            <div class="login-field-input">
                <span>Введите пароль:</span>
                <input type="password" placeholder="********" name="pass">
            </div>
            <input name="but" type="submit" value="Зарегистрироваться">
            <a class="small" href="login.php">Войти</a>
        </form>
    </div>
    <script src="script/jquery-3.6.0.js"></script>
    </body>
    <script>
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
    </script> 
    </html>
<?php
if (isset($_POST['but'])){
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    if (mb_strlen($pass) < 6){
        echo "<script>senderror('Пароль должен быть не менее 6 символов');</script>";
        goto end;
    }

    $sqledit = $mysql->query("INSERT INTO `users` (`email`, `pass`, `firstname`, `lastname`) VALUES ('$email','$pass','$fname','$lname')");
    echo "<script type=\"text/javascript\">location.href=\"login.php\"</script>";

    end:
}
?>