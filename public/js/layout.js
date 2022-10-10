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

function showMessage( message ) {
    bugReportMessage.innerHTML = `<p>${ message }</p>`;
    bugReportMessage.style.display = 'block';
    setTimeout( function(){
        bugReportMessage.style.display = 'none';
        bugReportMessage.innerHTML = '';
    }, 3000 );
}

function ajaxSendBugReport(e)
{
    e.preventDefault();
    reportBugForm = document.querySelector('#reportBugForm');
    // let bugMsg      = e.target.querySelector('#reportBugMsg').value;
    // let bugUrl      = e.target.querySelector('#bugReportUrl').value;
    // let bugReporter = e.target.querySelector('#bugReportUserId').value;
    fetch( '/bug/report', {
        method: 'POST',
        body: new FormData(reportBugForm)
    })
     .then( response => response.json() )
     .then( result => {
         hideReportBugModale();
         showMessage( result );
         document.querySelector('#reportBugMsg').value = '';
         document.querySelector('#bugReportUrl').value = '';
         document.querySelector('#bugReportUserId').value = '';
     })
     .catch( error => showMessage( 'Une erreur est survenue' ));
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