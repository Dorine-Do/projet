let sessionsLis, moduleSelect;

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

document.addEventListener('DOMContentLoaded', function(){

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