// document.addEventListener('DOMContentLoaded', function(){
//     let session = document.querySelector('#plan_qcm_session');
//     session.addEventListener('change', function(){
//         let form = this.closest('form');
//         let data = {};
//         data[session.getAttribute('name')] = session.value;
//         fetch(form.getAttribute('action'), data)
//             .then( html => {
//                 console.log(html)
//                 document.querySelector('#plan_qcm_module').replaceWith(
//                     document.querySelector(html.responseText).find('#plan_qcm_module')
//                 );
//             })
//     });
// })
//
let sessionfields, moduleField, qcmField, studentsFields, startDateField, endDateField, qcmData, selectedSession = null;

function getDataFromAjaxRequest( url )
{
    // let req = new Request(url, {
    //     method: 'GET',
    //     headers: {
    //         'Content-type': 'application/json'
    //     }
    // });
    return fetch( url, {method: 'GET'} )
        .then( response => console.log(response) )
        // .then( json => console.log(json) );
}

function updateModuleSelect( field, fieldData )
{
    fieldData.forEach( data => {
        let option = document.createElement('option');
        option.value = data.id;
        option.innerText = data.title;
        field.append(option);
    });
}

function updateCheckboxOptions( field, fieldData )
{
    fieldData.forEach( data => {
        let input = document.createElement('input');
        let label = document.createElement('label');
        input.type = 'checkbox';
        input.value = data.id;
        label.innerText = data.firstname + ' ' + data.lastname;


    });
}

document.addEventListener('DOMContentLoaded', function(){

    sessionfields   = document.querySelectorAll('input[name="session"]');
    moduleField     = document.querySelector('#module-choice');
    qcmField        = document.querySelector('#qcm-choice');
    studentsFields  = document.querySelector( '#qcm-students' );
    startDateField  = document.querySelector('#start-time');
    endDateField    = document.querySelector('#end-time');

    sessionfields.forEach( sessionField => {
        sessionField.addEventListener('click', function(){
            selectedSession = this.value;
            let modules = getDataFromAjaxRequest( 'qcm-planner/getSessionModules/' + selectedSession.toString() );
            // let students = getDataFromAjaxRequest( 'qcm-planner/getSessionStudents/' + selectedSession.toString() );
            // updateModuleSelect( moduleField, modules );
            // updateCheckboxOptions( studentsFields, students );
        });
    });



})