<?php

require('db_connection.php');
session_start();

# If Login is successful, direct the user to the home page 
if (isset($_SESSION['login_successful']) && $_SESSION['login_successful'] == true) {

    echo "
        <script>
            window.location.href='home.php'; 
        </script>
        ";
} 
?>

<!DOCTYPE html>
<html lang="en"> 

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Music Player</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"> 
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> 
    <link href="https://fonts.googleapis.com/css2?family=Londrina+Solid:wght@100;300;400&family=Montserrat:ital,wght@1,300&family=Open+Sans:wght@300&family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css" />
    <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
</head>

<body>
    <div class="gridWrap">
        <header class="header">
            <div class="headerTitlesWrapper">
                <h3 class="headerSpotify">Spotify @</h3>
                <h1 class="headerTitle">GOATIFY</h1>
            </div>
        </header>

        <section class="loginBox">
            <form class="loginForm" method="POST" action="account_changes/login_create_account.php">
                <h3 class="loginHeader">Enter Email</h3>
                <div class="loginLine">
                    <button class="loginIconButton" onClick="focusEmail()">
                        <img class="loginIcon" src="images/account.png"/>
                    </button>
                    <input class="loginInput" id="loginEmailText" type="email" placeholder="example@gmail.com" name="email" required />
                </div>

                <h3 class="loginHeader" >Enter Password</h3>
                <div class="loginLine"> 
                    <button class="loginIconButton" onClick="focusPassword()">
                        <img class="loginIcon" src="images/lock.png"/>
                    </button>
                    <input class="loginInput" id="loginPassText" type="password" placeholder="goatifyislife" name="password" required />
                </div>

                <button class="loginButton" type="submit" class="login-btn" name="login">LOGIN</button>
            </form>
            <div class="loginExtrasWrapper">
                <button class="loginExtras" onclick="popup('create-account-popup')">Create Account</button> 
                <button class="loginExtras" onclick="forgotPassPopup()">Forgot Password</button>
            </div>
        </section>
    </div>

    <!-- <div class="create-account">
        <button type="button" onclick="popup('create-account-popup')">
            CREATE ACCOUNT
        </button>
    </div> -->
<!-- 
    <div class="login">
        <button type="button" onclick="popup('login-popup')">
            LOGIN
        </button>
    </div> -->

    <!-- <div class="login">
        <button type="button" onclick="popup('spotify-login-popup')" disabled>
            SPOTIFY LOGIN
        </button>
    </div> -->

    <div class="popup-container" id="spotify-login-popup">
        <div class="login_popup">
            <form class="form_styles" id="spotify_form" method="POST" action="javascript:requestUserAuthorization()">
                <h2>
                    <span>USER LOGIN</span>
                    <button type="reset" onclick="popup('spotify-login-popup')">X</button>
                </h2>
                <input type="email" placeholder="Spotify Email" name="email"/>
                <input type="password" placeholder="Password" name="password" />
                <button type="submit" id="spotify_submit" lass="login-btn" name="login">LOGIN</button>
            </form>
            <div class="password-forgot-btn">
                <button type="button" onclick="forgotPassPopup()">
                    Forgot Password
                </button>
            </div>
        </div>
    </div>

    <div class="popup-container" id="login-popup">
        <div class="login_popup">
            <form class="form_styles" method="POST" action="account_changes/login_create_account.php">
                <h2>
                    <span>USER LOGIN</span>
                    <button type="reset" onclick="popup('login-popup')">X</button>
                </h2>
                <input type="email" placeholder="Email" name="email" required />
                <input type="password" placeholder="Password" name="password" required />
                <button type="submit" class="login-btn" name="login">LOGIN</button>
            </form>
            <div class="password-forgot-btn">
                <button type="button" onclick="forgotPassPopup()">
                    Forgot Password
                </button>
            </div>
        </div>
    </div>

    <div class="forgotPasswordPopup" id="forgot-popup">
        <div class="popupHeaderWrapper">
            <h2 class="popupHeader">PASSWORD RESET</h2>
            <button class="closePopupButton" type="reset" onclick="popup('forgot-popup')">X</button>
        </div>

        <form class="form_styles" method="POST" action="account_changes/forgot_password_reset.php">
            <h3 class="popupInputHeader">Enter Email</h3>
            <input class="popupInput" type="email" placeholder="example@gmail.com" name="email" required />

            <h3 class="popupInputHeader">What is your Mother's Maiden name?</h3>
            <input class="popupInput" type="text" placeholder="Spangler" name="sq_one" required />

            <h3 class="popupInputHeader">What is your childhood nickname?</h3>
            <input class="popupInput" type="text" placeholder="Spangy" name="sq_two" required />

            <h3 class="popupInputHeader">What city were you born in?</h3>
            <input class="popupInput" type="text" placeholder="Orlando" name="sq_three" required />

            <button class="popupSubmit" type="submit" class="preset-btn" name="send-password-reset-link">CONFIRM</button>
        </form> 
    </div>

    <div class="createAccountPopup" id="create-account-popup">
        <div class="popupHeaderWrapper">
            <h2 class="popupHeader">CREATE ACCOUNT</h2>
            <button class="closePopupButton" type="reset" onclick="popup('create-account-popup')">X</button>
        </div>

        <form class="form_styles" method="POST" action="account_changes/login_create_account.php">
            <h3 class="popupInputHeader">Enter Email</h3>
            <input class="popupInput" type="email" placeholder="example@gmail.com" name="email" required />

            <h3 class="popupInputHeader">Enter Password</h3>
            <input class="popupInput" type="password" placeholder="goatifyislife" name="password" required />

            <h3 class="popupInputHeader">What is your Mother's Maiden name?</h3>
            <input class="popupInput" type="text" placeholder="Spangler" name="sq_one" required />

            <h3 class="popupInputHeader">What is your childhood nickname?</h3>
            <input class="popupInput" type="text" placeholder="Spangy" name="sq_two" required />

            <h3 class="popupInputHeader">What city were you born in?</h3>
            <input class="popupInput" type="text" placeholder="Orlando" name="sq_three" required />

            <button class="popupSubmit" type="submit" class="register-btn" name="register">
                CREATE ACCOUNT
            </button>
            </form>
    </div>


    <script src="scripts/index.js"></script>
   
</body>

</html>