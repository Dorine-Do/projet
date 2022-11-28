let sessionfields, moduleField, qcmField, studentsFields, startDateField, endDateField, qcmData, selectedSession, divDate, btnValid = null;

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

    document.getElementById('module-choice').scrollIntoView({
        behavior: 'smooth',
        block: "center"
    });
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
        input.name = 'student[]';
        input.value = data.id;
        input.setAttribute('id', 'students['+ data.id +']' )
        label.innerText = data.firstName + ' ' + data.lastName;
        label.setAttribute('for', 'students['+ data.id +']')

        div.append(input, label)
        field.append(div);

    });
    moduleField.addEventListener('change', function (){
        document.getElementById('qcm-choice').scrollIntoView({
            behavior: 'smooth',
            block: "center"
        });
    })

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

    moduleField.addEventListener('change', function (){
        document.getElementById('qcm-students').scrollIntoView({
            behavior: 'smooth',
            block: "center"
        });
    })
}

function buildMessageError(e,parent, message, id){
    e.preventDefault()
    let p = document.createElement('p')
    p.innerHTML = message
    p.style.color = 'red'
    p.style.width = '100%'
    p.style.textAlign = 'center'
    p.id = id
    p.className = "messageError"
    parent.append(p)
}

function submitForm(e){
    let messagesError = document.querySelectorAll('.messageError')
    messagesError.forEach( (message) => {
        message.remove()
    } )

    let firstDate = new Date(startDateField.value)
    let secondDate = new Date(endDateField.value)
    let date = new Date()

    let sessionfield = document.querySelector('input[name="session"]:checked')
    let moduleFieldValue = document.querySelector('#module-choice').value
    let qcmFieldValue = document.querySelector('#qcm-choice').value
    let studentsField = document.querySelectorAll('input[name="student[]"]:checked')

    if(secondDate.getTime() < firstDate.getTime()){
        buildMessageError(e,divDate, "La date de fin ne peut pas être inférieure à la date de début", "errorDate")
    }

    if(firstDate.getTime() < date){
        buildMessageError(e,divDate, "La date ne peut pas être inférieure à la date du jour", "errorDate")
    }

    if (!startDateField.value || !endDateField.value){
        console.log('yep')
        buildMessageError(e,divDate, "Veuillez choisir une date", "errorDates")
    }

    if(!sessionfield){
        buildMessageError(e,sessionfields[0].parentNode.parentNode, "Veuillez choisir une session", "errorSession")
    }

    if(!moduleFieldValue){
        buildMessageError(e,moduleField.parentNode, "Veuillez choisir un module", "errorModule")
    }

    if(!qcmFieldValue){
        buildMessageError(e,qcmField.parentNode, "Veuillez choisir un qcm", "errorQcm")
    }

    if(studentsField.length === 0){
        buildMessageError(e,studentsFields.parentNode, "Veuillez choisir un/des élèves", "errorStudents")
    }
}

document.addEventListener('DOMContentLoaded', function(){

    sessionfields   = document.querySelectorAll('input[name="session"]');
    moduleField     = document.querySelector('#module-choice');
    qcmField        = document.querySelector('#qcm-choice');
    studentsFields  = document.querySelector( '#qcm-students' );
    startDateField  = document.querySelector('#start-time');
    endDateField    = document.querySelector('#end-time');
    divDate = document.querySelector('.allDate');
    btnValid = document.querySelector('.valid');
    btnValid.addEventListener('click', submitForm)


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