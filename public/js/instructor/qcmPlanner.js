let sessionfields, moduleField, qcmField, studentsFields, startDateField, endDateField, qcmData, selectedSession = null;

async function updateModulesFromAjax( session, field )
{
    await fetch( 'qcm-planner/getSessionModules/'+ session, {method: 'GET'} )
        .then( response => response.json() )
        .then( json => updateModuleSelect( field, json ) );
}

async function updateStudentsFromAjax( session, field )
{
    await fetch( 'qcm-planner/getSessionStudents/' + session, {method: 'GET'} )
        .then( response => response.json() )
        .then( json => updateStudentOptions( field, json ) );
}

async function updateQcmsFromAjax( module, field )
{
    await fetch( 'qcm-planner/getModuleQcms/' + module, {method: 'GET'} )
        .then( response => response.json() )
        .then( json => updateQcmSelect( field, json ) );
}

function updateModuleSelect( field, fieldData )
{
    field.innerHTML = '';
    fieldData.forEach( data => {
        let option = document.createElement('option');
        option.value = data.id;
        option.innerText = data.title;
        field.append(option);
    });
    updateQcmsFromAjax( fieldData[0].id, qcmField );
}

function updateStudentOptions( field, fieldData )
{
    field.innerHTML = '';
    fieldData.forEach( data => {
        let input = document.createElement('input');
        let label = document.createElement('label');
        let div = document.createElement('div');
        div.classList.add('divStudentOption')
        input.type = 'checkbox';
        input.name = 'students['+ data.id +']';
        input.value = data.id;
        label.innerText = data.firstName + ' ' + data.lastName;

        div.append(input, label)
        field.append(div);

    });
}

function updateQcmSelect( field, fieldData )
{
    field.innerHTML = '';
    fieldData.forEach( data => {
        let option = document.createElement('option');
        option.value = data.id;
        option.innerText = data.title;
        field.append(option);
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
            updateModulesFromAjax( selectedSession.toString(), moduleField );
            updateStudentsFromAjax( selectedSession.toString(), studentsFields );
        });
    });

    moduleField.addEventListener('change', function (){
        updateQcmsFromAjax( moduleField.value, qcmField );
    })


    // Position des label par rapport a leur input
    let liSession = document.querySelectorAll('.liSession')
    liSession.forEach( li => {

        let label = li.querySelector('label')
        let input = li.querySelector('input')

        let widthInput = input.getBoundingClientRect().width
        let heightInput = input.getBoundingClientRect().height

        let widthLabel = label.getBoundingClientRect().width
        let heightLabel = label.getBoundingClientRect().height

        label.style.top = ((heightInput/2)-(heightLabel/2)) + 'px'

        if (widthInput < widthLabel){
            input.style.width = (widthLabel + 10) + 'px'
        }else {
            label.style.width = (widthInput-10) + 'px'
        }

    })

})