<?php
    require_once 'php/connect.php'; //подключаемся к БД
    require_once 'auth.php';    //подключаемся к файлу с аутентификации
    $userLogin = filter_var(trim($_POST['userLogin']), FILTER_SANITIZE_STRING);  //записываем в переменную userLogin, убирая ненужные пробелы и символы через фильтр
    $userPassword = filter_var(trim($_POST['userPassword']), FILTER_SANITIZE_STRING);  //записываем в переменную userPassword, убирая ненужные пробелы и символы через фильтр
    //далее идут массивы с запросами. Первый - простые запросы. Любой сложности.
    //второй - процедуры с любым количеством параметров. Единственное условие - вместо переменных поставить "~"
    $arrayOfSelect = ['SELECT Article AS "Статья", Description AS "Описание", Penalty AS "Наказание" FROM `reasons`',
                      'SELECT FIO_Vialator AS "ФИО Нарушителя", Age_Vialator  AS "Возраст" FROM vialator WHERE Age_Vialator > 40;',
                      'SELECT DateRep AS "Дата", FIO_Vialator AS "ФИО Нарушителя" FROM report R INNER JOIN vialator V  ON R.ID_Vialator = V.ID_Vialator;',
                      'SELECT FIO_Worker AS "ФИО Сотрудника", Name AS "Отделение" FROM report Rt INNER JOIN worker Wr INNER JOIN department Dt ON Rt.ID_Worker = Wr.ID_Worker AND Wr.ID_Department = Dt.ID_Department GROUP BY Rt.ID_Worker;',
                      'SELECT FIO_Worker AS "ФИО Сотрудника", DateRep AS "Дата", Article AS "Статья", Penalty AS "Наказание" FROM Worker Wr INNER JOIN report Rt INNER JOIN violation Vt INNER JOIN reasons Rs ON Wr.ID_Worker = Rt.ID_Worker AND Rt.ID_Report = Vt.ID_Report AND Vt.ID_Reason = Rs.ID_Reasons;',
                      'SELECT FIO_Worker AS "ФИО Сотрудника", Name AS "Отделение" FROM worker Wr INNER JOIN department Dt ON Wr.ID_Department = Dt.ID_Department WHERE Gender_Worker = "ж";',
                      'SELECT Wr1.FIO_Worker AS "ФИО Сотрудника", (SELECT COUNT(Gender_Vialator) FROM vialator Vr INNER JOIN report Rt INNER JOIN worker Wr ON Vr.ID_Vialator = Rt.ID_Vialator AND Rt.ID_Worker = Wr.ID_Worker WHERE Vr.Gender_Vialator = "М" AND Rt.ID_Worker = Rt1.ID_Worker) AS "Пойманные мужчины", (SELECT COUNT(Gender_Vialator) FROM vialator Vr INNER JOIN report Rt INNER JOIN worker Wr ON Vr.ID_Vialator = Rt.ID_Vialator AND Rt.ID_Worker = Wr.ID_Worker WHERE Vr.Gender_Vialator = "Ж" AND Rt.ID_Worker = Rt1.ID_Worker) AS "Пойманные женщины" FROM worker Wr1 INNER JOIN report Rt1 ON Rt1.ID_Worker = Wr1.ID_Worker GROUP BY Wr1.FIO_Worker;'
                     ];
    $arrayOfSelect810 = ['SELECT NumberCar AS "Номер машины", Town AS "Город", DateRep AS "Дата", Article AS "Статья", Penalty AS "Наказание" FROM vialator Vr INNER JOIN report Rt INNER JOIN violation Vn INNER JOIN reasons Rs ON Vr.ID_Vialator = Rt.ID_Vialator AND Rt.ID_Report = Vn.ID_Report AND Vn.ID_Reason = Rs.ID_Reasons WHERE Vr.FIO_Vialator = ~;',
                         'SELECT FIO_Vialator AS "ФИО", Gender_Vialator AS "Пол", Age_Vialator AS "Возраст" FROM vialator Vr INNER JOIN report Rt ON Vr.ID_Vialator = Rt.ID_Vialator WHERE Rt.DateRep BETWEEN ~ AND ~;',
                         'SELECT FIO_Vialator AS "ФИО", Seriya AS "Серия паспорта", Number AS "Номер паспорта", Age_Vialator AS "Возраст" FROM vialator Vr WHERE Vr.Age_Vialator BETWEEN ~ AND ~;',
                        ];
    
    $mainTables = file_get_contents('php/key.txt');    //получаем весь файлик

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Запросы</title>
    <link href="/css.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <div class="wrapperMain">
        <div class="container">
            <div class="content">
                <?php if((strpos($mainTables, "admin") != false)){?>
                    <div class="admin">
                        <a href="?admin">Admin</a>  <!-- если нажали, то в $_GET передается флажок админки -->
                    </div>
                <?php } ?>
                <select class="select-css" onChange="SelectRequest(this.selectedIndex)">    <!-- скрипт внизу стр -->
                    <option>Выборка запросов</option>
                    <option>1. Выборка статей</option>
                    <option>2. Нарушители 40+</option>
                    <option>3. Дата и задержанный</option>
                    <option>4. Задействованные сотрудники</option>
                    <option>5. Сотрудники и выписанные статьи</option>
                    <option>6. Сотрудницы и отделения</option>
                    <option>7. Статистика задержанных по полу</option>
                    <option>8. Данные по ФИО нарушителя</option>
                    <option>9. Данные о нарушителях в указанной дате</option>
                    <option>10. Нарушители в возрастной категории</option>
                </select>
                
                <form action="request.php" style="display:none" id="selectForm" method="post">  <!-- создание пустой формы для передачи переменной js в переменную php -->
                    <textarea id="textarea" name="selectTextarea"></textarea>   <!-- поле, куда вводится значение номера выбранного option -->
                </form>

                <table class="table">
                    <tr>
                        <?php   
                        $queryStr = $arrayOfSelect[$_POST['selectTextarea']-1]; //выбираем нужный запрос из массива[номера выбранного option-1]
                        $lenghtOfQuery = 0;     //наш запрос - это таблица. Аналогично считаем кол-во его строк
                        while((strpos($queryStr, ',')) !== false){  //пока есть запятые начинаем цикл?>
                            <th><?= substr($queryStr, strpos($queryStr, 'AS')+4, strpos($queryStr, ',')-5-strpos($queryStr, 'AS')); //выводим псевдонимы в AS ""?></th>
                        <?php $lenghtOfQuery++; //количество строк увеличиваем
                        $queryStr = substr($queryStr, strpos($queryStr, ',')+1, strlen($queryStr)); //режем нашу строку с запросом
                        }   //закрываем цикл
                        $lenghtOfQuery++;   //и еще раз увеличиваем, т.к. последний столбик не был посчитан в таком случае?>
                        
                        <th><?= substr(substr($queryStr, strpos($queryStr, 'AS')+4, strlen($queryStr)), 0, strpos(substr($queryStr, strpos($queryStr, 'AS')+4, strlen($queryStr)), '"')); //выводим последний столбик с заголовком?></th>
                    </tr>
                    <?php
                        if(isset($_POST['selectTextarea'])){    //в POST есть выбранный option
                            if ($_POST['selectTextarea'] < 8){  //если выбраны запросы до 8-ого пункта
                                $query = mysqli_query($connect, $arrayOfSelect[$_POST['selectTextarea']-1]);    //делаем запрос в БД по элементу из списка запросов
                                $query = mysqli_fetch_all($query);  //пихаем данные в массив
                                foreach($query as $query){  //читаем пока не дочитаем?>
                                    <tr>
                                        <?php for($i = 0; $i < $lenghtOfQuery; $i++){   //выводим строки до их высчитанного конца?>
                                        <td><?php echo $query[$i]   //выводим данные?></td>
                                        <?php ;} //заканчиваем выводить?>
                                    </tr>
                                <?php } //дочитали данные
                            }else{  //ветка с процедурами (8-10 пункты)?>
                                <div class="value810">  <!-- только в таком случае появляется блок для ввода -->
                                    <form action="request.php" method="post">   <!-- форма для переадресации на этот же файл -->
                                        <br/><input type="text" name="selectValue810" id="selectValue810" class="valueText810">  <!-- Поле для ввода аргументов -->
                                        <br/><input type="submit" value="Найти" class="valueSubmit810">  <!-- кнопка отправки -->
                                        <br/><input type="text" name="voidNumber" id="voidNumber" style="display:none" value="<?=$_POST['selectTextarea']-1?>">  <!-- невидимое число с выбранным пунктом для 8-10 пунктов -->
                                    </form>
                                </div>
                            <?php }  //заканчиваем выводить}
                        } else if(isset($_POST['selectValue810'])){  //если в POST число пунктов в промежутке 8-10
                            $lenghtOfQuery = 0; //длина таблицы
                            $queryStr = $arrayOfSelect810[$_POST['voidNumber']-7];  //получаем наш запрос в качестве строки
                            while((strpos($queryStr, ',')) !== false){  //аналогично с предыдущим разом проходимся по запятым и выводим заголовки?>    
                                <th><?= substr($queryStr, strpos($queryStr, 'AS')+4, strpos($queryStr, ',')-5-strpos($queryStr, 'AS'));?></th>
                            <?php $lenghtOfQuery++;
                            $queryStr = substr($queryStr, strpos($queryStr, ',')+1, strlen($queryStr));
                            }
                            $lenghtOfQuery++;?>
                            <th><?= substr(substr($queryStr, strpos($queryStr, 'AS')+4, strlen($queryStr)), 0, strpos(substr($queryStr, strpos($queryStr, 'AS')+4, strlen($queryStr)), '"')); //последний столбик?></th>
                            <?php 
                                while((strpos($_POST['selectValue810'], '!')) !== false){   //пока в строке есть разделитель "!"
                                    $arrayOfSelect810[$_POST['voidNumber']-7] = substr($arrayOfSelect810[$_POST['voidNumber']-7], 0, strpos($arrayOfSelect810[$_POST['voidNumber']-7], '~')).'"'.substr($_POST['selectValue810'], 0, strpos($_POST['selectValue810'], '!')).'"'.substr($arrayOfSelect810[$_POST['voidNumber']-7], strpos($arrayOfSelect810[$_POST['voidNumber']-7], '~')+1, strlen($arrayOfSelect810[$_POST['voidNumber']-7])); //удаляем ненужное и собираем запрос, меняя ~ на значения
                                    $_POST['selectValue810'] = substr($_POST['selectValue810'], strpos($_POST['selectValue810'], '!')+1, strlen($_POST['selectValue810'])); //после делаем отступ и продолжаем собирать запрос
                                }
                                $arrayOfSelect810[$_POST['voidNumber']-7] = substr($arrayOfSelect810[$_POST['voidNumber']-7], 0, strpos($arrayOfSelect810[$_POST['voidNumber']-7], '~')).'"'.$_POST['selectValue810'].'"'.substr($arrayOfSelect810[$_POST['voidNumber']-7], strpos($arrayOfSelect810[$_POST['voidNumber']-7], '~')+1, strlen($arrayOfSelect810[$_POST['voidNumber']-7]));   //заканчиваем менять ~ на переменные

                            $query = mysqli_query($connect, $arrayOfSelect810[$_POST['voidNumber']-7]); //делаем запрос в БД
                            $query = mysqli_fetch_all($query);  //пихаем в массив
                            if(!empty($query)){ //если он не пустой
                                foreach($query as $query){  //пока не дочитаем?>
                                    <tr>
                                    <?php for($i = 0; $i < $lenghtOfQuery; $i++){?>
                                    <td><?php echo $query[$i]   //аналогично со всем выводим данные?></td>
                                    <?php ;}?>
                                    </tr>
                                    <?php }
                            } else {
                                echo '<p>По введённым данным нет результата.</p>';
                            }
                            
                        }

                        if(isset($_GET['admin'])){ ?>
                            <th>ID Пользователя</th>
                            <th>Логин</th>
                            <th>Пароль</th>
                        <?php 
                        
                        $usersQuery = "SELECT * FROM `users` WHERE NOT ((userLogin = 'admin1')AND(userPassword = 'admin1')) ORDER BY id";
                        $usersQuery = mysqli_query($connect, $usersQuery);
                        
                        while($userQuery = mysqli_fetch_array($usersQuery)){ ?>
                            <tr>
                                <td><?=$userQuery[0]?></td>
                                <td><?=$userQuery[1]?></td>
                                <td><?=$userQuery[2]?></td>

                                <td><a href="php/CRUD.php?id=<?=$userQuery[0]?>&flagCRUD=UUser&userLogin=<?=$userQuery[1]?>" class="updateDelete">Изменить</a></td>
                                <td><a href="php/CRUD.php?id=<?=$userQuery[0]?>&flagCRUD=DUser&userLogin=<?=$userQuery[1]?>" class="updateDelete delete">Удалить</a></td>
                            </tr>
                        <?php }
                        }
                    ?>
                </table>    
            </div>
            <!--тут все аналогично по всем блокам ссылок. У нас есть ссылка, если у пользователя, когда мы авторизовались, в секретный файл записывается доступная таблица, то она включена во множество
            Это множество и определяет, будет ли выводиться название таблицы или надпись "Заблокировано". Всё вручную прописано для каждой из ссылок-->
            <div class="department menuBlock">
                <?php if((strpos(file_get_contents('php/key.txt'), "department") != false )){?>
                    <a href="/index.php?nameOfTable=department">Отделения</a>
                <?php }else{ ?>
                    <a href="#" style="color:red;">Заблокировано</a>
                    <?php } ?>
            </div>
            <div class="reasons menuBlock">
                <?php if((strpos(file_get_contents('php/key.txt'), "reasons") != false )){?>
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
                <a href="#">Запросы</a>
            </div>
            <div class="exit menuBlock">
                <a href="/login.php">Выход</a>
            </div>
        </div>
    </div>
</body>
<script> 
    //функция переноса числа выбранного пункта списка в скрытую текстарию и её отправка, чтобы в php перенеслась переменная js
    function SelectRequest(number){
        document.getElementById('textarea').innerHTML = number;
        document.getElementById('selectForm').submit();
    }
</script>
</html>