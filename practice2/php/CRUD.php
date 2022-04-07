<?php
    ob_start();
    require_once 'connect.php'; //Подключаемся к БД

    if($_GET['flagCRUD']=='C'){ # CREATE
        $queryINSERTpartINSERTINTO = "INSERT INTO ".$_GET['nameOfTable']." (";  //первая часть запроса
        $queryINSERTpartVALUES = " VALUES (";   //вторая часть запроса
        $types = mysqli_query($connect, "DESCRIBE `".$_GET['nameOfTable']."`"); //получаем типы атрибутов
        $type = mysqli_fetch_assoc($types); //преобразовываем в массив данных
        while($type = mysqli_fetch_assoc($types)){  //проходимся по нему
            $queryINSERTpartINSERTINTO.=$type['Field'].', ';    //и к первой части пишем через запятую все атрибуты
        }
        $queryINSERTpartINSERTINTO = substr($queryINSERTpartINSERTINTO, 0, strlen($queryINSERTpartINSERTINTO)-2);   //удаляем последнюю запятую
        $queryINSERT.=$queryINSERTpartINSERTINTO.')'.$queryINSERTpartVALUES;    //собираем итоговый запрос

        $NameOfComments = mysqli_query($connect, "SELECT COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'uchet_pdd' AND TABLE_NAME = '".$_GET['nameOfTable']."'"); //У каждой таблицы в СУБД есть вспомогательные аргументы в качестве комментариев. Они-то и будут заголовками таблиц
        mysqli_fetch_assoc($NameOfComments);    //делаем массив

        $types = mysqli_query($connect, "DESCRIBE `".$_GET['nameOfTable']."`"); //получаем типы атрибутов
        $type = mysqli_fetch_assoc($types); //прогоняем первый (ID)

        while($type = mysqli_fetch_assoc($types)){  //пока не закончатся типы
            $nameOftable = mysqli_fetch_assoc($NameOfComments); //берём комментарий из БД
            if(($type['Type']=="date")||(strpos($type['Type'], "varchar")!==false)||(strpos($type['Type'], "char")!==false)){   //если у нас date, varchar, char
                $queryINSERT.="'".$_POST[$nameOftable['COLUMN_COMMENT']]."', "; //то мы это делаем через кавычки
            } else{ //все другие варианты
                $queryINSERT.="".$_POST[$nameOftable['COLUMN_COMMENT']].", ";   //без кавычек
            }
        }
        $queryINSERT = substr($queryINSERT, 0, strlen($queryINSERT)-2);   //удаляем последнюю запятую
        $queryINSERT.=")";  //закрываем скобочку
        
        mysqli_query($connect, $queryINSERT);
        header("Location: /index.php?nameOfTable=".$_GET['nameOfTable']."");
    } else if($_GET['flagCRUD']=='U'){ # UPDATE
        
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Практика</title>
            <link href="/css.css" rel="stylesheet" type="text/css"/>
        </head>
        <body>
            <div class="wrapperMain">
                <div class="container">
                    <div class="content">
                        <form action="CRUD.php?id=<?= $_GET['id']?>&nameOfTable=<?= $_GET['nameOfTable']?>&flagCRUD=U&update=on" method="post">
                        <table class="table">
                            <tr>
                                <?php 
                                $NameOfComments = mysqli_query($connect, "SELECT COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'uchet_pdd' AND TABLE_NAME = '".$_GET['nameOfTable']."'"); //У каждой таблицы в СУБД есть вспомогательные аргументы в качестве комментариев. Они-то и будут заголовками таблиц
                                $lengthOfTable = 0; //изначально длина таблицы (количество строк) равно нулю
                                $types = mysqli_query($connect, "DESCRIBE `".$_GET['nameOfTable']."`"); //делаем запрос на информацию о столбцах
                                $foreigns = mysqli_query($connect, "SELECT `column_name`, `referenced_table_schema` AS foreign_db, `referenced_table_name` AS foreign_table, `referenced_column_name` AS foreign_column FROM `information_schema`.`KEY_COLUMN_USAGE` WHERE `constraint_schema` = 'uchet_pdd' AND `table_name` = '".$_GET['nameOfTable']."' AND `referenced_column_name` IS NOT NULL");  //получение внешних ключей
                                $tableQueryPart1 = "SELECT ";   //начало запроса
                                $tableQueryPart2 = " FROM ".$_GET['nameOfTable']." ".substr($_GET['nameOfTable'], 0, 3);    //начинка запроса
                                $tableQueryPart3 = " ON ";  //связка таблиц

                                $type = mysqli_fetch_assoc($types);
                                $name_table = mysqli_fetch_assoc($NameOfComments);
                                while(($name_table = mysqli_fetch_assoc($NameOfComments))){   //цикл пока не закончатся комментарии (столбики)
                                        $type = mysqli_fetch_assoc($types); //берем тип таблицы 
                                        if($type['Key'] == 'MUL'){  //если в этом типе есть хоть один внешний ключ
                                            $foreign = mysqli_fetch_array($foreigns);   //мы берем таблицу внешнего ключа
                                            $tableQueryPart2 = $tableQueryPart2." INNER JOIN ".$foreign[2]." ".substr($foreign[2], 0, 3);   //и пихаем в связку, нарезая на название и три символа после как псевдоним, потому что в названиях некоторых таблиц 2 первых символа повторяются
                                            $selects = mysqli_query($connect, "DESCRIBE `".$foreign[2]."`");    //берем информацию о столбиках
                                            $selected = mysqli_fetch_assoc($selects);   //раз выкидываем первый элемент
                                            $selected = mysqli_fetch_assoc($selects);   //два выкидываем первый элемент (то есть второй). Это нужно для удобной выборки
                                            $tableQueryPart1 = $tableQueryPart1.substr($foreign[2], 0, 3).".".$selected['Field'].", ";  //делаем наш запрос. Начало
                                            $tableQueryPart3 = $tableQueryPart3.substr($_GET['nameOfTable'], 0, 3).".".$foreign[0]." = ".substr($foreign[2], 0, 3).".".$foreign[0]." AND "; //заканчиваем запрос
                                        }else{  //если эта таблица простая (одна таблица)
                                            $tableQueryPart1 = $tableQueryPart1.substr($_GET['nameOfTable'], 0, 3).".".$type['Field'].", "; //мы все равно делаем это через псевдонимы для универсальности кода
                                        }?>
                                    <th><?=$name_table['COLUMN_COMMENT']?>  <!-- заголовок --></th>
                                <?php $lengthOfTable++;}    //увеличиваем длину таблицы на 1
                                if($tableQueryPart3 != " ON "){ //если у нас строка без ON
                                    $table = mysqli_query($connect, substr($tableQueryPart1, 0, strlen($tableQueryPart1)-2).$tableQueryPart2.substr($tableQueryPart3, 0, strlen($tableQueryPart3)-5).";");  //то собираем обычный запрос
                                }else{
                                    $table = mysqli_query($connect, substr($tableQueryPart1, 0, strlen($tableQueryPart1)-2).$tableQueryPart2.";");  //иначе со связкой
                                }
                                ?>
                            </tr>
                            <tr>
                                <?php

                                    $queryUPDATEpartUPDATE = "UPDATE `".$_GET['nameOfTable']."` SET ";
                                    $queryUPDATE.=$queryUPDATEpartUPDATE;
                                    $NameOfComments = mysqli_query($connect, "SELECT COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'uchet_pdd' AND TABLE_NAME = '".$_GET['nameOfTable']."'"); //У каждой таблицы в СУБД есть вспомогательные аргументы в качестве комментариев. Они-то и будут заголовками таблиц
                                    mysqli_fetch_assoc($NameOfComments);    //делаем массив

                                    $types = mysqli_query($connect, "DESCRIBE `".$_GET['nameOfTable']."`"); //получаем типы атрибутов
                                    $type = mysqli_fetch_assoc($types); //прогоняем первый (ID)

                                    
                                    while($type = mysqli_fetch_assoc($types)){  //пока не закончатся типы
                                        $nameOftable = mysqli_fetch_assoc($NameOfComments); //берём комментарий из БД
                                        if(($type['Type']=="date")OR((strpos($type['Type'], "varchar"))!==false)OR((strpos($type['Type'], "char"))!==false)){   //если у нас date, varchar, char
                                            $queryUPDATE.="`".$type['Field']."` = '".$_POST[$nameOftable['COLUMN_COMMENT']]."', "; //то мы это делаем через кавычки
                                        } else{ //все другие варианты
                                            $queryUPDATE.="`".$type['Field']."` = ".$_POST[$nameOftable['COLUMN_COMMENT']].", ";   //без кавычек
                                        }
                                    }

                                    $queryUPDATE = substr($queryUPDATE, 0, strlen($queryUPDATE)-2);
                                    $queryUPDATEpartWHERE = " WHERE `".$_GET['nameOfTable']."`.`ID_".$_GET['nameOfTable']."` = ".$_GET['id']."";
                                    $queryUPDATE.=$queryUPDATEpartWHERE;

                                    $queryData = mysqli_query($connect, "SELECT * FROM ".$_GET['nameOfTable']." WHERE ID_".$_GET['nameOfTable']." = ".$_GET['id']."");  //запрос для заполнения полей редактирования
                                    $queryDataEcho = mysqli_fetch_array($queryData);

                                    $NameOfComments = mysqli_query($connect, "SELECT COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'uchet_pdd' AND TABLE_NAME = '".$_GET['nameOfTable']."'"); //У каждой таблицы в СУБД есть вспомогательные аргументы в качестве комментариев. Они-то и будут заголовками таблиц
                                    mysqli_fetch_assoc($NameOfComments);

                                    $types = mysqli_query($connect, "DESCRIBE `".$_GET['nameOfTable']."`"); //делаем запрос на информацию о столбцах
                                    mysqli_fetch_assoc($types);

                                    $foreigns = mysqli_query($connect, "SELECT `referenced_table_name` AS foreign_table FROM `information_schema`.`KEY_COLUMN_USAGE` WHERE `constraint_schema` = 'uchet_pdd' AND `table_name` = '".$_GET['nameOfTable']."' AND `referenced_column_name` IS NOT NULL");  //получение внешних ключей
                                    for($i = 1; $i < count($queryDataEcho)/2; $i++){ 
                                        $nameOftable = mysqli_fetch_assoc($NameOfComments);
                                        $type = mysqli_fetch_assoc($types);
                                        if((strpos($type['Type'], "varchar")!==false)||(strpos($type['Type'], "char")!==false)){
                                            if($nameOftable['COLUMN_COMMENT'] == "Пол"){ ?>
                                                <td>
                                                    <select required name="<?=$nameOftable['COLUMN_COMMENT']?>">
                                                        <option value="М">М</option>
                                                        <option value="Ж">Ж</option>
                                                    </select>
                                                </td>
                                    <?php } else { ?>
                                        <td><textarea required rows="2" cols="1" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>"><?=$queryDataEcho[$i];?></textarea></td>
                                    <?php }
                                    } else if($type['Type']=="date"){?>
                                        <td><input required type="date" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>" value="<?=$queryDataEcho[$i];?>"></td>
                                    <?php } else if((strpos($type['Type'], "int")!==false)&&($type['Key'] !== 'MUL')){
                                        if($nameOftable['COLUMN_COMMENT'] == "Серия_Паспорта"){ ?>
                                            <td><input required type="number" minlength="4" maxlength="4" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>" value="<?=$queryDataEcho[$i];?>"></td>
                                        <?php } else if($nameOftable['COLUMN_COMMENT'] == "Номер_Паспорта"){ ?>
                                                <td><input required type="number" minlength="6" maxlength="6" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>" value="<?=$queryDataEcho[$i];?>"></td>
                                        <?php } else if($nameOftable['COLUMN_COMMENT'] == "Возраст"){ ?>
                                                <td><input required type="number" min="18" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>" value="<?=$queryDataEcho[$i];?>"></td>
                                        <?php } else { ?>
                                            <td><input required type="number" min="0" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>" value="<?=$queryDataEcho[$i];?>"></td>
                                    <?php }?>
                                    <?php } else if($type['Key'] == 'MUL'){
                                            $foreign = mysqli_fetch_array($foreigns);   //мы берем таблицу внешнего ключа 
                                            $foreigns_table = mysqli_query($connect, "SELECT * FROM `".$foreign[0]."`");?>
                                            <td>
                                                <select required onchange="alert(value)" name="<?=$nameOftable['COLUMN_COMMENT']?>">
                                                    <?php
                                                        while($foreign_table = mysqli_fetch_array($foreigns_table)){ ?>
                                                            <option value=<?=$foreign_table[0]?>><?=$foreign_table[1]?></option>
                                                        <?php }
                                                    ?>
                                                </select>
                                            <td>
                                        <?php }
                                    } ?>
                            </tr>
                        </table>
                        <input type="submit" value="Изменить!" >
                        </form>
                        <a href="/index.php?nameOfTable=<?=$_GET['nameOfTable']?>" class="goBack">Назад</a>
                        <?php if($_GET['update']=='on'){
                                mysqli_query($connect, $queryUPDATE);
                                header("Location: /index.php?nameOfTable=".$_GET['nameOfTable']."");
                            }
                        ?>
                    </div>
                        
                    <div class="department menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "department") != false )){   //если в файле у нас есть таблица department, то ссылка будет открыта?>
                            <a href="/index.php?nameOfTable=department">Отделения</a>
                        <?php }else{    //иначе ссылка будет заблокирована?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="reasons menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "reasons") != false )){  //аналогично со всеми остальными ссылками?>
                            <a href="/index.php?nameOfTable=reasons">Причины</a>
                        <?php }else{ ?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="report menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "report") != false )){?>
                            <a href="/index.php?nameOfTable=report">Нарушения</a>
                        <?php }else{ ?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="vialator menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "vialator") != false )){?>
                            <a href="/index.php?nameOfTable=vialator">Нарушитель</a>
                        <?php }else{ ?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="worker menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "worker") != false )){?>
                            <a href="/index.php?nameOfTable=worker">Сотрудник ГИБДД</a>
                        <?php }else{ ?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="request menuBlock">
                        <a href="/request.php">Запросы</a>   <!-- Ссылка на запросы -->
                    </div>
                    <div class="exit menuBlock">
                        <a href="/login.php">Выход</a>  <!-- ссылка на выход -->
                    </div>
                </div>
            </div>
        </body>
        </html>
        
    <?php } else if($_GET['flagCRUD']=='D'){ # DELETE
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Практика</title>
            <link href="/css.css" rel="stylesheet" type="text/css"/>
        </head>
        <body>
            <div class="wrapperMain">
                <div class="container">
                    <div class="content">
                        <form action="CRUD.php?id=<?= $_GET['id']?>&nameOfTable=<?= $_GET['nameOfTable']?>&flagCRUD=D" method="post">
                            </br></br>
                            <input required type="number" name="answer" max="1" min="1" placeholder="Вы уверены? 1 - да"><br/>
                            <input type="submit" value="Подтвердить">
                            <?php
                                $queryDELETE = "DELETE FROM `".$_GET['nameOfTable']."` WHERE `".$_GET['nameOfTable']."`.`ID_".$_GET['nameOfTable']."` = ".$_GET['id']."";
                                
                                if($_POST['answer']==1){
                                    mysqli_query($connect, $queryDELETE);
                                    header("Location: /index.php?nameOfTable=".$_GET['nameOfTable']."");
                                }
                            ?>
                        </form>
                        <a href="/index.php?nameOfTable=<?=$_GET['nameOfTable']?>" class="goBack">Назад</a>
                    </div>
                        
                    <div class="department menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "department") != false )){   //если в файле у нас есть таблица department, то ссылка будет открыта?>
                            <a href="/index.php?nameOfTable=department">Отделения</a>
                        <?php }else{    //иначе ссылка будет заблокирована?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="reasons menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "reasons") != false )){  //аналогично со всеми остальными ссылками?>
                            <a href="/index.php?nameOfTable=reasons">Причины</a>
                        <?php }else{ ?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="report menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "report") != false )){?>
                            <a href="/index.php?nameOfTable=report">Нарушения</a>
                        <?php }else{ ?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="vialator menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "vialator") != false )){?>
                            <a href="/index.php?nameOfTable=vialator">Нарушитель</a>
                        <?php }else{ ?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="worker menuBlock">
                        <?php if((strpos(file_get_contents('key.txt'), "worker") != false )){?>
                            <a href="/index.php?nameOfTable=worker">Сотрудник ГИБДД</a>
                        <?php }else{ ?>
                            <a href="#" style="color:red;">Заблокировано</a>
                            <?php } ?>
                    </div>
                    <div class="request menuBlock">
                        <a href="/request.php">Запросы</a>   <!-- Ссылка на запросы -->
                    </div>
                    <div class="exit menuBlock">
                        <a href="/login.php">Выход</a>  <!-- ссылка на выход -->
                    </div>
                </div>
            </div>
        </body>
        </html>  
    <?php }

    if($_GET['flagCRUD']=='UUser'){
        
    }

    if($_GET['flagCRUD']=='DUser'){
        $DROPUserQuery = "DROP USER '".$_GET['userLogin']."'";
        mysqli_query($connect, $DROPUserQuery);
        $DROPUserQuery = "DELETE FROM `users` WHERE userLogin = '".$_GET['userLogin']."'";
        mysqli_query($connect, $DROPUserQuery);
        header("Location: /request.php?admin");
    }
    // допилить права на C-Create, U-Update, D-Delete
?>