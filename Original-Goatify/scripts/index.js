function popup(popup_type) {
    get_popup = document.getElementById(popup_type);

    if (get_popup.style.display == "flex") {
        get_popup.style.display = "none";
    } else {
        get_popup.style.display = "flex";
    }
}

function forgotPassPopup() {

    document.getElementById('login-popup').style.display = "none";
    document.getElementById('forgot-popup').style.display = "flex";

}

function focusEmail() {
    document.getElementById('loginEmailText').focus();
}

function focusPassword() {
    document.getElementById('loginPassText').focus();
}

if (sessionStorage.getItem('profile_info')) {
    profile_data_string = sessionStorage.getItem('profile_info')
    console.log("From session storage:\n" + profile_data_string);
    profile_data = JSON.parse(profile_data_string);
    console.log(profile_data);
    // document.getElementById('profile_name').innerHTML = response.display_name;
}

const searchParams = new URLSearchParams({response_type: 'code'})

const apiHost = "https://accounts.spotify.com"

var access = "";

var client_id = '261c04b256e94036804d51019492e734'; // Your client id
var client_secret = '9e432db539ab49a8a0cb22ef6306692a'; // Your secret
var redirect_uri = 'https://brandonspangler.com/goatify2/spotify-at-goatify/Original-Goatify/after_code.php'; // Your redirect uri

var generateRandomString = function(length) {
    var text = '';
    var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

    for (var i = 0; i < length; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return text;
}

async function requestUserAuthorization() {
    var state = generateRandomString(16);
    var scope = 'user-read-private user-read-email';

    var searchParams = new URLSearchParams({response_type: 'code', client_id: client_id, scope: scope, redirect_uri: redirect_uri, state: state})
    var redirectURL = (apiHost + '/authorize?' + searchParams.toString());
    console.log(redirectURL);

    window.location.href = redirectURL;
}

async function getAccessToken(code, state) {
    // console.log("The Code:\n" + code);
    // console.log("The State:\n" + state);

    var searchParams = new URLSearchParams({code: code, redirect_uri: redirect_uri, grant_type: 'authorization_code'})
    var redirectURL = (apiHost + '/api/token');
    var buffer = btoa((client_id + ':' + client_secret));

    // console.log(redirectURL);
    // console.log(buffer);

    var formDetails = {
        code: code,
        redirect_uri: redirect_uri,
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
        // return responseJSON;
        // console.log(responseJSON);
        // console.log(responseJSON.access_token);
        access = responseJSON.access_token;
        // console.log(access);
        $.ajax({
            url: 'https://api.spotify.com/v1/me',
                headers: {
                  'Authorization': 'Bearer ' + access
                },
                success: function(response) {
                //   console.log(response);
                  sessionStorage.setItem('profile_info', JSON.stringify(response));
                  document.getElementById('profile_name').innerHTML = response.display_name;
                }
        });
    } catch(error) {
        console.error(error);
    }
}