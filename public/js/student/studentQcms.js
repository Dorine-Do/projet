function manageErrorChoice (element, color, choice){
    let pError = document.getElementById('p-error')
    if(pError !== null){
        pError.remove()
    }
    let p = document.createElement('p')
    p.innerHTML = `Sélectionne bien ton ${choice}`
    p.id = 'p-error'
    p.style.color = color
    p.style.padding = '.2em'
    element.parentNode.append(p)
}



document.addEventListener('DOMContentLoaded', function(){
    // TRAINING QCM
    let trainingChoicesContainer = document.querySelector('#training_choices');
    let validTrainingQcm = trainingChoicesContainer.querySelector('button');
    validTrainingQcm.addEventListener('click', function(){
        let module = trainingChoicesContainer.querySelector('select')
        let moduleValue =  module.value;
        let difficulty = trainingChoicesContainer.querySelector('input[name="Difficulty"]:checked').value;
        if (moduleValue === ""){
            manageErrorChoice(module, '#ffac8f', 'module')
        }else{
            fetch( "qcm/training/"+moduleValue+"/"+difficulty, {method: 'GET'} )
                .then( response => response.json() )
                .then( result => {
                    window.location.href = 'student/qcms/qcmToDo/'+ result.qcmInstance +'/'
                })
                .catch( error => console.log(error) )

        }
    });

    // DRILL QCM
    let drillChoicesContainer = document.querySelector('#drill_choices');
    let validDrillQcm = drillChoicesContainer.querySelector('button');
    validDrillQcm.addEventListener('click', function(){
        let qcm = drillChoicesContainer.querySelector('select').value;
        if (qcm === ""){
            manageErrorChoice(drillChoicesContainer, '#fff4e4', 'qcm')
        }else{
            window.location.href = "qcms/qcmToDo/"+qcm;
        }

    });

    // RETRY TO GET BADGE
    let retryForBadgesContainer = document.querySelector('#retry_for_badges');
    let validRetryForBadge = retryForBadgesContainer.querySelector('button');
    validRetryForBadge.addEventListener('click', function(){
        let module = retryForBadgesContainer.querySelector('select')
        let moduleValue = module.value;
        if (moduleValue === ""){
            manageErrorChoice(validRetryForBadge, '#ffac8f', 'module')
        }else{
            fetch("qcm/retry_for_badges/"+moduleValue, {method: 'GET'})
                .then( response => response.json())
                .then( result => {
                    window.location.href = window.location.href = 'student/qcms/qcmToDo/'+ result.qcmInstance +'/1';
                })
                .catch( error => console.log(error) )
        }
    });
});