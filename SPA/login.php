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
    <title>Авторизация</title>
    <link rel="stylesheet" href="style/logreg.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login">
        <form class="login-field" method="post">
            <div class="login-field-input">
                <span>Введите почту:</span>
                <input type="text" placeholder="example@mail.ru" name="email">
            </div>
            <div class="login-field-input">
                <span>Введите пароль:</span>
                <input type="password" placeholder="********" name="pass">
            </div>
            <input name="but" type="submit" value="Войти">
            <a class="small" href="register.php">Зарегистрироваться</a>
        </form>
    </div>
    <script src="script/jquery-3.6.0.js"></script>
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
</body>
</html>
<?php
    if (isset($_POST['but'])){

        if ($_POST['email'] == ''){
            goto end;
        }

        $email = $_POST['email'];
        $pass = $_POST['pass'];

        $result = $mysql->query("SELECT * FROM `users` WHERE `email` = '$email'");
        $mas = $result -> fetch_all();

        if ($mas[0][2] == $pass){
            $_SESSION['authorized'] = true;
            $_SESSION['userid'] = $mas[0][0];
            echo "<script type=\"text/javascript\">location.href=\"index.php\"</script>";
        }
        else{
            // echo "<script type=\"text/javascript\">alert('Введён неверный пароль');</script>";
            echo "<script>
                senderror('Введён неверный логин или пароль');
            </script>";
        }
        end:
    }
?>
