let sessionsLis, moduleSelect;

function fetchModules()
{
    let sessionId = e.target.dataset.sessionId;

    fetch( `instructor/qcm-planner/getSessionModules/${sessionId}` )
        .then( response => response.json() )
        .then( modules => displayModulesOptions(modules) );
}

function displayModulesOptions(modules)
{
    moduleSelect = document.querySelector( '#module' );

    moduleSelect.innerHTML = '';

    modules.foreach( module => {
        let option = document.createElement('option');
        option.value = module.id;
        option.innerText = module.title;

        moduleSelect.append(option);
    });
}

document.addEventListener('DOMContentLoaded', function(){

    sessionsLis = document.querySelectorAll('#sessions-list li');

    sessionsLis.forEach( sessionLi => {
        sessionLi.addEventListener('click', fetchModules)
    } )

});