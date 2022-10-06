// vars
let reportBugBtn, reportBugModale, closeReportBugModaleBtn, reportBugForm;

// functions
function showReportBugModale()
{
    reportBugModale.style.display = 'flex';
}

function hideReportBugModale(){
    reportBugModale.style.display = 'none';
}

function ajaxSendBugReport(e)
{
    e.preventDefault();
    let bugMsg = e.target.querySelector('textarea').innerText;
    console.log( bugMsg )
    fetch( '/bug-report/' + bugMsg, { method: 'GET' } )
     .then( response => response.json() )
     .then( result => {
        console.log('Merci pour votre retour, un mail a été envoyé !');
     })
     .catch( error => console.log(error) );
}

// code principal

document.addEventListener('DOMContentLoaded', function(){

    reportBugBtn            = document.querySelector('#reportBugBtn');
    reportBugModale         = document.querySelector('#reportBugModale');
    closeReportBugModaleBtn = document.querySelector('#closeReportBugModaleBtn');
    reportBugForm           = document.querySelector('#reportBugForm');

    reportBugBtn.addEventListener('click', showReportBugModale);
    closeReportBugModaleBtn.addEventListener('click', hideReportBugModale);
    reportBugForm.addEventListener('submit', ajaxSendBugReport);
});