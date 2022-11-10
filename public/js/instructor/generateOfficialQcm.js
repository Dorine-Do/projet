let sessionsLis, moduleSelect, sessionField;

function fetchModules(e)
{
    let sessionId = e.target.value;
    // document.querySelector('#sessionToDistribute').value = sessionId;
    fetch( `../../instructor/qcm-planner/getSessionModules/${sessionId}` )
        .then( response => response.json() )
        .then( modules => displayModulesOptions(modules) );
}

function displayModulesOptions(modules)
{
    moduleSelect = document.querySelector( '#module' );

    moduleSelect.innerHTML = '';

    modules.forEach( module => {
        let option = document.createElement('option');
        option.value = module.id;
        option.innerText = module.title;

        moduleSelect.append(option);
    });

    moduleSelect.scrollIntoView({
        behavior: 'smooth'
    });
}

function messageError(e){
    sessionField = document.querySelector('input[name="session"]:checked')
    console.log(sessionField)
    console.log(moduleSelect)

    if(moduleSelect === 'undefined'){
        let p = document.createElement('p')
        p.innerHTML = 'Veuillez sélectionner un module'
        p.style.color = 'red'
        p.style.width = '100%'
        p.style.textAlign = 'center'
        p.className = "messageError"
        moduleSelect.parentNode.append(p)
        e.preventDefault()
    }

    if(sessionField === null){
        let p = document.createElement('p')
        p.innerHTML = 'Veuillez sélectionner une session'
        p.style.color = 'red'
        p.style.width = '100%'
        p.style.textAlign = 'center'
        p.className = "messageError"
        sessionField.parentNode.parentNode.append(p)
        e.preventDefault()
    }
    e.preventDefault()
}

document.addEventListener('DOMContentLoaded', function(){

    let btnSubmit = document.querySelector('.buttonSubmit')
    btnSubmit.addEventListener('click', messageError)

    sessionsLis = document.querySelectorAll('.liSession');

    sessionsLis.forEach( sessionLi => {
        sessionLi.addEventListener('click', fetchModules)
    } )

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

});