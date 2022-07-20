
function checker(event) {
    event.preventDefault();
    let result = confirm('Êtes vous sûr de vouloir valider ce QCM ?')
    if (result){
        this.removeEventListener('submit', checker)
        this.submit()
    }
}

document.addEventListener("DOMContentLoaded", (event) => {
    let form = document.querySelector('form')
    form.addEventListener('submit', checker)
})