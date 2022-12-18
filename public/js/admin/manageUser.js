let roleUpdaterModale, updateRoleBtn, editRoleBtns, roleUpdaterModalCloseBtn, roleChoice, usersList, userDetailsModale, userDetailsBtns, closeUserDetailsBtn, userDetailsModaleContent;

function openRoleUpdaterModale(e)
{
    roleUpdaterModale.classList.remove('hideModale');
    updateRoleBtn.dataset.user = this.dataset.user;
    updateRoleBtn.dataset.role = this.dataset.role;
}

function closeRoleUpdaterModale()
{
    roleUpdaterModale.classList.add('hideModale');
}

async function validateChange()
{
    await fetch( 'update-user/'+ this.dataset.user +'/' + this.dataset.role, { method: 'GET' } )
        .then( response => response.json() )
        .then( user => refreshUserInTable(user) );
}

function refreshUserInTable( user )
{
    let roleToDisplay = '';
    if( user.roles.includes('ROLE_ADMIN') )
    {
        roleToDisplay = 'Admin';
    }
    else if( user.roles.includes('ROLE_INSTRUCTOR') )
    {
        roleToDisplay = 'Formateur';
    }
    else
    {
        roleToDisplay = 'Etudiant';
    }
    document.querySelector('#tableToSort tr button[data-user="'+ user.id +'"]').closest('tr').querySelector('td:nth-of-type(3)').innerText = roleToDisplay;

    closeRoleUpdaterModale();
}

function handleRoleChange()
{
    updateRoleBtn.dataset.role = this.value;
}

function initRoleUpdater()
{
    editRoleBtns = document.querySelectorAll('.editRoleBtn');
    roleUpdaterModale = document.querySelector('#roleUpdaterModale');
    roleUpdaterModalCloseBtn = document.querySelector('#roleUpdaterModalCloseBtn');
    updateRoleBtn = document.querySelector('#updateRoleBtn');
    roleChoice = document.querySelector('#roleChoice');
    usersList = document.querySelector('#tableToSort tbody')

    for( let btn = 0; btn < editRoleBtns.length; btn++ )
    {
        editRoleBtns[btn].addEventListener('click', openRoleUpdaterModale);
    }

    roleUpdaterModalCloseBtn.addEventListener('click', closeRoleUpdaterModale);
    updateRoleBtn.addEventListener('click', validateChange);
    roleChoice.addEventListener('click', handleRoleChange);
}

async function fetchUserData()
{
    await fetch( 'user-details/'+ this.dataset.user, { method: 'GET' })
        .then(response => response.json())
        .then( data => openUserDetailsModale(data) )
}

function openUserDetailsModale(data)
{
    const { user, currentSession } = data;
    console.log(user, currentSession);
    let roleToDisplay = '';
    if( user.roles.includes('ROLE_ADMIN') )
    {
        roleToDisplay = 'Admin';
    }
    else if( user.roles.includes('ROLE_INSTRUCTOR') )
    {
        roleToDisplay = 'Formateur';
    }
    else
    {
        roleToDisplay = 'Etudiant';
    }

    let createdAt = new Date(user.createdAt);
    let updatedAt = new Date(user.updatedAt);
    let birthdate = new Date(user.birthDate);

    userDetailsModaleContent.innerHTML = `
        <h2>${ user.firstName } ${ user.lastName }</h2>
        <p>
            <b>Rôle :</b> ${ roleToDisplay }
        </p>
        <p>
            <b>Session actuelle : ${ currentSession ? currentSession.name.toUpperCase() : 'N\'est inscrit sur aucune session sur la journée' }</b>
        </p>
        <p>
            <b>Inscription :</b> ${ createdAt.toLocaleDateString() }
        </p>
        <p>
            <b>Dernière mise à jour :</b> ${ updatedAt.toLocaleDateString() }
        </p>
        <h3>Contact et infos</h3>
        <p>
            <b>Email :</b> ${ user.email }
        </p>
        <p>
            <b>Date de naissance :</b> ${ birthdate.toLocaleDateString() }
        </p>
    `;

    userDetailsModale.classList.remove('hideModale');
}

function closeUserDetailsModale()
{
    userDetailsModale.classList.add('hideModale');
    userDetailsModaleContent.innerText = '';
}

function initUserDetails()
{
    userDetailsModale = document.querySelector('#userDetailsModale');
    userDetailsModaleContent = document.querySelector('#userDetailsModaleContent');
    userDetailsBtns = document.querySelectorAll('.userDetailsBtn');
    closeUserDetailsBtn = document.querySelector('#closeUserDetailsBtn');

    for( let btn = 0; btn < userDetailsBtns.length; btn++ )
    {
        userDetailsBtns[btn].addEventListener('click', fetchUserData);
    }

    closeUserDetailsBtn.addEventListener('click', closeUserDetailsModale)
}

document.addEventListener('DOMContentLoaded', function(){

    initRoleUpdater();

    initUserDetails();

});