document.addEventListener('DOMContentLoaded', function(){
    // TRAINING QCM
    let trainingChoicesContainer = document.querySelector('#training_choices');
    let validTrainingQcm = trainingChoicesContainer.querySelector('button');
    validTrainingQcm.addEventListener('click', function(){
        let module = trainingChoicesContainer.querySelector('select').value;
        let difficulty = trainingChoicesContainer.querySelector('input[name="Difficulty"]').value;
        window.location.href = "qcm/training?module="+module+"&difficulty="+difficulty;
    });

    // DRILL QCM
    let drillChoicesContainer = document.querySelector('#drill_choices');
    let validDrillQcm = drillChoicesContainer.querySelector('button');
    validDrillQcm.addEventListener('click', function(){
        let qcm = drillChoicesContainer.querySelector('select').value;
        if (qcm === ""){
            let p = document.createElement('p')
            p.innerHTML = "Sélectionne bien ton qcm"
            p.style.color = '#fff4e4'
            p.style.padding = '.2em'

            drillChoicesContainer.parentNode.append(p)
        }else{
            window.location.href = "qcms/qcmToDo/"+qcm;
        }

    });

    // RETRY TO GET BADGE
    let retryForBadgesContainer = document.querySelector('#retry_for_badges');
    let validRetryForBadge = retryForBadgesContainer.querySelector('button');
    validRetryForBadge.addEventListener('click', function(){
        let module = retryForBadgesContainer.querySelector('select').value;
        window.location.href = "qcm/retry_for_badges/"+module;
    });
});