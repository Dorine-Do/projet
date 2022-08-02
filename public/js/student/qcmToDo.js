function showModal(event)
{
    event.preventDefault();

    if( isQcmComplete() )
    {
        showValidationModal();
    }
    else
    {
        showErrorModal();
    }
}

function isQcmComplete()
{
    let questions = document.querySelectorAll('.allReponse' );
    let questionsComplete = 0;
    for( let q = 0; q < questions.length; q++ )
    {
        let checkedInputs = questions[q].querySelectorAll('input:checked');
        if( checkedInputs.length >= 1 )
        {
            questionsComplete++;
        }
    }
    console.log(questions.length)
    console.log(questionsComplete)
    console.log(questionsComplete === questions.length)
    return questionsComplete === questions.length;
}

function showErrorModal()
{
    let errorModal = document.getElementById("error-modal");
    errorModal.style.display = "block";
}

function showValidationModal()
{

    let modal = document.getElementById("my-modal");
    modal.style.display = "block";
}

function submitQcm()
{
    let form = document.querySelector('form');
    let modal = document.getElementById("my-modal");
    modal.style.display = "none";
    form.submit();
}

function hideModals(){
    let modal = document.getElementById("my-modal");
    let errorModal = document.getElementById("error-modal");
    modal.style.display = "none";
    errorModal.style.display = "none";
}



document.addEventListener("DOMContentLoaded", (event) => {
    let confirmQcm = document.getElementById('confirm-qcm-btn');
    let valid = document.getElementById("valid");
    let cancelBtn = document.getElementById("cancel-qcm-btn");
    let cancelErrorBtn = document.getElementById('cancel-error-qcm-btn')
    valid.addEventListener('click', showModal);
    confirmQcm.addEventListener('click', submitQcm);
    cancelBtn.addEventListener('click', hideModals);
    cancelErrorBtn.addEventListener('click', hideModals);
})
