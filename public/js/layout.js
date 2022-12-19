// vars
let reportBugBtn, reportBugModale, closeReportBugModaleBtn, reportBugForm, bugReportMessage;

// functions
function showReportBugModale()
{
    reportBugModale.style.display = 'flex';
}

function hideReportBugModale(){
    reportBugModale.style.display = 'none';
}

function showMessage( message, msgType ) {
    if( msgType === 'success' )
    {
        bugReportMessage.classList.add('successBorder');
        bugReportMessage.classList.add('successMsg');
    }
    if( msgType === 'error' )
    {
        bugReportMessage.classList.add('errorBorder');
        bugReportMessage.classList.add('errorMsg');
    }
    bugReportMessage.innerHTML = `<p>${ message }</p>`;
    bugReportMessage.style.display = 'block';
    setTimeout( function(){
        bugReportMessage.style.display = 'none';
        bugReportMessage.innerText = '';
        bugReportMessage.classList.remove(...bugReportMessage.classList);
    }, 3000 );
}

function ajaxSendBugReport(e)
{
    e.preventDefault();
    reportBugForm = document.querySelector('#reportBugForm');
    fetch( '/bug/report', {
        method: 'POST',
        body: new FormData(reportBugForm)
    })
     .then( response => response.json() )
     .then( result => {
         hideReportBugModale();
         let msgType = 'success';
         showMessage( result, msgType );
         document.querySelector('#reportBugMsg').value = '';
         document.querySelector('#bugReportUrl').value = '';
         document.querySelector('#bugReportUserId').value = '';
     })
     .catch( error => {
         let msgType = 'error';
         showMessage( 'Une erreur est survenue', msgType );
     });
}

// code principal

document.addEventListener('DOMContentLoaded', function(){

    reportBugBtn            = document.querySelector('#reportBugBtn');
    reportBugModale         = document.querySelector('#reportBugModale');
    closeReportBugModaleBtn = document.querySelector('#closeReportBugModaleBtn');
    reportBugForm           = document.querySelector('#reportBugForm');
    bugReportMessage        = document.querySelector('#bugReportMessage');

    reportBugBtn.addEventListener('click', showReportBugModale);
    closeReportBugModaleBtn.addEventListener('click', hideReportBugModale);
    reportBugForm.addEventListener('submit', ajaxSendBugReport);
});