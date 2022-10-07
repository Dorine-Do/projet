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
         alert( result );
         hideReportBugModale();
         document.querySelector('#reportBugMsg').value = '';
         document.querySelector('#bugReportUrl').value = '';
         document.querySelector('#bugReportUserId').value = '';
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