let sessionStudentsModale, closeSessionStudentsBtn, sessionStudentsModaleContent , showStudentsBtns, sessionModulesModale, closeSessionModulesBtn, sessionModulesModaleContent , showModulesBtns;

async function fetchSessionUsers()
{
    await fetch('session-students/' + this.dataset.session, {method: 'GET'})
        .then( response => response.json() )
        .then( students => displayStudentsModale(students) );
}

function displayStudentsModale(students)
{
    if( students.length == 0 )
    {
        sessionStudentsModaleContent.innerHTML = `<h2>Cette session ne compte aucun élève</h2>`;
    }

    if( students.length > 0 )
    {
        students.forEach( student => {
            sessionStudentsModaleContent.innerHTML += `
                <div>${ student.firstName } ${ student.lastName }</div>
            `;
        })
    }

    showStudentsModale();
}

function showStudentsModale()
{
    sessionStudentsModale.classList.remove('hideModale');
}

function hideStudentsModale()
{
    sessionStudentsModale.classList.add('hideModale');
    sessionStudentsModaleContent.innerHTML = '';
}

function initSessionStudents()
{
    sessionStudentsModale = document.querySelector('#sessionStudentsModale');
    closeSessionStudentsBtn = document.querySelector('#closeSessionStudentsBtn');
    showStudentsBtns = document.querySelectorAll('.showStudentsBtn');
    sessionStudentsModaleContent = document.querySelector('#sessionStudentsModaleContent');

    for( let btn = 0; btn < showStudentsBtns.length; btn++)
    {
        showStudentsBtns[btn].addEventListener('click', fetchSessionUsers);
    }

    closeSessionStudentsBtn.addEventListener('click', hideStudentsModale);
}

//------------- MODULES

async function fetchSessionModules()
{
    await fetch('session-modules/' + this.dataset.session, {method: 'GET'})
        .then( response => response.json() )
        .then( modules => displayModulesModale(modules) );
}

function displayModulesModale(modules)
{
    if( modules.length == 0 )
    {
        sessionModulesModaleContent.innerHTML = `<tr><td colspan="3">Cette session ne compte aucun module</td></tr>`;
    }

    if( modules.length > 0 )
    {
        modules.forEach( module => {
            let startDate = new Date(module.startDate);
            let endDate = new Date(module.endDate);
            let nowDate = new Date();
            let moduleRow = document.createElement('tr');

            if(startDate.getTime() < nowDate.getTime() && endDate.getTime() > nowDate.getTime()){
                moduleRow.classList.add('currentModule')
            }

            moduleRow.innerHTML = `
                <td>${ module.title }</td>
                <td>${ modules.instructors ? module.instructors.forEach( instructor => instructor.firstName + ' ' + instructor.lastName ) : 'Aucun formateur' }</td>
                <td>${ startDate.toLocaleDateString() }</td>
                <td>${ endDate.toLocaleDateString() }</td>
            `;
            sessionModulesModaleContent.append(moduleRow)
        })
    }

    showModulesModale();
}

function showModulesModale()
{
    sessionModulesModale.classList.remove('hideModale');
}

function hideModulesModale()
{
    sessionModulesModale.classList.add('hideModale');
    sessionModulesModaleContent.innerHTML = '';
}

function initSessionModules()
{
    sessionModulesModale = document.querySelector('#sessionModulesModale');
    closeSessionModulesBtn = document.querySelector('#closeSessionModulesBtn');
    showModulesBtns = document.querySelectorAll('.showModulesBtn');
    sessionModulesModaleContent = document.querySelector('#sessionModulesModaleContent table tbody');

    for( let btn = 0; btn < showModulesBtns.length; btn++)
    {
        showModulesBtns[btn].addEventListener('click', fetchSessionModules);
    }

    closeSessionModulesBtn.addEventListener('click', hideModulesModale);
}

//-------------
document.addEventListener('DOMContentLoaded', function(){

    initSessionStudents();
    initSessionModules();
});