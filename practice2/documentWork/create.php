<?php  //открытие php
    require_once '../connectdb.php';  //подключение файла с БД
    $issuance_id = $_GET['id'];  //при выборе строки с выдачей, мы через GET получали её ID. Здесь происходит созранение её ID
    $issuance = mysqli_query($connect,"SELECT * FROM `issuance` WHERE `id` = '$issuance_id'");  //выкачка всей информации по данному ID
    $issuance = mysqli_fetch_assoc($issuance);  //преобразование в массив


    $BookID = $issuance['ID_Book'];  //присваивание переменной ID книги
    $BookName = mysqli_query($connect, "SELECT `Name` FROM `book` WHERE `ID` = '$BookID'");  //получение даты издания по названию книги
    while($Book_Name = $BookName->fetch_assoc()){  //чтение полученного массива из одного элемента
        $NameOfBook = $Book_Name['Name'];  //буквенное представление названия из массива
    }

    $Realese_Year = mysqli_query($connect, "SELECT `Realese_Year` FROM `book` WHERE `ID` = '$BookID'");  //получение даты издания по названию книги
    while($RealeseYear = $Realese_Year->fetch_assoc()){  //чтение полученного массива из одного элемента
        $Book_Realese_Year = $RealeseYear['Realese_Year'];  //буквенное представление навания из массива
    }

    $ID_Publishing = mysqli_query($connect, "SELECT `ID_Publishing` FROM `book` WHERE `ID` = '$BookID'");  //получение ID издательства
    while($IDPublishing = $ID_Publishing->fetch_assoc()){  //чтение полученного массива из одного элемента
        $Publishing_ID = $IDPublishing['ID_Publishing'];  //Получение цифры из массива
    }
    $ID_Publishing_Name = mysqli_query($connect, "SELECT `publishingName` FROM `publishing` WHERE `ID` = '$Publishing_ID'");  //получение названия издательства по его ID
    while($PublishingName = $ID_Publishing_Name->fetch_assoc()){  //чтение полученного массива из одного элемента
        $Publishing_Name = $PublishingName['publishingName'];  //Буквенное название из массива
    }

    //=======================================================

    $WorkerID = $issuance['ID_Worker'];  //присваивание переменной ID работника
    $WorkerName = mysqli_query($connect, "SELECT `Name` FROM `worker` WHERE `ID` = '$WorkerID'");  //Получение имени работника по его фамилии
    while($Worker_Name = $WorkerName->fetch_assoc()){  //чтение полученного массива из одного элемента
        $NameOfWorker = $Worker_Name['Name'];  //Буквенное название из массива
    }
    $WorkerSurname = mysqli_query($connect, "SELECT `Surname` FROM `worker` WHERE `ID` = '$WorkerID'");  //Получение имени работника по его фамилии
    while($Worker_Surname = $WorkerSurname->fetch_assoc()){  //чтение полученного массива из одного элемента
        $SurnameOfWorker = $Worker_Surname['Surname'];  //Буквенное название из массива
    }
    $WorkerPatronymic = mysqli_query($connect, "SELECT `Patronymic` FROM `worker` WHERE `ID` = '$WorkerID'");  //Получение отчества работника по его фамилии
    while($Worker_Patronymic = $WorkerPatronymic->fetch_assoc()){  //чтение полученного массива из одного элемента
        $PatronymicOfWorker = $Worker_Patronymic['Patronymic'];  //Буквенное название из массива
    }

    //=======================================================

    $ReaderID = $issuance['ID_Reader'];  //присваивание переменной ID читателя
    $ReaderName = mysqli_query($connect, "SELECT `Name` FROM `reader` WHERE `ID` = '$ReaderID'");  //Получение имени читателя по его фамилии
    while($Reader_Name = $ReaderName->fetch_assoc()){  //чтение полученного массива из одного элемента
        $NameOfReader = $Reader_Name['Name'];  //Буквенное название из массива
    }
    $ReaderSurname = mysqli_query($connect, "SELECT `Surname` FROM `reader` WHERE `ID` = '$ReaderID'");  //Получение имени читателя по его фамилии
    while($Reader_Surname = $ReaderSurname->fetch_assoc()){  //чтение полученного массива из одного элемента
        $SurnameOfReader = $Reader_Surname['Surname'];  //Буквенное название из массива
    }
    $ReaderPatronymic = mysqli_query($connect, "SELECT `Patronymic` FROM `reader` WHERE `ID` = '$ReaderID'");  //Получение отчества читателя по его фамилии
    while($Reader_Patronymic = $ReaderPatronymic->fetch_assoc()){  //чтение полученного массива из одного элемента
        $PatronymicOfReader = $Reader_Patronymic['Patronymic'];  //Буквенное название из массива
    }
