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
        let qcm = trainingChoicesContainer.querySelector('select').value;

        window.location.href = "student/qcm/qcmDone/"+qcm;
    });
});