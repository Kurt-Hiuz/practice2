<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link href="css.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="/font-awesome-4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="wrapperLogin">
        <div class="loginCard">
            <div class="headerCard">Registration</div>
            <div class="loginSystem">
                <form action="checkReg.php" method="post">
                    <div class="userName">
                        <p> 
                            <i class="fa fa-user fa-3x" aria-hidden="false"></i>
                            <input type="text" name="userLogin" id="userLogin" required placeholder="Your login">
                        </p>
                    </div>
                    <div class="userPassword">
                        <p>
                            <i class="fa fa-lock fa-3x" aria-hidden="true"></i>
                            <input type="password" name="userPassword" id="userPassword" required placeholder="Your password">
                        </p>
                    </div>
                    <input type="submit" name="submit" value="Reg me!">
                </form>
                <a href="login.php" class="registration">Go back</a>
            </div>
        </div>
    </div>
</body>
</html>