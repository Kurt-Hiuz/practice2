<?php 
    require_once 'connect.php'; //подгружаем файл с подключением
    $NameOfComments = mysqli_query($connect, "SELECT COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'uchet_pdd' AND TABLE_NAME = '".$_GET['nameOfTable']."'"); //У каждой таблицы в СУБД есть вспомогательные аргументы в качестве комментариев. Они-то и будут заголовками таблиц
    $lengthOfTable = 0; //изначально длина таблицы (количество строк) равно нулю
    $types = mysqli_query($connect, "DESCRIBE `".$_GET['nameOfTable']."`"); //делаем запрос на информацию о столбцах
    $foreigns = mysqli_query($connect, "SELECT `column_name`, `referenced_table_schema` AS foreign_db, `referenced_table_name` AS foreign_table, `referenced_column_name` AS foreign_column FROM `information_schema`.`KEY_COLUMN_USAGE` WHERE `constraint_schema` = 'uchet_pdd' AND `table_name` = '".$_GET['nameOfTable']."' AND `referenced_column_name` IS NOT NULL");  //получение внешних ключей
    $tableQueryPart1 = "SELECT ";   //начало запроса
    $tableQueryPart2 = " FROM ".$_GET['nameOfTable']." ".substr($_GET['nameOfTable'], 0, 3);    //начинка запроса
    $tableQueryPart3 = " ON ";  //связка таблиц
    $tableQueryPart4 = " ORDER BY `ID_".$_GET['nameOfTable']."`";   //порядок по ID

    $mainTables = file_get_contents('php/key.txt');    //получаем весь файлик
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <link href="/css.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <table class="table">
        <tr>
            <?php while(($name_table = mysqli_fetch_assoc($NameOfComments))){   //цикл пока не закончатся комментарии (столбики)
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
                $table = mysqli_query($connect, substr($tableQueryPart1, 0, strlen($tableQueryPart1)-2).$tableQueryPart2.substr($tableQueryPart3, 0, strlen($tableQueryPart3)-5).$tableQueryPart4.";");  //то собираем обычный запрос
            }else{
                $table = mysqli_query($connect, substr($tableQueryPart1, 0, strlen($tableQueryPart1)-2).$tableQueryPart2.$tableQueryPart4.";");  //иначе со связкой
            }
            ?>
        </tr>
        <?php
            $table = mysqli_fetch_all($table);  //делаем массив из полученных данных
            foreach($table as $table){  //читаем до его конца
        ?>
        <tr>
            <?php for($i = 0; $i < $lengthOfTable; $i++){   //выводим столько строк, сколько надо?>
            <td><?php echo $table[$i]   //выводим данные?></td> 
            <?php ;}
            if((strpos($mainTables, "UPDATE") != false)){ ?>
                <td><a href="php/CRUD.php?id=<?= $table[0]?>&flagCRUD=U&nameOfTable=<?= $_GET['nameOfTable']?>" class="updateDelete">Изменить</a></td>
            <?php } 
            if((strpos($mainTables, "DELETE") != false)){ ?>
                <td><a href="php/CRUD.php?id=<?= $table[0]?>&flagCRUD=D&nameOfTable=<?= $_GET['nameOfTable']?>" class="updateDelete delete">Удалить</a></td>
            <?php } ?>
        </tr>
        <?php
            }
        ?>
    </table>
    <?php if((strpos($mainTables, "INSERT") != false)){ ?>
        <div class="createCRUD">  <!--Форма добавления записи-->
            <form action="php/CRUD.php?nameOfTable=<?= $_GET['nameOfTable']?>&flagCRUD=C" method="post">
                <!-- <input type="text" name="POSTnameOfTable" id="" style="display:none;"> -->
                <?php $NameOfComments = mysqli_query($connect, "SELECT COLUMN_COMMENT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'uchet_pdd' AND TABLE_NAME = '".$_GET['nameOfTable']."'"); //У каждой таблицы в СУБД есть вспомогательные аргументы в качестве комментариев. Они-то и будут заголовками таблиц
                mysqli_fetch_assoc($NameOfComments);
                $types = mysqli_query($connect, "DESCRIBE `".$_GET['nameOfTable']."`"); //делаем запрос на информацию о столбцах
                $type = mysqli_fetch_assoc($types);

                $foreigns = mysqli_query($connect, "SELECT `referenced_table_name` AS foreign_table FROM `information_schema`.`KEY_COLUMN_USAGE` WHERE `constraint_schema` = 'uchet_pdd' AND `table_name` = '".$_GET['nameOfTable']."' AND `referenced_column_name` IS NOT NULL");  //получение внешних ключей
                while(($nameOftable = mysqli_fetch_assoc($NameOfComments))){

                    $type = mysqli_fetch_assoc($types);
                    if((strpos($type['Type'], "varchar")!==false)||(strpos($type['Type'], "char")!==false)){
                        if($nameOftable['COLUMN_COMMENT'] == "Пол"){ ?>
                                    <select required name="<?=$nameOftable['COLUMN_COMMENT']?>">
                                        <option value="М">М</option>
                                        <option value="Ж">Ж</option>
                                    </select>
                        <?php } else { ?>
                                    <p><input required type="text" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>"></p>
                        <?php }  
                    } else if($type['Type']=="date"){?>
                        <p><input required type="date" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>"></p>
                    <?php } else if((strpos($type['Type'], "int")!==false)&&($type['Key'] !== 'MUL')){
                            if($nameOftable['COLUMN_COMMENT'] == "Серия_Паспорта"){ ?>
                                <p><input required type="number" minlength="4" maxlength="4" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>"></p>
                        <?php }
                            else if($nameOftable['COLUMN_COMMENT'] == "Номер_Паспорта"){ ?>
                                <p><input required type="number" minlength="6" maxlength="6" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>"></p>
                    <?php } else if($nameOftable['COLUMN_COMMENT'] == "Возраст"){ ?>
                                <p><input required type="number" min="18" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>"></p>
                    <?php } else { ?>
                            <p><input required type="number" min="0" name="<?=$nameOftable['COLUMN_COMMENT']?>" placeholder="<?=$nameOftable['COLUMN_COMMENT']?>"></p>
                    <?php }
                        } else if($type['Key'] == 'MUL'){
                        $foreign = mysqli_fetch_array($foreigns);   //мы берем таблицу внешнего ключа 
                        $foreigns_table = mysqli_query($connect, "SELECT * FROM `".$foreign[0]."`");?>
                        <select required onchange="alert(value)" name="<?=$nameOftable['COLUMN_COMMENT']?>" >
                            <?php
                                while($foreign_table = mysqli_fetch_array($foreigns_table)){ ?>
                                    <option value=<?=$foreign_table[0]?>><?=$foreign_table[1]?></option>
                                <?php }
                            ?>
                        </select><br/>
                    <?php }
                } ?>
                <input type="submit" value="Добавить" id="CRUDsubmit">
            </form>
        </div>
    <?php }?>
</body>
</html>