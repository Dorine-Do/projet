let selectSession, selectModule, qcmsContainer, ul, qcmsName, studentsContainer


function fetchModules(e){
    fetch( 'distributed_qcms/' + e.target.value, {method: 'GET'} )
        .then((response) => response.json())
        .then((data) => {
            console.log(data)
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
        })
}

function fetchQcms(e){
    fetch('../qcm-planner/getModuleQcms/' + e.target.value, {method: 'GET'})
        .then((response) => response.json())
        .then((data) => {
            console.log(data)
            qcmsContainer.innerHTML = ''
            data.forEach( qcm => {
                console.log(qcm)
                ul = document.createElement('ul')
                let name = document.createElement('li')
                let difficulty = document.createElement('li')
                let isOfficial = document.createElement('li')
                let date = document.createElement('li')
                ul.className = 'qcmStudent'
                name.className = 'button'
                isOfficial.className = 'official'
                difficulty.className = 'difficulty'
                date.className = 'date'

                name.innerHTML = qcm.title
                name.dataset.qcm = qcm.id
                qcmsContainer.append(ul)

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
                    isOfficial.innerHTML = 'Entrainement'
                }

                let dateToDisplay = new Date(qcm.updatedAt)
                date.innerHTML = dateToDisplay.toLocaleDateString()
                ul.append(name, date, isOfficial, difficulty)

                qcmsName = document.getElementsByClassName('button')
                for(let i = 0; i<qcmsName.length; i++){
                    qcmsName[i].addEventListener('click', fetchStudents)
                }


            })
        })
}

function fetchStudents(){
    fetch('distributed_students/' + this.dataset.qcm, {method: 'GET'})
        .then((response) => response.json())
        .then((studentsResults) => {
            console.log(studentsResults)
            if(studentsResults === null){
                studentsContainer.innerHTML = "Aucun étudiant n'a effectué ce qcm"
            }else{
                studentsContainer.innerHTML = ''
                studentsResults.forEach( studentResult => {
                    let ulStudent = document.createElement('ul')
                    let name = document.createElement('li')
                    let result = document.createElement('li')
                    let img = document.createElement('img')


                    ulStudent.className = 'studentQcm'
                    name.className = 'firstName'
                    result.className = 'score'

                    let score = studentResult.result ? studentResult.result.score : 'QCM pas encore effectué'

                    name.innerHTML = studentResult.student.firstName +' '+ studentResult.student.lastName +' : '

                    if(score < 25){
                        img.src = decouvre
                        img.className = 'decouvre'
                        result.append(img)
                    }else if(score >= 25 && score < 50){
                        img.src = explore
                        img.className = 'explore'
                        result.append(img)
                    }else if(score >= 50 && score < 75){
                        img.src = maitrise
                        img.className = 'maitrise'
                        result.append(img)
                    }else if(score >= 75 && score <= 100){
                        img.src = domine
                        img.className = 'domine'
                        result.append(img)
                    }else{
                        result.innerHTML = 'Non effectué'
                    }

                    studentsContainer.append(ulStudent)


                    ulStudent.append(name, result)
                })
            }
        })
}

function showQcmsStudent()
{
    qcmsContainer.style.display = "block";
    studentsContainer.style.display = "block";
}


document.addEventListener("DOMContentLoaded", (event) => {
    selectSession = document.getElementById('session-choice')
    selectModule = document.getElementById('module-choice')
    qcmsContainer = document.getElementById('qcms-module')
    studentsContainer = document.getElementById('students-qcm')
    selectSession.addEventListener('change', fetchModules)
    selectModule.addEventListener('change', fetchQcms)
    selectModule.addEventListener('change', showQcmsStudent)
    qcmsName.addEventListener('click', showQcmsStudent)
});