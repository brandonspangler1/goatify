function popup(popup_type) {
    get_popup = document.getElementById(popup_type);

    headerWrap = document.getElementById('headerWrap');
    goatifyWrap = document.getElementById('goatifyWrap');
    spotifyWrap = document.getElementById('spotifyWrap');
    footerWrap = document.getElementById('footerWrap');

    if (get_popup.style.display == "flex") {
        get_popup.style.display = "none";
        headerWrap.style.filter = 'none';
        goatifyWrap.style.filter = 'none';
        spotifyWrap.style.filter = 'none';
        footerWrap.style.filter = 'none';
    } else {
        get_popup.style.display = "flex";
        headerWrap.style.filter = 'blur(10px)';
        goatifyWrap.style.filter = 'blur(10px)';
        spotifyWrap.style.filter = 'blur(10px)';
        footerWrap.style.filter = 'blur(10px)';
    }
}

const spotifyProfileHtml = `<div class='spotifyInfoHeaderWrapper'>
                                <h1 class='spotifyInfoHeader'>Profile Details</h1> 
                                <a class='spotifyProfileLink' id='spotifyProfileLink'>View on Spotify</a>
                            </div>
                            <div class='profileLineWrapper'>
                                <h2 class='spotifyProfileHeader'>Name: </h2> 
                                <h2 class='spotifyProfileName' id='profileName'></h2>
                            </div>
                            <div class='profileLineWrapper'>
                                <h2 class='spotifyProfileHeader'>Email: </h2>
                                <h2 class='spotifyProfileEmail' id='profileEmail'></h2>
                            </div>
                            <div class='topSongs'>
                                <h2 class='topSongsHeader'>Top Songs:</h2>
                                <div class='topSongsWrapper'>
                                    <a class='topSong' id='topSong1'></a>
                                    <a class='topSong' id='topSong2'></a>
                                    <a class='topSong' id='topSong3'></a>
                                    <a class='topSong' id='topSong4'></a>
                                    <a class='topSong' id='topSong5'></a>
                                </div>
                            </div>`;

document.addEventListener('DOMContentLoaded', () => {
    var spotifyWrap = document.getElementById('spotifyWrap');

    if (sessionStorage.getItem('profile_info') && sessionStorage.getItem('top_tracks')) {
        spotifyWrap.innerHTML = spotifyProfileHtml;

        displaySpotifyInfo();
 
    }
    else {
        spotifyWrap.innerHTML = '<button class="connectWithSpotifyButton" onclick="requestUserAuthorization()"><img class="spotifyIcon" src="images/Spotify_Icon_Green.png">Connect with Spotify</button>';
    }
});

function displaySpotifyInfo() {
    // Get Profile Data from Session Storage
    profile_data = JSON.parse(sessionStorage.getItem('profile_info'));

    const profileName = document.getElementById('profileName');
    profileName.innerHTML = profile_data.display_name;
    

    const profileLink = document.getElementById('spotifyProfileLink');
    profileLink.setAttribute('href', profile_data.external_urls.spotify);

    const profileEmail = document.getElementById('profileEmail');
    profileEmail.innerHTML = profile_data.email;

    // Get Top Tracks from Session Storage
    top_tracks = JSON.parse(sessionStorage.getItem('top_tracks'));

    // Get First Five Top Songs
    for (var i = 0; i < 5; i++) {
        var topTrack = document.getElementById('topSong' + String((i + 1)));
        var artistList = '';
        var numArtists = top_tracks.items[i].artists.length;

        // Get Artists
        for (var j = 0; j < numArtists; j++) {
            artistList += top_tracks.items[i].artists[j].name;
            if (j < numArtists - 1) {
                artistList += ", ";
            }
        }
        // Update HTML and Link
        topTrack.innerHTML = top_tracks.items[i].name + ' by ' + artistList;
        topTrack.setAttribute('href', top_tracks.items[i].external_urls.spotify);
    }
}

function generateRandomString(length) {
    var text = '';
    var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    for (var i = 0; i < length; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return text;
}

async function requestUserAuthorization() {

    const id = CLIENT_ID; 
    const uri = REDIRECT_URI;

    const state = generateRandomString(16);
    const scope = 'user-read-private user-read-email user-top-read';

    const apiHost = "https://accounts.spotify.com";

    var searchParams = new URLSearchParams({response_type: 'code', client_id: id, scope: scope, redirect_uri: uri, state: state})
    var redirectURL = (apiHost + '/authorize?' + searchParams.toString());

    window.location.href = redirectURL;
}


async function getAccessToken(code, id, secret, uri) {
    const apiHost = "https://accounts.spotify.com";

    var redirectURL = (apiHost + '/api/token');
    var buffer = btoa((id + ':' + secret));

    var formDetails = {
        code: code,
        redirect_uri: uri,
        grant_type: 'authorization_code'
    };

    var formParams = new URLSearchParams(formDetails);


    try {
        let response = await fetch(redirectURL, {
            method: 'POST',
            mode: 'cors',
            headers: {
                'Authorization': 'Basic ' + buffer,
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formParams,
            json: true,
        });
        let responseJSON = await response.json();

        return responseJSON;
    } catch(error) {
        console.error(error);
    }

}

async function getRefreshedAccessToken(refreshToken, id, secret) {
    const apiHost = "https://accounts.spotify.com";
    const redirectURL = (apiHost + '/api/token');
    const buffer = btoa((id + ':' + secret));

    var formDetails = {
        grant_type: 'refresh_token',
        refresh_token: refreshToken
    };

    var formParams = new URLSearchParams(formDetails);

    try {
        let response = await fetch(redirectURL, {
            method: 'POST',
            mode: 'cors',
            headers: {
                'Authorization': 'Basic ' + buffer,
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: formParams,
            json: true,
        });
        let responseJSON = await response.json();

        const access = responseJSON.access_token;

        return access; 
    } catch(error) {
        console.error(error);
    }

}

async function getSpotifyData(code, state) {

    var client_id = CLIENT_ID;
    var client_secret = CLIENT_SECRET;
    var redirect_uri = REDIRECT_URI;

    spotifyAccess = await getAccessToken(code, client_id, client_secret, redirect_uri);

    var access = spotifyAccess.access_token;
    var refreshToken = spotifyAccess.refresh_token;

    await $.ajax({
        url: 'https://api.spotify.com/v1/me',
        headers: {
            'Authorization': 'Bearer ' + access
        },
        success: function(response) {
            sessionStorage.setItem('profile_info', JSON.stringify(response));
        },
        error: function(response) {
            alert("Failed to Get Profile Information");
        }
    });

    access = await getRefreshedAccessToken(refreshToken, client_id, client_secret);

    await $.ajax({
        url: 'https://api.spotify.com/v1/me/top/tracks',
        headers: {
            'Authorization': 'Bearer ' + access,
            'Content-Type': 'application/json'
        },
        success: function(response) {
            sessionStorage.setItem('top_tracks', JSON.stringify(response));
        },
        error: function(response) {
            alert("Failed to Get Top Tracks");
        }
    });

    if (sessionStorage.getItem('profile_info') && sessionStorage.getItem('top_tracks')) {
        var spotifyWrap = document.getElementById('spotifyWrap');
        spotifyWrap.innerHTML = spotifyProfileHtml;
        displaySpotifyInfo();
    }
}