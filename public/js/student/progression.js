let lineY, lineX, passOrNot, passOrNots, timeLine, containerTimeLine, diffLeft, left, groupElementTimeLine = null;

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

function resizeSup420(){

    // timeline
    timeLine.style.flexDirection = 'row'

    let leftLastElement = timeLine.lastElementChild.getBoundingClientRect().x / 16
    let leftFirstElement = timeLine.firstElementChild.getBoundingClientRect().x / 16

    // Élement des div (qcm) => rond rose
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

    // Élement de la time line ( p(badge) + div(qcm => isOfficialQcm) )
    groupElementTimeLine = document.querySelectorAll('.groupElementTimeLine')
    left = groupElementTimeLine[0].getBoundingClientRect().left
    // Convertion px to em
    left = left/16

    for (let i =0; i < groupElementTimeLine.length; i++){

        if ( groupElementTimeLine[i].tagName === 'DIV' ){
            groupElementTimeLine[i].style.top = 0
        }else{
            groupElementTimeLine[i].style.top = 'inherit'
        }

        if (i !== 0) {
            if (i === 1) {
                groupElementTimeLine[i].style.left = left - 7 + 'em'
                left = left - 7
            } else if (i !== 0) {
                groupElementTimeLine[i].style.left = left - 7 + 'em'
                left = left - 7
            }
        }
    }

    // timeline
    diffLeft = leftLastElement - leftFirstElement
    timeLine.style.width = diffLeft - 10 + 'em'

    // lineX et lineY
    lineXManagment()
    lineYManagment()
}

function resize420() {
    // timeline
    timeLine.style.display = 'flex'
    timeLine.style.flexDirection = 'column'

    // Élement des div (qcm) => rond rose
    let passOrNots = document.querySelectorAll('.passOrNot')
    passOrNots.forEach( (p,key) => {
        p.style.marginBottom = 2.8 + 'em'
        p.style.marginTop = 0
        if ((key+1) % 2 === 0){
            p.parentNode.classList.add('isOfficialQcmOdd')
        }
    } )

    // Élement de la time line ( p(badge) + div(qcm => isOfficialQcm) )
    let topP = 0;
    groupElementTimeLine = document.querySelectorAll('.groupElementTimeLine')
    for (let i =0; i < groupElementTimeLine.length; i++){
        if (i !== 0) {
                groupElementTimeLine[i].style.left = 'inherit'

            if (groupElementTimeLine[i].tagName === 'DIV'){
                groupElementTimeLine[i].style.top = topP + 'em'
                topP = topP - 1
            }else{
                groupElementTimeLine[i].style.top = topP + 1 + 'em'
                topP = topP + 2
            }
        }
    }

    // timeline
    timeLine.style.width = 'auto'

    // lineX
    lineX.style.left = timeLine.getBoundingClientRect().width * 0.55 +  'px'
    lineX.style.marginLeft = 0
    lineX.style.width = 1 + '%'

    //lineY
    lineY = document.querySelectorAll('.lineY')
    lineY.forEach( (line,key) => {
        line.style.left = 'inherit'
        line.style.top = 24 + 'px'
    })
}

document.addEventListener("DOMContentLoaded", (event) => {
    passOrNots = document.querySelectorAll('.passOrNot')
    lineX = document.querySelector('.lineX')
    containerTimeLine = document.querySelector('.timeLine')
    timeLine = document.querySelector('.timeLine')
    passOrNot = document.querySelector('.passOrNot')


    if (window.innerWidth > 450){
        resizeSup420()
    }else if(window.innerWidth <= 450){
        resize420()
    }

    window.addEventListener('resize', function(e) {
        timeLine = document.querySelector('.timeLine')
        if (window.innerWidth > 450){
            resizeSup420()
        }else if(window.innerWidth <= 450){
            resize420()
        }
    }, true);


});