?>
<!DOCTYPE html>  <!--начало html файла -->
<html lang="en">  <!--начало чтения html файла -->
<head>  <!--голова сайта -->
    <meta charset="UTF-8">  <!--кодировка -->
    <title>БД Библиотеки</title>  <!--заголовок вкладки -->
    <link rel="stylesheet" type="text/css" href="/styles/css.css">  <!--подключение стилей -->
    <style>  /*стили, которые отказывались работать по непонятным причинам*/
        #dateBill{  /*обращение к параграфу с сегодняшней датой*/
            text-align:center;  /*центрирование*/
            margin-top: 30px;  /*внешний отступ сверху*/
        }
        .ReaderBlock span, .WorkerBlock span, .BookBlock span{  /*обращение к параграфам этих блоков*/
            margin-left: 50px;  /*отступ слева*/
        }
        .downloadBtn{  /*обращение к кнопке загрузки*/
            margin-left:100px;  /*внешний отступ слева*/
        }
        #exportContent{  /*обращение к */
            width: 50%;  /*ширина*/
        }
    </style>  <!--закрытие стилей -->
</head>  <!--закрытие головы -->
<body>  <!--начало тела документа -->
    <div class="wrapper">  <!--блок-обёртка сайта -->
        <div class="logo">  <!--блок-лого -->
            <a class="linkBlock" href="/index.php">Библиотека</a>  <!--ссылка на главную страницу -->
        </div>  <!--конец блока-лого -->
        <div class="mainMenu">  <!--блок с меню -->
            <div class="wrapMenu">  <!--блок-обертка для пунктов меню -->
                <div class="allTable menuBlock">  <!--блок с ссылкой на все таблицы -->
                    <a class="linkBlock" href="/index.php?alltable">Все таблицы</a>  <!--ссылка на документ с таблицами -->
                </div>  <!--конец блока с ссылкой на все таблицы -->
                <div class="workDoc menuBlock">  <!--аналогично, как у блока с "Все таблицы" -->
                    <a class="linkBlock" href="/index.php?workdoc">Работа с документом</a>
                </div>
                <div class="exit menuBlock">  <!--аналогично, как у блока с "Все таблицы" -->
                    <a class="linkBlock" href="https://www.yandex.ru" onclick="Alert.render('До свидания')">Выход</a>
                </div>
            </div>  <!--конец блока-обертки -->
        </div>  <!--конец блока с меню -->
        <div class="content">  <!--блок контент -->
            <?php  //открытие php
                if(isset($_GET['alltable'])){  //проверка на нажатие ссылки
                    include('../content.php');  //подгрузка новой страницы
                }  //конец проверки
            ?>  <!--закрытие php -->
            <?php  //аналогично предыдущему тегу php
                if(isset($_GET['workdoc'])){  
                    include('../document.php');
                }
            ?>
            <div id="exportContent">  <!--экспортируемый блок с контентом -->
                <h1 style = "color: rgb(80, 59, 36); font-size: 64px; display: block; text-align:center;" id="numberBill">№</h1>  <!--заголовок и стили к нему -->
                <hr/>  <!--декоративная полоска -->
                <div class="ReaderBlock">  <!--блок с читателем -->
                    <span><?=$ReaderID?></span>  <!--блок с ID читателя -->
                    <span><?=$NameOfReader?></span>  <!--блок с именем читателя -->
                    <span><?=$SurnameOfReader?></span>  <!--блок с фамилией читателя -->
                    <span><?=$PatronymicOfReader?></span>  <!--блок с отчеством читателя -->
                    <span>____________</span>  <!--блок с подписью читателя -->
                </div>  <!--закрытие блока с читателем -->
                <p></p>  <!--абзац -->
                <div class="WorkerBlock">  <!--начало аналогии с читателем -->
                    <span><?=$WorkerID?></span>
                    <span><?=$NameOfWorker?></span>
                    <span><?=$SurnameOfWorker?></span>
                    <span><?=$PatronymicOfWorker?></span>
                    <span>____________</span>
                </div>
                <p></p>
                <div class="BookBlock">
                    <span><?=$BookID?></span>
                    <span><?=$NameOfBook?></span>
                    <span><?=$Book_Realese_Year?> г.</span>
                    <span><?=$Publishing_Name?></span>
                    <span>____________</span>
                </div>
                <hr/>  <!--конец аналогии -->
                <p id="dateBill">Date</p>  <!--параграф с датой-->
            </div>  <!--конец экспортируемого контента -->
            <button onclick = "Export2Doc('exportContent', 'Чек')" class="downloadBtn">Скачать документом</button>  <!--кнопка загрузки -->
        </div>  <!--конец блока контента -->
    </div>  <!--конец блока обертки -->

    <div id="dialogoverlay"></div>  <!--блок с фоном alert'a выхода -->
    <div id="dialogbox">  <!--сам alert -->
        <div>  <!--обертка -->
            <div id="dialogboxhead"></div>  <!--голова alert'a -->
            <div id="dialogboxbody"></div>  <!--тело alert'a -->
        </div>  <!--конец обёртки -->
    </div>  <!--конец alert'a -->
    <script src="scripts/js.js"></script>  <!--подключение скриптов для кнопки выхода -->
        <script>  //скрипт для кнопки загрузки. Данный скрипт находится в свободном доступе в интернете и использовался как утилита, помогающая без ошибок сохранять файлы
            function Export2Doc(element, fileName = ''){
                var preHtml = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'><head><meta charset='utf-8'><title>Export HTML to Doc</title></head><body>";
                var postHtml = "</body></html>";
                var html = preHtml+document.getElementById(element).innerHTML+postHtml;
        
                var blob = new Blob(['\ufeff', html],{
                type: 'application/msword'
            });
        
            var url = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(html);
        
            fileName = fileName?fileName+'.doc': 'document.doc';
        
            var downloadLink = document.createElement("a");
        
            document.getElementsByTagName("body")[0].appendChild(downloadLink);
        
            if (navigator.msSaveOrOpenBlob){
                navigator.msSaveOrOpenBlob(blob, fileName);
            }
            else{
                downloadLink.href = url;
                downloadLink.download = fileName;
                downloadLink.click();
            }
        
            document.getElementsByTagName("body")[0].removeChild(downloadLink);
            }

            var today = new Date();  //присваивание переменной сегодняшней даты
            var year = today.getFullYear();  //определение года
            var month = today.getMonth()+1;  //месяца
            var day = today.getDate();  //дня
            if(month<10){  //если месяц по счёту <10
                month = '0'+month;  //то приписываем ноль в начало
            }
            if(day<10){  //аналогично месяцу
                day = '0'+day;
            }
            var todayDate = day+'-'+month+'-'+year;  //составление всей даты
            var el = document.getElementById('dateBill');  //находим параграф, отвечающий за дату
            el.innerHTML = todayDate;  //вставляем в него нашу дату

            var numberBill = document.getElementById('numberBill');
            numberBill.innerHTML += ' '+(day+month+year)*2;
        </script>  <!--закрытие скриптов-->
</body>  <!--закрытие тела документа-->
</html>  <!--закрытие чтения html файла-->