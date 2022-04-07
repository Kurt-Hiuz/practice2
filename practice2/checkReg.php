<?php
    require_once 'php/connect.php';  //подключение к БД с рута
    $userLogin = filter_var(trim($_POST['userLogin']), FILTER_SANITIZE_STRING); // Удаляет все лишнее и записываем значение в переменную //$login
    $userPassword = filter_var(trim($_POST['userPassword']), FILTER_SANITIZE_STRING);
    $cardFirstPart = '<div class="wrapperLogin">    
                        <div class="loginCard">
                            <div class="headerCard">Registration</div>
                            <div class="loginSystem">
                                <div class="userName">
                                    <p>
                                    ';
    $cardSecondPart = '</p>
                    </div>
                    <form action="registration.php" method="post">
                        <input type="submit" name="submit" value="Вернуться">
                    </form>
                </div>
            </div>
        </div>';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <style>
        <?=file_get_contents('css.css')?>   /* php - это наша радость и только так и подключаются стили */
        <?=file_get_contents('/font-awesome-4.7.0/css/font-awesome.min.css')?>  /* иконки */
    </style>
</head>
<body>
    <?php
    $mysql = new mysqli('localhost', 'root', '', 'uchet_pdd');  //подключаемся к БД с root'a
    $result1 = $mysql->query("SELECT * FROM `users` WHERE `userLogin` = '$userLogin' AND `userPassword` = '$userPassword'");
    $user1 = $result1->fetch_assoc(); // Конвертируем в массив
    if(!empty($user1)){
    	echo $cardFirstPart.'Данный логин уже используется!'.$cardSecondPart;    //выводим карточку с ошибкой
        exit(); //выходим с кода
    } else if(mb_strlen($userLogin) < 5 || mb_strlen($userLogin) > 90){  // Проверяем длину логина и пароля 
        echo $cardFirstPart.'<p style="text-align: center;">Недопустимая длина логина (Длина должна быть больше 5, но меньше 90 символов)</p>'.$cardSecondPart;    //выводим карточку с ошибкой
        exit(); //выходим с кода
    } else if(mb_strlen($userPassword) < 5){
        echo $cardFirstPart.'<p style="text-align: center;">Недопустимая длина пароля (Длина должна быть больше 5)</p>'.$cardSecondPart;    //выводим карточку с ошибкой
        exit(); //выходим с кода
    } else if(count($user) == 0){
        $queryToReg = $mysql->query("INSERT INTO users(userLogin, userPassword) VALUES ('$userLogin', '$userPassword')");
        $queryToCreateNewUserForSelectAllTables = mysqli_query($connect, "CREATE USER '$userLogin'@'%' IDENTIFIED BY '$userPassword'");
        $queryAllTable = mysqli_query($connect, "SHOW TABLES");
        while($queryAllNameTable = mysqli_fetch_assoc($queryAllTable)){
            mysqli_query($connect, "GRANT SELECT, DELETE, UPDATE, INSERT ON ".$queryAllNameTable['Tables_in_uchet_pdd']." TO '$userLogin'@'%'");
        }

        $result2 = $mysql->query("SELECT * FROM `users` WHERE `userLogin` = '$userLogin' AND `userPassword` = '$userPassword'");
        $RegMe = $result2->fetch_assoc(); // Конвертируем в массив
        if(!empty($RegMe)){
            echo $cardFirstPart.'Вы успешно зарегистрировались!'.$cardSecondPart;    //выводим карточку с успешной регистрацией
            $mysql->close();
        } else {
            echo $cardFirstPart.'Не вышло... Попробуйте позже'.$cardSecondPart;    //выводим карточку с ошибкой
            exit(); //выходим с кода
        }
    }
    ?>
</body>
</html>