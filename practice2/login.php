<?php 
    file_put_contents('php/key.txt', "$accessOnTables"); // Насильно сохраняем таблицы, к которым имеется доступ, пока работаем в auth.php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link href="css.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="wrapperLogin">
        <div class="loginCard">
            <div class="headerCard">Login</div>
            <div class="loginSystem">
                <form action="auth.php" method="post">
                    <div class="userName">
                        <p>  
                            <i class="fa fa-user fa-3x" aria-hidden="false"></i>
                            <input type="text" name="userLogin" id="userLogin" required placeholder="UserLogin">
                        </p>
                    </div>
                    <div class="userPassword">
                        <p>
                            <i class="fa fa-lock fa-3x" aria-hidden="true"></i>
                            <input type="password" name="userPassword" id="userPassword" required placeholder="UserPassword">
                        </p>
                    </div>
                    <input type="submit" name="submit" value="Log in">
                </form>
                <a href="registration.php" class="registration">No Reg?</a>
            </div>
        </div>
    </div>
</body>
</html>