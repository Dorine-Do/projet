document.addEventListener('DOMContentLoaded', function() {

    let choice = document.querySelector('.choice')

    if (window.screen.width <= 450){
        choice.style.position = 'initial'
    }else{
        choice.style.left = (window.screen.width / 2) - (choice.getBoundingClientRect().width/2) + 'px'
    }

    window.addEventListener('resize', (e) => {
        choice.style.left = (window.screen.width / 2) - (choice.getBoundingClientRect().width/2) + 'px'
        if (window.screen.width <= 450){
            choice.style.position = 'initial'
        }
    })




})