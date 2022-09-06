<?php
    require('db_connection.php');
    session_start();

    if (!isset($_SESSION['login_successful'])) {

        echo "
            <script>
                window.location.href='index.php'; 
            </script>
            ";
    } 

    $query = "SELECT * FROM `user_information` WHERE `email`= '$_SESSION[email]'";

    $result = mysqli_query($con, $query);

    if ($result) {

        $result_fetch = mysqli_fetch_assoc($result);

        if ($result_fetch['playlist_status'] == 1) {

            $last_song_link = $result_fetch['last_song_link'];

            if ($last_song_link == null) {

                $song_name = "No Song Played Yet";
                $song_status = 0;
            } else {

                $query = "SELECT * FROM `song_$_SESSION[email]` WHERE `link`='$last_song_link'";
                $result = mysqli_query($con, $query);
                $fetch_result = mysqli_fetch_assoc($result);

                $song_name = $fetch_result['song_name'];
                $song_status = 1;
            }

            $last_playlist_id = $result_fetch['last_playlist'];

            if ($last_playlist_id == null) {

                $last_playlist_name = "No Playlist Played Yet";
                $playlist_status = 0;
            } else {

                $playlist_query = "SELECT * FROM `$_SESSION[email]` WHERE `id`='$last_playlist_id'";
                $playlist_result = mysqli_query($con, $playlist_query);

                if ($playlist_result) {

                    $playlist_fetch = mysqli_fetch_assoc($playlist_result);

                    $last_playlist_name = $playlist_fetch['playlist_name'];
                    $playlist_status = 1;
                } else {
                    $last_playlist_name = "No Playlist Played Yet";
                    $playlist_status = 0;
                }
            }
        } else {
            echo "
                <script>
                    alert('You have not created a playlist yet. Please create a playlist to continue to the home page'); 
                    window.location.href='create_playlist.php'; 
                </script>
                ";
        }
    }

    // echo "<script src='scripts/home.js?0.52'></script>";
    // echo "<script src='https://code.jquery.com/jquery-3.5.0.js'></script>";


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music Player</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"> 
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> 
    <link href="https://fonts.googleapis.com/css2?family=Londrina+Solid:wght@100;300;400&family=Montserrat:ital,wght@1,300&family=Open+Sans:wght@300&family=Roboto+Slab:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/home.css" />
    <script src='scripts/obfuscated_home.js'></script>
    <script src='https://code.jquery.com/jquery-3.5.0.js'></script>
</head>

