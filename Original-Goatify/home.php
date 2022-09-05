<?php
    require('../db_connection.php');
    session_start();

    $query = "SELECT * FROM `user_information` WHERE `email`= '$_SESSION[email]'";

    echo "<script src='../scripts/index.js'></script>";
    echo "<script src='https://code.jquery.com/jquery-3.5.0.js'></script>";

    if (isset($_SESSION['code']) && isset($_SESSION['state'])) {      
        echo "
            <script>
                getAccessToken('".$_SESSION['code']."', '".$_SESSION['state']."');
            </script>
            ";
        unset($_SESSION['code']);
        unset($_SESSION['state']);
    } 

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
                    window.location.href='../create_playlist.php'; 
                </script>
                ";
        }
    }
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
</head>

<body>

    <header class="headerWrapper">
        <div class="headerButtonWrapper">
            <a class="headerButton" href='../logout.php'>LOGOUT</a>
        </div>

        <h1 class="header">GOATIFY</h1>

        <div class='headerButtonWrapper'>
            <button class="headerButton" onclick="popup('donate-popup')">
                Settings
            </button>
        </div>

        <div class="popup_container" id="donate-popup">
            <div class="donation popup">
                <div>
                    <button type="reset" onclick="popup('donate-popup')">
                        X
                    </button>
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
                </div>
            </div>
        </div>

        <script>
            function popup(popup_type) {
                get_popup = document.getElementById(popup_type);

                if (get_popup.style.display == "flex") {
                    get_popup.style.display = "none";
                } else {
                    get_popup.style.display = "flex";
                }
            }
        </script>
    </header>

    
    <section class="goatifyWrap">
        <!-- Search Bar -->
        <section class="searchBar">
            <form class="searchForm" method="POST" action="../search.php">
                <input class="searchInput" type="text" placeholder="Search songs" name="search_input" />
                <button class="searchButton" type="submit" class="search-btn" name="search"> <img class="searchIcon" src="../images/magnifying-glass.png"/></button>
            </form>
        </section>

        <section class="playlistListWrapper">
            <!-- List of Playlists, New Playlist, Delete Playlist  -->
            <!-- <div class="head_title"> -->
                <h3 class="playlistListHeader">List of Playlists</h3>
            <!-- </div> -->

            <!-- <div class="playlists"> -->
                <div class="playlistList">
                    <!-- <table border="1px"> -->
                        <form class="playlistListForm" action='../playlist_pages/redirect.php' method='POST'>
                            <?php

                            $query = "SELECT * FROM `$_SESSION[email]`";
                            $result = mysqli_query($con, $query);

                            $playlist_names = array();
                            $i = 0;

                            while ($name = mysqli_fetch_assoc($result)) {
                                array_push($playlist_names, $name['playlist_name']);
                            ?>
                                <!-- <tr class="names"> -->
                                    <!-- <td> -->
                                        <button class="playlistButton" type='submit' <?php echo "value='$playlist_names[$i]'" ?> name='playlist_button'>
                                            <?php
                                            echo $playlist_names[$i];
                                            ?>
                                        </button>
                                    <!-- </td> -->
                                <!-- </tr> -->
                            <?php
                                $i++;
                            }
                            ?>
                        </form>
                    <!-- </table> -->
                </div>

            <!-- </div> -->

           
        </section>

    <section class="playlistInfo">
        <!-- Last Playlist -->
        <!-- <div class="second_title"> -->
            <h3 class="playlistInfoHeader">Last Played Playlist</h3>
        <!-- </div> -->

        <div class="last_playlist">
            <table border="1px">
            <tr class="name">
                <td>
                    <div class="last_played_playlist">
                        <?php if ($playlist_status == 0) { ?>
                        <button disabled><?php echo $last_playlist_name; ?></button>
                        <?php } else { ?>
                        <button onclick="<?php $_SESSION['current_playlist'] = $last_playlist_name; ?> location.href = '../playlist_pages/playlist_page.php'"><?php echo $last_playlist_name; ?></button>
                        <?php } ?>
                    </div>
                </td>
            </tr>
            <?php
            ?>
            </table>
        </div>

        <!-- Last Song -->
        <!-- <div class="third_title"> -->
            <h3 class="playlistInfoHeader">Last Played Song</h3>
        <!-- </div> -->

        <div class="last_song">
            <table border="1px">
                <tr class="song_name">
                    <td>
                        <div class="music">
                            <?php if ($song_status == 0) { ?>
                                <button disabled><?php echo $song_name; ?></button>
                            <?php } else { ?>
                                <button onclick="<?php $_SESSION['current_song'] = $song_name; ?> location.href = '../song_pages/song_page.php'"><?php echo $song_name; ?></button>
                            <?php } ?>
                        </div>
                        <div>
                            <?php
                            echo '<audio controls>
                                <source src="' . $last_song_link . '" type="audio/mpeg">
                                Unfortunately, the audio element is not supported in your browser.
                                </audio>';
                            ?>
                        </div>

                    </td>
                </tr>
                <?php
                ?>
            </table>
        </div>
    </section>

        <section class="playlistActionButtons">
            <!-- <div class="playlist_actions"> -->
                <div class="playlistActionButtonWrapper">
                    <a class="playlistActionButton" href='../create_playlist.php'>CREATE PLAYLIST</a>
                </div>
                <div class="playlistActionButtonWrapper">
                    <a class="playlistActionButton" href='../delete_playlist.php'>DELETE PLAYLIST</a>
                </div>
            <!-- </div> -->

            <!-- <div class="change"> -->
                <!-- Change Email -->

            <!-- </div> -->
        </section>

        <div class="popup_container" id="email-popup">
            <div class="email_popup">
                <form style="text-align: center" method="POST" action="../account_changes/change_account_info.php">
                    <h2>
                        <span>CHANGE EMAIL</span>
                        <button type="reset" onclick="popup('email-popup')">X</button>
                    </h2>
                    <input type="email" placeholder="Current Email" name="p_email" required />
                    <input type="email" placeholder="New Email" name="email" required />
                    <br /><br />
                    <button type="submit" class="email-btn" name="change_email">SUBMIT</button>
                </form>
            </div>
        </div>

        <div class="popup_container" id="password-popup">
            <div class="password_popup">
                <form style="text-align: center" method="POST" action="../account_changes/change_account_info.php">
                    <h2>
                        <span>CHANGE PASSWORD</span>
                        <button type="reset" onclick="popup('password-popup')">X</button>
                    </h2>
                    <input type="password" placeholder="Current Password" name="p_password" required />
                    <input type="password" placeholder="New Password" name="password" required />
                    <br /><br />
                    <button type="submit" class="password-btn" name="change_password">SUBMIT</button>
                </form>
            </div>
        </div>

        <script>
            function popup(popup_type) {
                get_popup = document.getElementById(popup_type);

                if (get_popup.style.display == "flex") {
                    get_popup.style.display = "none";
                } else {
                    get_popup.style.display = "flex";
                }
            }
        </script>
    </section>

    <section class="spotifyWrap">
        <button>In spotify</button>
    </section>

    <footer class="footerWrap">
        <button>In footer</button>
    </footer>
</body>

</html>