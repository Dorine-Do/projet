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
        console.log(reponses)
        let countLetter = 0
        for (let i = 0 ; i < reponses.children.length ; i++){

            let divReponse = reponses.children[i].querySelector('.divReponse')

            console.log(divReponse)
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
})