<body class="gridWrap">
    <header class="headerWrapper" id="headerWrap">
        <div class="headerButtonWrapper">
            <a class="headerButton" href='logout.php'>LOGOUT</a>
        </div>

        <h1 class="header">GOATIFY</h1>

        <div class='headerButtonWrapper'>
            <button class="headerButton" onclick="popup('donate-popup')">
                Settings
            </button>
        </div>
    </header>

    <!-- Main Goatify Content -->
    <section class="goatifyWrap" id="goatifyWrap">
        <!-- Search Bar -->
        <section class="searchBar">
            <form class="searchForm" method="POST" action="search.php">
                <input class="searchInput" type="text" placeholder="Search songs" name="search_input" />
                <button class="searchButton" type="submit" class="search-btn" name="search"> <img class="searchIcon" src="images/magnifying-glass.png"/></button>
            </form>
        </section>

        <!-- Playlist List -->
        <section class="playlistListWrapper">
            <div class="playlistHeaderWrapper">
                <h3 class="playlistListHeader">List of Playlists</h3>

                <div class="playlistActionButtonsWrapper">
                    <a class="playlistActionButton" href='create_playlist.php'>CREATE PLAYLIST</a>
                    <a class="playlistActionButton" href='delete_playlist.php'>DELETE PLAYLIST</a>
                </div>
            </div>

            <div class="playlistList">
                <form class="playlistListForm" action='playlist_pages/redirect.php' method='POST'>
                    <?php

                    $query = "SELECT * FROM `$_SESSION[email]`";
                    $result = mysqli_query($con, $query);

                    $playlist_names = array();
                    $i = 0;

                    while ($name = mysqli_fetch_assoc($result)) {
                        array_push($playlist_names, $name['playlist_name']);
                    ?>
                                <button class="playlistButton" type='submit' <?php echo "value='$playlist_names[$i]'" ?> name='playlist_button'>
                                    <?php
                                    echo $playlist_names[$i];
                                    ?>
                                </button>
                    <?php
                        $i++;
                    }
                    ?>
                </form>
            </div>
        </section>
        
        <!-- Playlist Info & Options -->
        <section class="playlistInfo">
            <!-- Last Playlist -->
            <div class="lastPlayedPlaylistWrapper">
                <h3 class="playlistInfoHeader">Last Played Playlist</h3>

                <div class="lastPlayedPlaylistButtonWrapper">
                    <?php if ($playlist_status == 0): ?>
                        <button class="lastPlaylistButton" disabled><?= $last_playlist_name?> </button>
                    <?php endif; ?>
                    <?php if ($playlist_status == 1): ?>
                        <button class="lastPlaylistButton" onclick="<?php $_SESSION['current_song'] = $song_name ?> location.href = 'playlist_pages/playlist_page.php'"><?= $last_playlist_name ?></button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Last Song -->
            <div class="lastPlayedSongWrapper">
                <h3 class="playlistInfoHeader">Last Played Song</h3>

                <div class="lastSongWrapper">
                    <?php if ($song_status == 0): ?>
                        <button class="lastSongButton" disabled><?= $song_name ?></button>
                    <?php endif; ?>
                    <?php if ($song_status == 1): ?>
                        <button class="lastSongButton" onclick="location.href = 'song_pages/song_page.php'"><?= $song_name ?></button>
                    <?php endif; ?>

                    <audio class="lastSongAudioControls" controls>
                        <source src="<?= substr($last_song_link, 3) ?>" type="audio/mpeg">
                        Unfortunately, the audio element is not supported in your browser.
                    </audio>
                </div>
            </div>
        </section>
    </section>

    <section class="spotifyWrap" id="spotifyWrap">



            <!-- <button class="connectWithSpotifyButton" onclick="requestUserAuthorization()"><img class="spotifyIcon" src="images/Spotify_Icon_Green.png">Connect with Spotify</button> -->

            <!-- <h1 class="spotifyInfoHeader">Spotify Information</h1>
            <div class="profileInfoWrapper">
                <h2 class="spotifyProfileHeader">Profile Name: </h2>
                <a class="spotifyProfileInfo" id="profileName"></a>
                <h2 class="spotifyProfileHeader">Email: </h2>
                <h3 class="spotifyProfileInfo" id="profileEmail"></h2>
            </div> -->
    </section>

    <footer class="footerWrap" id="footerWrap">
        <div class="aboutGoatify">
            <h1 class="aboutGoatifyHeader">About Goatify</h1>
            <p class="aboutGoatifyText">
                Goatify was originally a classroom project created by Alfred Yoo, Ahmad Louis, Exdol Davy, and Brandon Spangler. 
                The goal was to be an online music player 'proof of concept'. 
                I later added the ability to connect to your spotify account. 
                This was done using Spotify's Web APIs. 
                I did this because I thought it was a cool idea and some good practice using OAuth 2.0 Authorization Flow and calling different APIs.
            </p>
        </div>

        <!-- <div class="socialLinks">
            <h1 class="socialLinkHeader">LinkedIn <img class="socialIcon" src="images/linkedin_logo_white.png"></h1>
            <div class="socialLinkLine">
                <a class="socialLink">Brandon Spangler</a> 
            </div>
            <div class="socialLinkLine">
                <a class="socialLink">Alfred Yoo</a> 
            </div>
            <div class="socialLinkLine">
                <a class="socialLink">Ahmad Louis</a> 
            </div>
            <div class="socialLinkLine">
                <a class="socialLink">Exdol Davy</a> 
            </div>
        </div> -->
    </footer>

    <article class="settingsPopupWrapper" id="donate-popup">
        <div class="settingsHeaderWrapper">
            <h1 class="settingsHeader">Settings</h1>
            <button class="closeSettingsButton" type="reset" onclick="popup('donate-popup')">
                X
            </button>
        </div>

        <div class="settingButtonWrapper">
            <button class="settingButton" type='button' onclick="popup('email-popup')">
                CHANGE EMAIL
            </button>
        </div>

        <!-- Change Password -->
        <div class="settingButtonWrapper">
            <button class="settingButton" type='button' onclick="popup('password-popup')">
                CHANGE PASSWORD
            </button>
        </div>
    </article>

    <?php
        if (isset($_SESSION['code']) && isset($_SESSION['state'])) {
            echo "
                <script>
                    getSpotifyData('".$_SESSION['code']."', '".$_SESSION['state']."');
                </script>
                ";
            unset($_SESSION['code']);
            unset($_SESSION['state']);
        } 
    ?>
</body>

</html>