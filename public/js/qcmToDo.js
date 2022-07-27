function showModal(event)
{
    event.preventDefault();
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

function hideModal(){
    let modal = document.getElementById("my-modal");
    modal.style.display = "none";
}

document.addEventListener("DOMContentLoaded", (event) => {
    let confirmQcm = document.getElementById('confirm-qcm-btn');
    let valid = document.getElementById("valid");
    let cancelBtn = document.getElementById("cancel-qcm-btn");
    valid.addEventListener('click', showModal);
    confirmQcm.addEventListener('click', submitQcm);
    cancelBtn.addEventListener('click', hideModal);
})
