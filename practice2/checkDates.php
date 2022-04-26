<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <form action="форма2.php" method="get" id="dateForm">
        <input type="date" name="твояСтараяДата" id="dateInputOld" onchange="setMin()" required>
        <input type="date" name="твояНоваяДата" id="dateInputTeen" required>
        <button onclick="checkDate()">checkDate</button>
    </form>
    

    <script>
        let dateInputOld = document.getElementById("dateInputOld");
        let dateInputTeen = document.getElementById("dateInputTeen");
        
        function checkOld(){
            let dateTeen = new Date(dateInputTeen.value).getTime();
            let dateOld = new Date(dateInputOld.value).getTime();
            if(dateTeen < dateOld || isNaN(dateTeen) || isNaN(dateOld)){
                return false;
            }
            return true;
        }
        function setMin(){
            dateInputTeen.setAttribute("min", dateInputOld.value);
        }
        function checkDate(){
            if(checkOld()){
                alert("INSERT");
                document.getElementById("dateForm").submit;
            } else {
                alert("Enter the correct value!");
            }
        }
        window.onload = function(){
            // dateInputOld.value = '2020-12-11';
        }
    </script>
</body>
</html>