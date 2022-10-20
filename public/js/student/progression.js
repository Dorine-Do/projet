let lineY, lineX, passOrNot, passOrNots, timeLine, containerTimeLine, diffLeft, left, topP, divIsOfficials = null;

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

function createInterCaseBagde(levelMaxByModule, type){

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
    if (type === 'left'){
        pBadge.style.left = left - 2 + 'em'
        left = left + 3
    }else{
        pBadge.style.top = '-' + (topP - 1) + 'em'
        topP = topP - 1
    }

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
                        divIsOfficials[i].style.left = left - 7 + 'em'
                        left = left - 7
                        divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                    } else if (i !== 0) {
                        divIsOfficials[i].style.left = left - 7 + 'em'
                        left = left - 7
                        divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                    }
                    if (levelMaxByModule < divIsOfficials[i].dataset.level){
                        levelMaxByModule = divIsOfficials[i].dataset.level
                    }
                }
                // Si il y a un changement de module, afficher le logo YouUp si l'étudiant à réussi à avoir la moyenne au moins
                // une fois aux qcms de fin de semaine.
                else {
                    console.log('pBadgeChange')

                    let pBadge = createInterCaseBagde(levelMaxByModule, 'left')
                    divIsOfficials[i].parentNode.insertBefore(pBadge,divIsOfficials[i])
                    divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                    levelMaxByModule = 0
                    console.log(i)
                    i --
                    console.log(i)
                }
            }
            // Dernière Div (ligne 46 : divIsOfficials.length + 1). Le +1 sert à verifier si le dernier qcm est le dernier
            // du module de celui-ci.
            // Si oui alors une imageBadge doit être afficher.
            else{
                let now = new Date()
                let endDate = new Date(divIsOfficials[i-1].dataset.enddate)
                if (endDate < now){
                    console.log('pBadgeSup')
                    let pBadge = createInterCaseBagde(levelMaxByModule, 'left')
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
    timeLine.style.display = 'flex'
    timeLine.style.flexDirection = 'column'

    let divPrev = null
    let levelMaxByModule = 0;
    topP = 2;
    divIsOfficials = document.querySelectorAll('.isOfficialQcm')
    for (let i =0; i < divIsOfficials.length + 1; i++){
        if (i !== 0) {
            if (i !== divIsOfficials.length){
                // Si il n'y a pas de changement de module
                if (divPrev === divIsOfficials[i].querySelector('.moduleTitle').dataset.id) {
                    if (i !== 0) {
                        divIsOfficials[i].style.top = '-' + topP + 'em'
                        topP = topP + 2
                        divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                        if (i % 2 !== 0){
                            divIsOfficials[i].style.flexDirection = 'row-reverse'
                            divIsOfficials[i].style.alignSelf = 'self-end'
                        }
                    }
                    if (levelMaxByModule < divIsOfficials[i].dataset.level){
                        levelMaxByModule = divIsOfficials[i].dataset.level
                    }
                }
                // Si il y a un changement de module, afficher le logo YouUp si l'étudiant à réussi à avoir la moyenne au moins
                // une fois aux qcms de fin de semaine.
                else {
                    console.log('pBadge')
                    let pBadge = createInterCaseBagde(levelMaxByModule, 'top')
                    divIsOfficials[i].parentNode.insertBefore(pBadge,divIsOfficials[i])
                    divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
                    levelMaxByModule = 0
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
                    let pBadge = createInterCaseBagde(levelMaxByModule, 'top')
                    divIsOfficials[i-1].parentNode.append(pBadge)
                }
            }
        }else {
            divPrev = divIsOfficials[i].querySelector('.moduleTitle').dataset.id
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
        console.log(windowSizeSup420)
        windowSizeSup420()
    }else if(window.innerWidth <= 420){
        console.log(windowSize420)
        windowSize420()
    }

    window.addEventListener('resize', function(e) {
        timeLine = document.querySelector('.timeLine')
        if (window.innerWidth > 420){
            windowSizeSup420()
        }else if(window.innerWidth <= 420){
            windowSize420()
        }
    }, true);


});