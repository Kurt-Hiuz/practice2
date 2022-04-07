<?php
    require_once 'auth.php';   //подключаем файл с аутентификацией 
    $userLogin = filter_var(trim($_POST['userLogin']), FILTER_SANITIZE_STRING);  //записываем в переменную userLogin, убирая ненужные пробелы и символы через фильтр
    $userPassword = filter_var(trim($_POST['userPassword']), FILTER_SANITIZE_STRING);  //записываем в переменную userPassword, убирая ненужные пробелы и символы через фильтр
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <style>
        <?=file_get_contents('css.css')?>
    </style>
</head>
<body>
    <div class="wrapperMain">
        <div class="container">
            <div class="content">
                <?php  //открытие php
                    if(isset($_GET['nameOfTable'])){  //если мы нажали на карточку
                        include('php/tables.php');  //то подгружаем контент с содержанием таблицы
                    }//закрытие условия
                ?>  <!--закрытие php -->
            </div>
            

            
            <?php

                $file = file_get_contents('php/key.txt');
                $tables = '';
                while(!empty($file)){
                    $file = substr($file, 4, strlen($file));
                    $file = substr($file, 0, strpos($file, '%'));
                    echo $file;
                    $file = '';
                }

            ?>



            <div class="department menuBlock">
                <?php if((strpos(file_get_contents('php/key.txt'), "department") != false )){   //если в файле у нас есть таблица department, то ссылка будет открыта?>
                    <a href="/index.php?nameOfTable=department">Отделения</a>
                <?php }else{    //иначе ссылка будет заблокирована?>
                    <a href="#" style="color:red;">Заблокировано</a>
                    <?php } ?>
            </div>
            <div class="reasons menuBlock">
                <?php if((strpos(file_get_contents('php/key.txt'), "reasons") != false )){  //аналогично со всеми остальными ссылками?>
                    <a href="/index.php?nameOfTable=reasons">Причины</a>
                <?php }else{ ?>
                    <a href="#" style="color:red;">Заблокировано</a>
                    <?php } ?>
            </div>
            <div class="report menuBlock">
                <?php if((strpos(file_get_contents('php/key.txt'), "report") != false )){?>
                    <a href="/index.php?nameOfTable=report">Нарушения</a>
                <?php }else{ ?>
                    <a href="#" style="color:red;">Заблокировано</a>
                    <?php } ?>
            </div> 
            <div class="vialator menuBlock">
                <?php if((strpos(file_get_contents('php/key.txt'), "vialator") != false )){?>
                    <a href="/index.php?nameOfTable=vialator">Нарушитель</a>
                <?php }else{ ?>
                    <a href="#" style="color:red;">Заблокировано</a>
                    <?php } ?>
            </div>
            <div class="worker menuBlock">
                <?php if((strpos(file_get_contents('php/key.txt'), "worker") != false )){?>
                    <a href="/index.php?nameOfTable=worker">Сотрудник ГИБДД</a>
                <?php }else{ ?>
                    <a href="#" style="color:red;">Заблокировано</a>
                    <?php } ?>
            </div>
            <div class="request menuBlock">
                <a href="request.php">Запросы</a>   <!-- Ссылка на запросы -->
            </div> 
            <div class="exit menuBlock">
                <a href="/login.php">Выход</a>  <!-- ссылка на выход -->
            </div>
        </div>
    </div>
</body>
</html>