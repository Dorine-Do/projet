let roleUpdaterModale, updateRoleBtn, editRoleBtns, roleUpdaterModalCloseBtn, roleChoice, usersList;

function openModale(e)
{
    roleUpdaterModale.classList.remove('hideRoleUpdaterModale');
    updateRoleBtn.dataset.user = this.dataset.user;
    updateRoleBtn.dataset.role = this.dataset.role;
}

function closeModale()
{
    roleUpdaterModale.classList.add('hideRoleUpdaterModale');
}

async function validateChange()
{
    await fetch( 'update-user/'+ this.dataset.user +'/' + this.dataset.role, {
        method: 'GET'
    } ).then( response => response.json() )
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
    document.querySelector('#tableToSort tr button[data-user="'+ user.id +'"]').closest('tr').querySelector('td:nth-of-type(3)').innerHTML = roleToDisplay;

    closeModale();
}

function handleRoleChange()
{
    updateRoleBtn.dataset.role = this.value;
}

document.addEventListener('DOMContentLoaded', function(){

    editRoleBtns = document.querySelectorAll('.editRoleBtn');
    roleUpdaterModale = document.querySelector('#roleUpdaterModale');
    roleUpdaterModalCloseBtn = document.querySelector('#roleUpdaterModalCloseBtn');
    updateRoleBtn = document.querySelector('#updateRoleBtn');
    roleChoice = document.querySelector('#roleChoice');
    usersList = document.querySelector('#tableToSort tbody')

    for( let btn = 0; btn < editRoleBtns.length; btn++ )
    {
        editRoleBtns[btn].addEventListener('click', openModale);
    }

    roleUpdaterModalCloseBtn.addEventListener('click', closeModale);
    updateRoleBtn.addEventListener('click', validateChange);
    roleChoice.addEventListener('click', handleRoleChange);
});