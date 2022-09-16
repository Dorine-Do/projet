let lineY, lineX, passOrNot, timeLine, isOfficialQcm, divOdd, divEven, passOrNots = null;

function lineYManagment(){
    lineY = document.querySelectorAll('.lineY')
    lineY.forEach( (line,key) => {
        let parent = line.parentNode
        let parentWidth = parent.getBoundingClientRect().width
        line.style.left = parentWidth/2 + 'px'

        if ( (key+1) % 2 === 0 ){
            line.style.top = 47 + 'px'
        }else {
            line.style.top = 4 + 'px'
        }
    } )
}

function lineXManagment(){
    let timelineX = timeLine.getBoundingClientRect().x
    let passOrNotX = passOrNot.getBoundingClientRect().x
    let diff = passOrNotX - timelineX
    lineX.style.marginLeft = diff + 20 + 'px'
}

function divIsOfficialManagment(){

    let passOrNots = document.querySelectorAll('.passOrNot')
    passOrNots.forEach( (p,key) => {
        if ((key+1) % 2){
            p.style.marginTop = 0
            p.style.marginBottom = 2 + 'em'
            p.parentNode.classList.add('isOfficialQcmOdd')
        }else{
            p.style.marginBottom = 0
            p.style.marginTop= 1.5 + 'em'
        }
    } )

    // Positionnement des div isOfficialQcm
    let divIsOfficials = document.querySelectorAll('.isOfficialQcm')
    let left = divIsOfficials[0].getBoundingClientRect().left
    // Convertion px to em
    left = left/16
    let divPrev = null
    let levelMaxByModule = 0;
    for (let i =0; i < divIsOfficials.length; i++){
        if (i !== 0) {
            // Si il n'y a pas de changement de module
            if (divPrev === divIsOfficials[i].querySelector('.moduleTitle').dataset.id) {
                if (i === 1) {
                    divIsOfficials[i].style.left = left - 11 + 'em'
                    left = left - 11
                    divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                } else if (i !== 0) {
                    divIsOfficials[i].style.left = left - 7 + 'em'
                    left = left - 7
                    divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                }
                if (levelMaxByModule < divIsOfficials[i]){
                    levelMaxByModule = divIsOfficials[i].dataset.level
                }
            }
            // Si il y a un changement de module, afficher le logo YouUp si l'étudiant à réussi à avoir la moyenne au moins
            // une fois aux qcms de fin de semaine.
            else {
                let pBagde = document.createElement('p')
                pBagde.classList.add('pBadge')
                if ( levelMaxByModule > 2 ){
                    let imgYouUp = document.createElement('img')
                    imgYouUp.setAttribute('src', logo)
                    imgYouUp.setAttribute('alt', 'Logo YouUp')
                    imgYouUp.classList.add('imgYouUp')
                    pBagde.append(imgYouUp)
                }else{
                    let imgYouUp = document.createElement('img')
                    imgYouUp.setAttribute('src', wrong)
                    imgYouUp.setAttribute('alt', 'Croix')
                    imgYouUp.classList.add('wrong')
                    pBagde.append(imgYouUp)
                }
                pBagde.style.left = left - 2 + 'em'
                left = left + 3

                divIsOfficials[i].parentNode.insertBefore(pBagde,divIsOfficials[i])
                divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                i --
            }
        }
        else {
            left = divIsOfficials[i].getBoundingClientRect().left
            left = left/16
            divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
            levelMaxByModule = divIsOfficials[i].dataset.level
        }
    }

}

document.addEventListener("DOMContentLoaded", (event) => {
    lineX = document.querySelector('.lineX')
    divIsOfficialManagment()
    passOrNot = document.querySelector('.passOrNot')
    timeLine = document.querySelector('.timeLine')
    isOfficialQcm = document.querySelectorAll('.isOfficialQcm')

    console.log(isOfficialQcms)
    lineXManagment()
    lineYManagment()




});