<?php
    $card = '<div class="wrapperLogin">    
                <div class="loginCard">
                    <div class="headerCard">Login</div>
                    <div class="loginSystem">
                        <div class="userName">
                            <p> 
                                Вы неверно ввели логин и пароль!
                            </p>
                        </div>
                        <form action="login.php" method="post">
                            <input type="submit" name="submit" value="Вернуться">
                        </form>
                    </div>
                </div>
            </div>';    //Создание карточки с выводом ошибки
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Практика</title>
    <style>
        <?=file_get_contents('css.css')?>   /* php - это наша радость и только так и подключаются стили */
        <?=file_get_contents('/font-awesome-4.7.0/css/font-awesome.min.css')?>  /* иконки */
    </style>
</head>
<body>
    <?php
    require_once 'php/connect.php';  //подключение к БД с рута
    if(isset($_POST['userLogin'])){  //когда есть $_POST['userLogin']
            $userLogin = filter_var(trim($_POST['userLogin']), FILTER_SANITIZE_STRING);  //получаю и фильтрую логин от пробелов
            $userPassword = filter_var(trim($_POST['userPassword']), FILTER_SANITIZE_STRING);  //получаю и фильтрую пароль от пробелов
            $accessOnTables = [];   //пустой массив с названиями таблиц, к которым имеется доступ
            
            $mysql = new mysqli('localhost', 'root', '', 'uchet_pdd');  //подключаемся к БД с root'a
            $result1 = $mysql->query("SELECT * FROM `users` WHERE `userLogin` = '$userLogin' AND `userPassword` = '$userPassword'");    //делаем запрос на наших пользователей с логином и паролем
            $user1 = $result1->fetch_assoc(); // Конвертируем в массив
            if(!empty($user1)){
                $querySHOWGRANTS = mysqli_query($connect, "SHOW GRANTS FOR $userLogin");   //запрос на получение привилегий конкретного пользователя по логину
                if(!empty($querySHOWGRANTS)){
                    $flag = false;  //устанавливаем флажок
                    if($querySHOWGRANTS != false){  //если логин окажется неправильным, то запрос будет false. Мы рассматриваем вариант с верными логином
                        $emptyStr = "<br> ";    //пустая строка для определения названий таблиц
                        while($grants = mysqli_fetch_array($querySHOWGRANTS)){  //распаковываем массив данных, полученных с запроса, и работаем до его конца
                            if($flag){  //флажок в положении true ***
                                $grantsON = $grants[0];
                                $grantsON = substr($grantsON,5,strpos($grantsON, "ON")-6).'<br> ';
                                $grants[0] = substr($grants[0], strpos($grants[0], ".")+2, strpos($grants[0], "TO")-strpos($grants[0], ".")-4); //нарезаем полученный массив данных по табличкам, минуя TO и точки
                                $emptyStr = $emptyStr.$grants[0]."%".$grantsON;   //конкатенируем с пустой строкой, чтобы в итоге у нас было " таблица1 таблица2 ... таблицаn "
                            }
                            $flag = true;   //*** сразу ставим флажок, минуя первое значение, которое в СУБД показывает отношение к БД, а не к таблицам. Крч, первое значение нам не сдалось
                        }   //закрываем цикл. В итоге имеем список всех доступных таблиц
                        if(($userLogin == 'admin1')&&($userPassword == 'admin1')){
                            $emptyStr .= 'admin';
                        }
                        file_put_contents('php/key.txt', $emptyStr);    //пихаем это в промежуточный файл. Для злоумышленников тут нет ничего интересного.
                        $strOfTables = file_get_contents('php/key.txt');    //для вывода и работой с файлом записываем его значение в переменную
                    }   //закрываем условие правильного логина
                } 
            } else {
                echo $card;    //выводим карточку с ошибкой
                exit(); //выходим с кода
            } 
    }   //закрываем условие существование логина в _POST[]
    if($strOfTables != ""){     //если файл НЕ пустой (есть список таблиц)
        $mysql = new mysqli('localhost', 'root', '', 'uchet_pdd');  //подключаемся к БД с root'a
        $result1 = $mysql->query("SELECT * FROM `users` WHERE `userLogin` = '$userLogin' AND `userPassword` = '$userPassword'");    //делаем запрос на наших пользователей с логином и паролем
        $user1 = $result1->fetch_assoc(); // Конвертируем в массив
        if(!empty($user1)){ //если массив НЕ пустой (то есть он вернул какое-то значение)
            $mysql->close();    //закрываем соединение 
            include('index.php');   //подгружаем index.php
        }   //закрываем ветку с НЕ пустым массивом
        else if(count($user) == 0){ //теперь если массив пустой (запрос ничего не вернул)
            echo $card;    //выводим карточку с ошибкой
            exit(); //выходим с кода
        }   //закрываем ветку с пустым массивом 
    else {       //закрываем ветку с НЕ пустым файлом
        echo $card;    //выводим карточку с ошибкой
        exit();
    }
}
    ?>
</body>
</html>