let lineY, lineX, passOrNot, passOrNots, timeLine, containerTimeLine, diffLeft, left, divIsOfficials = null;

function lineYManagment(){
    lineY = document.querySelectorAll('.lineY')
    lineY.forEach( (line,key) => {
        let parent = line.parentNode
        let parentWidth = parent.getBoundingClientRect().width
        line.style.left = parentWidth/2 + 'px'

        if ( (key+1) % 2 === 0 ){
            line.style.top = 37 + 'px'
        }else {
            line.style.top = 67 + 'px'
        }
    } )
}

function lineXManagment(){
    let timelineX = containerTimeLine.getBoundingClientRect().x
    let passOrNotX = passOrNot.getBoundingClientRect().x
    let diff = passOrNotX - timelineX
    lineX.style.marginLeft = diff + 20 + 'px'
    lineX.style.width = diffLeft - 20 + 'em'
}

function createInterCaseBagde(levelMaxByModule){

    let pBadge = document.createElement('p')
    pBadge.classList.add('pBadge')
    if ( levelMaxByModule > 2 ){
        let imgYouUp = document.createElement('img')
        imgYouUp.setAttribute('src', logo)
        imgYouUp.setAttribute('alt', 'Logo YouUp')
        imgYouUp.classList.add('imgYouUp')
        pBadge.append(imgYouUp)
    }else{
        let imgYouUp = document.createElement('img')
        imgYouUp.setAttribute('src', wrong)
        imgYouUp.setAttribute('alt', 'Croix')
        imgYouUp.classList.add('wrong')
        pBadge.append(imgYouUp)
    }
    pBadge.style.left = left - 2 + 'em'
    left = left + 3
    return pBadge
}

function divIsOfficialManagment(){

    let passOrNots = document.querySelectorAll('.passOrNot')
    passOrNots.forEach( (p,key) => {
        if ((key+1) % 2 === 0){
            p.style.marginTop = 0
            p.style.marginBottom = 2 + 'em'
            p.parentNode.classList.add('isOfficialQcmOdd')
        }else{
            p.style.marginBottom = 0
            p.style.marginTop= 1.5 + 'em'
        }
    } )

    // Positionnement des div isOfficialQcm
    divIsOfficials = document.querySelectorAll('.isOfficialQcm')
    left = divIsOfficials[0].getBoundingClientRect().left
    // Convertion px to em
    left = left/16
    let divPrev = null
    let levelMaxByModule = 0;
    for (let i =0; i < divIsOfficials.length + 1; i++){
        if (i !== 0) {
            if (i !== divIsOfficials.length){
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
                    console.log(divIsOfficials[i])
                    let pBadge = createInterCaseBagde(levelMaxByModule)
                    divIsOfficials[i].parentNode.insertBefore(pBadge,divIsOfficials[i])
                    divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                    i --
                }
            }
            // Dernière Div (ligne 46 : divIsOfficials.length + 1). Le +1 sert à verifier si le dernier qcm est le dernier
            // du module de celui-ci.
            // Si oui alors une imageBadge doit être afficher.
            else{
                let now = new Date()
                let endDate = new Date(divIsOfficials[i-1].dataset.enddate)
                if (endDate < now){
                    let pBadge = createInterCaseBagde(levelMaxByModule,left)
                    divIsOfficials[i-1].parentNode.append(pBadge)
                }
            }
        }
        // Premiere div
        else {
            left = divIsOfficials[i].getBoundingClientRect().left
            left = left/16
            divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
            levelMaxByModule = divIsOfficials[i].dataset.level
        }
    }

}

function windowSize420(){
    passOrNots.forEach( p => {
        p.style.marginBottom = 2 + 'em'
        p.style.marginTop = 0
    } )
    lineX.style.height = timeLine.getBoundingClientRect().height + 'px'
    let divPrev = null
    let levelMaxByModule = 0;
    divIsOfficials = document.querySelectorAll('.isOfficialQcm')
    console.log(document.querySelectorAll('.isOfficialQcm'))
    for (let i =0; i < divIsOfficials.length + 1; i++){
        if (i !== 0) {
            if (i !== divIsOfficials.length){
                // Si il n'y a pas de changement de module
                if (divPrev === divIsOfficials[i].querySelector('.moduleTitle').dataset.id) {
                    if (i === 1) {
                        divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                    } else if (i !== 0) {
                        divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                    }
                    if (levelMaxByModule < divIsOfficials[i]){
                        levelMaxByModule = divIsOfficials[i].dataset.level
                    }
                }
                    // Si il y a un changement de module, afficher le logo YouUp si l'étudiant à réussi à avoir la moyenne au moins
                // une fois aux qcms de fin de semaine.
                else {
                    console.log(divIsOfficials[i])
                    let pBadge = createInterCaseBagde(levelMaxByModule)
                    divIsOfficials[i].parentNode.insertBefore(pBadge,divIsOfficials[i])
                    divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                    i --
                }
            }
                // Dernière Div (ligne 46 : divIsOfficials.length + 1). Le +1 sert à verifier si le dernier qcm est le dernier
                // du module de celui-ci.
            // Si oui alors une imageBadge doit être afficher.
            else{
                let now = new Date()
                let endDate = new Date(divIsOfficials[i-1].dataset.enddate)
                if (endDate < now){
                    let pBadge = createInterCaseBagde(levelMaxByModule,left)
                    divIsOfficials[i-1].parentNode.append(pBadge)
                }
            }
        }
    }
}

function windowSizeSup420(){
    let leftLastElement = timeLine.lastElementChild.getBoundingClientRect().x / 16
    let leftFirstElement = timeLine.firstElementChild.getBoundingClientRect().x / 16
    divIsOfficialManagment()
    diffLeft = leftLastElement - leftFirstElement
    timeLine.style.width = diffLeft - 10 + 'em'
    lineXManagment()
    lineYManagment()
}

document.addEventListener("DOMContentLoaded", (event) => {
    passOrNots = document.querySelectorAll('.passOrNot')
    lineX = document.querySelector('.lineX')
    containerTimeLine = document.querySelector('.timeLine')
    timeLine = document.querySelector('.timeLine')
    passOrNot = document.querySelector('.passOrNot')


    if (window.innerWidth > 420){
        windowSizeSup420()
    }else if(window.innerWidth <= 420){
        windowSize420()
    }

    window.addEventListener('resize', function(e) {
        console.log(window.innerWidth)
        timeLine = document.querySelector('.timeLine')
        if (window.innerWidth > 420){
            windowSizeSup420()
        }else if(window.innerWidth <= 420){
            windowSize420()
        }
    }, true);


});