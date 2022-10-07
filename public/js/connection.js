let emailBlock, nextBtn, oneTapBtn, passwordBlock, prevBtn, emailField;

function checkEmail(e)
{
    email3waFormat = "^[A-Za-z0-9._%+-]+@3wa.io$";
    emailFormat = "^[A-Za-z0-9._%+-]+@+[A-Za-z0-9._%+-]+[.]+([A-Za-z0-9]{2,3})$";

    if( e.target.value.includes('@') )
    {
        if( e.target.value.match( email3waFormat ) )
        {
            console.log('email 3wa saisie')
            oneTapBtn.classList.remove('hideConnectionElement');
            if( !nextBtn.classList.contains('hideConnectionElement') )
            {
                nextBtn.classList.add('hideConnectionElement');
            }
        }
        else if( e.target.value.match( emailFormat ) )
        {
            console.log('email pas 3wa saisie');
            nextBtn.classList.remove('hideConnectionElement');
            if( !oneTapBtn.classList.contains('hideConnectionElement') )
            {
                oneTapBtn.classList.add('hideConnectionElement');
            }
        }
        else
        {
            if( !nextBtn.classList.contains('hideConnectionElement') )
            {
                nextBtn.classList.add('hideConnectionElement');
            }
            if( !oneTapBtn.classList.contains('hideConnectionElement') )
            {
                oneTapBtn.classList.add('hideConnectionElement');
            }
            console.log('email invalide')
        }
    }
    else if( e.target.value === '' )
    {
        oneTapBtn.classList.add('hideConnectionElement')
    }
}

function nextStep(e)
{
    e.preventDefault();
    emailBlock.classList.add('hideConnectionElement');
    passwordBlock.classList.remove('hideConnectionElement');
}

function prevStep(e)
{
    e.preventDefault();
    emailBlock.classList.remove('hideConnectionElement');
    passwordBlock.classList.add('hideConnectionElement');
}

function oneTapConnect(e)
{
    e.preventDefault();
}

document.addEventListener('DOMContentLoaded', function(){

    emailBlock = document.querySelector('#login-form .emailBlock');
    nextBtn = document.querySelector('#login-form .nextBtn');
    prevBtn = document.querySelector('#login-form .prevBtn');
    oneTapBtn = document.querySelector('#login-form .oneTapBtn');
    passwordBlock = document.querySelector('#login-form .passwordBlock');
    emailField = document.querySelector('#login-form input[name="_username"]');

    emailField.addEventListener('keyup', checkEmail);
    nextBtn.addEventListener('click', nextStep);
    prevBtn.addEventListener('click', prevStep);
    oneTapBtn.addEventListener('click', oneTapConnect)
});