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