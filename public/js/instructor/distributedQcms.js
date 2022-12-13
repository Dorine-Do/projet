let selectSession, selectModule, qcmsContainer, ulQcm, qcmsName, studentsContainer, redirect, divLegend, ulStudent, spinner, spinner2


function fetchModules(e){

    spinner.classList.add("show");
    setTimeout(() => {
        spinner.classList.remove("show");
    }, 5000);

    fetch( 'distributed_qcms/' + e.target.value, {method: 'GET'} )
        .then((response) => response.json())
        .then((data) => {
            selectModule.innerHTML = ''
            let option = document.createElement('option')
            option.innerHTML = 'Sélectionnez un module'
            option.value = 0
            selectModule.append(option)
            data.forEach( module => {
                option = document.createElement('option')
                option.innerHTML = module['name']
                option.value = module['id']
                selectModule.append(option)
            })

            spinner.classList.remove("show");

            document.getElementById('sectionModule').scrollIntoView({
                behavior: 'smooth'
            });

            qcmsContainer.style.display = 'none'
            ulStudent.remove()
            ulQcm.remove()

        })
}

function fetchQcms(e){

    spinner2.classList.add("show");
    setTimeout(() => {
        spinner2.classList.remove("show");
    }, 5000);

    fetch('../qcm-planner/getModuleQcms/' + e.target.value + '/true', {method: 'GET'})
        .then((response) => response.json())
        .then((data) => {

            let qcmsWithoutRetry = data.filter( function (qcm){
                return !qcm.title.includes('Retentative')
            } )

            qcmsContainer.innerHTML = ''
            qcmsContainer.append(divLegend)

            if(qcmsWithoutRetry.length === 0){
                let p = document.createElement('p')
                p.innerHTML = 'Aucun QCM n\'a été trouvé'
                p.style.textAlign = 'center'
                p.style.fontSize = '1.2em'
                p.style.marginTop = '40px'
                qcmsContainer.append(p)
            }

            qcmsWithoutRetry.forEach( qcm => {

                ulQcm = document.createElement('ul')
                let name = document.createElement('li')
                let difficulty = document.createElement('li')
                let isOfficial = document.createElement('li')

                ulQcm.className = 'qcmStudent'
                name.className = 'button'
                isOfficial.className = 'official'
                difficulty.className = 'difficulty'

                name.innerHTML = qcm.title
                name.dataset.qcm = qcm.id
                name.id = qcm.id
                qcmsContainer.append(ulQcm)

                if(qcm.difficulty === 1){
                    difficulty.innerHTML = 'Facile'
                }else if(qcm.difficulty === 2){
                    difficulty.innerHTML = 'Moyen'
                }else{
                    difficulty.innerHTML = 'Difficile'
                }

                if(qcm.isOfficial === true){
                    isOfficial.innerHTML = 'Officiel'
                }else{
                    isOfficial.innerHTML = 'Exerice'
                }

                ulQcm.append(name, isOfficial, difficulty)

                qcmsName = document.getElementsByClassName('button')
                for(let i = 0; i<qcmsName.length; i++){
                    qcmsName[i].addEventListener('click', fetchStudents)
                }

            })

            spinner2.classList.remove("show");

            document.getElementById('sectionQcms').scrollIntoView({
                behavior: 'smooth'
            });
        })
    ulStudent.remove()
    ulStudent.innerHTML = ' '
}

