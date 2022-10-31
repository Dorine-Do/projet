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

function showValidationComment()
{

    let modal = document.getElementById("comment-modal");
    modal.style.display = "block";
}

function submitQcm()
{
    let form = document.querySelector('form');
    let modal = document.getElementById("my-modal");
    let comment = document.getElementById("comment-modal");
    modal.style.display = "none";
    comment.style.display = "none";

    let textaera = comment.querySelector('textarea')
    form.append(textaera);
    form.submit();
}

function hideModals()
{
    let modal = document.getElementById("my-modal");
    let comment = document.getElementById("comment-modal");
    let errorModal = document.getElementById("error-modal");
    modal.style.display = "none";
    comment.style.display = "none";
    errorModal.style.display = "none";
}




document.addEventListener("DOMContentLoaded", (event) => {
    let numeroForm = document.querySelectorAll('.numeroForm')
    let allReponse = document.querySelectorAll('.allReponse')

    let confirmQcm = document.getElementById('confirm-qcm-btn');
    let valid = document.getElementById("valid");
    let cancelBtn = document.getElementById("cancel-qcm-btn");
    let cancelErrorBtn = document.getElementById('cancel-error-qcm-btn')
    let confirmPopup = document.getElementById('confirm-comment-btn')
    let cancelComment = document.getElementById('cancel-comment-btn')
    let divReponses = document.querySelectorAll('.divReponse')
    valid.addEventListener('click', showModal);
    confirmQcm.addEventListener('click', submitQcm);
    cancelBtn.addEventListener('click', hideModals);
    cancelComment.addEventListener('click', submitQcm);
    cancelErrorBtn.addEventListener('click', hideModals);
    confirmPopup.addEventListener('click', showValidationComment)

    divReponses.forEach(response => {
        response.addEventListener('click', function (){
            response.closest('.reponse').querySelector('input').click()
        })
    })

    // Numéro question
    let countNum = 1
    numeroForm.forEach(num => {
        num.innerHTML = countNum
        countNum ++
    })

    // Lettre réponse
    allReponse.forEach(reponses =>{
        let countLetter = 0
        for(let i = 0 ; i < reponses.children.length ; i++){

            let divReponse = reponses.children[i].lastElementChild
            let alphabet = ["A", "B", "C", "D", "E", "F", "G", "H"];

            let end = parseInt(countLetter, 10) + 1; // 4 +1 = 5    '4' + 1 = 41
            let begin = countLetter;
            let letter = alphabet.slice(begin, end);
            let p = document.createElement("p");
            p.className = "pLetter";
            p.innerHTML = letter;

            divReponse.insertBefore(p,divReponse.firstElementChild )
            countLetter++;
        }

    })
})
