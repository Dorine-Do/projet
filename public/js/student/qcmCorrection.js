document.addEventListener("DOMContentLoaded", (event) => {
    let numeroForm = document.querySelectorAll('.numeroForm')
    let allReponse = document.querySelectorAll('.allReponse')

    // Numéro question
    let countNum = 1
    numeroForm.forEach(num => {
        num.innerHTML = countNum
        countNum++
    })

    // Lettre réponse
    allReponse.forEach(reponses => {
        let countLetter = 0
        for (let i = 0 ; i < reponses.children.length ; i++){

            let divReponse = reponses.children[i].querySelector('.divReponse')

            let alphabet = ["A", "B", "C", "D", "E", "F", "G", "H"];

            let end = parseInt(countLetter, 10) + 1; // 4 +1 = 5    '4' + 1 = 41
            let begin = countLetter;
            let letter = alphabet.slice(begin, end);
            let p = document.createElement("p");
            p.className = "pLetter";
            p.innerHTML = letter;

            if (divReponse.firstElementChild.dataset.correction){
                p.style.backgroundColor = "green"
            }

            divReponse.insertBefore(p ,divReponse.firstElementChild )
            countLetter++;
        }

    })

    let btnAddComment = document.querySelector('.addComment')
    if (btnAddComment !== null){
        btnAddComment.addEventListener('click', (e) => {

            let parent = e.target.parentNode
            let pInfoComment = parent.querySelector('.infoComment')
            let input = parent.querySelector('input')

            let result = input.dataset.id
            let comment = input.value

            if (comment === ""){
                pInfoComment.style.display = 'block'
                pInfoComment.innerHTML = "Veuillez remplir le formulaire d'ajout de commentaire avant de le soumettre"
                pInfoComment.style.color = 'red'
            }else {
                pInfoComment.style.display = 'none'
            }
            fetch(`https://127.0.0.1:8000/instructor/qcm_student/correction/${result}/${comment}`)
                .then((response) => response.json())
                .then((data) => {
                    pInfoComment.style.display = 'block'
                    pInfoComment.innerHTML = data
                    pInfoComment.style.color = '#93ad6e'
                    input.value = comment

                });
        })
    }

})