function fetchStudents(e){

    let spinner3 = document.createElement('div')
    spinner3.id = 'spinner3';

    let ulQcm = e.target.parentNode
    ulQcm.insertBefore(spinner3, e.target)

    spinner3.classList.add("show");
    setTimeout(() => {
        spinner3.classList.remove("show");
    }, 5000);

    fetch('distributed_students/' + this.dataset.qcm, {method: 'GET'})
        .then((response) => response.json())
        .then((studentsResults) => {
            studentsContainer.innerHTML = ''
            if (typeof studentsResults === 'string'){
                let p = document.createElement('p')
                p.classList.add('noStudent')
                p.innerHTML = "Aucun étudiant n'a encore de note sur ce QCM"
                studentsContainer.append(p)

                document.getElementById('sectionStudents').scrollIntoView({
                    behavior: 'smooth'
                });

            }else{
                ulStudent = document.createElement('ul')
                studentsResults.forEach( studentResult => {

                    let li = document.createElement('li')
                    let div = document.createElement('div')
                    let container = document.createElement('div')
                    let name = document.createElement('p')
                    let img = document.createElement('img')
                    let date = document.createElement('p')
                    let button = document.createElement('button')

                    li.className = 'liStudent'
                    div.className = 'containerDiv'
                    container.classList = 'containerInfo'
                    ulStudent.className = 'studentQcm'
                    name.className = 'firstName'
                    date.className = 'distributedAt'
                    button.className = 'viewAnswers'

                    let score = studentResult.result ? studentResult.result.score : 'QCM pas encore effectué'

                    name.innerHTML = studentResult.student.firstName +' '+ studentResult.student.lastName +' : '
                    container.append(name)

                    let dateToDisplay = new Date(studentResult.distributedAt)
                    date.innerHTML = dateToDisplay.toLocaleDateString()
                    container.append(date)

                    if(score < 25){
                        img.src = decouvre
                        img.className = 'decouvre'
                        img.dataset.level = 'Découvre'
                    }else if(score >= 25 && score < 50){
                        img.src = explore
                        img.className = 'explore'
                        img.dataset.level = 'Explore'
                    }else if(score >= 50 && score < 75){
                        img.src = maitrise
                        img.className = 'maitrise'
                        img.dataset.level = 'Maitrise'
                    }else if(score >= 75 && score <= 100){
                        img.src = domine
                        img.className = 'domine'
                        img.dataset.level = 'Domine'
                    }

                    img.addEventListener('mouseenter', mouseEnter);
                    img.addEventListener('mousemove', mouseMouve);
                    img.addEventListener('mouseout', mouseOut);

                    li.append(img)
                    div.append(container)
                    if (studentResult.result !== null){
                        button.innerText = 'Voir les réponses'
                        button.dataset.resultid = studentResult.result.id

                        button.addEventListener('click', (e)=>{
                            window.location.href = '/student/qcm/correction/' + e.target.dataset.resultid
                        })
                        div.append(button)
                    }else{
                        let notDone = document.createElement('p')
                        notDone.className = 'notDone'
                        notDone.innerText = 'Non effectué'
                        div.append(notDone)
                    }

                    li.append(div)

                    studentsContainer.append(ulStudent);
                    ulStudent.append(li);

                })

                document.getElementById('sectionStudents').scrollIntoView({
                    behavior: 'smooth'
                });
            }

            spinner3.remove()
        })
}

function showQcmsStudent()
{
    qcmsContainer.style.display = "block";
    studentsContainer.style.display = "block";
    divLegend.style.display = "flex"
}


const mouseEnter = (e) =>{
    let pInfo = document.createElement('p');
    pInfo.style.position = 'absolute'
    pInfo.setAttribute('id', 'infoHover')
    pInfo.classList.add('imgHover')
    pInfo.innerHTML = e.target.dataset.level
    e.target.parentNode.append(pInfo)
    pInfo.style.left = e.pageX + 'px';
    pInfo.style.top = e.pageY + 'px';
}

const mouseMouve = (e) =>{
    let pInfo = document.getElementById('infoHover');
    pInfo.style.left = e.layerX + 'px';
    pInfo.style.top = e.layerY + 'px';
}

const mouseOut = (e) =>{
    let pInfo = document.getElementById('infoHover');
    pInfo.remove()
}



document.addEventListener("DOMContentLoaded", (event) => {
    selectSession = document.getElementById('session-choice')
    selectModule = document.getElementById('module-choice')
    qcmsContainer = document.getElementById('qcms-module')
    divLegend = document.getElementById('div-legend')
    studentsContainer = document.getElementById('students-qcm')
    spinner = document.querySelector('#spinner')
    spinner2 = document.querySelector('#spinner2')

    selectSession.addEventListener('change', fetchModules)
    selectModule.addEventListener('change', fetchQcms)
    selectModule.addEventListener('change', showQcmsStudent)
    qcmsName.addEventListener('click', showQcmsStudent)


});