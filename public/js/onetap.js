function handleCredentialResponse()
{
    console.log('credentials received')
}

window.onload = function () {
    google.accounts.id.initialize({
        client_id: '939653659160-g2cn2cgin2ua2cgp33003np8quf1l7m1.apps.googleusercontent.com',
        callback: handleCredentialResponse
    });
    google.accounts.id.prompt();

    /* prompt arg
    (notification) => {
        if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
            // try next provider if OneTap is not displayed or skipped
        }
    }
    matthieu.fergola@3wa.io
     */
}