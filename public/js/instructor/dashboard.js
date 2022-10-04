let liSession, liLevel, levelDiv, studentsDiv, qcmsDiv = null
let namesLevel, levelChoice, liStudentsData, nameSession, moduleId, sessionId, selectModule, ulListQcms, ulListStudents, liStudentData = null

//Ajax
function getModuleBySessionFromAjax(sessionId){
    fetch( 'dashboard/' + sessionId, {method: 'GET'} )
        .then((response) => response.json())
        .then((data) => {
            displayModules(data)
        });
}

function getStudentsByModuleFromAjax(sessionId, moduleId){
    fetch( 'dashboard/' + sessionId + '/' + moduleId, {method: 'GET'} )
        .then((response) => response.json())
        .then((data) => {
            displayStudents(data)
        })
}

function getQcmsDoneByStudentFromAjax(sessionId, moduleId, studentId){
    fetch( 'dashboard/' + sessionId + '/' + moduleId + '/' + studentId, {method: 'GET'} )
        .then((response) => response.json())
        .then((data) => {
            displayQcmsDone(data)
        })
}


//Display
function displayModules(data){
    levelDiv.style.display = 'block'
    selectModule.innerHTML = ""
    data.forEach( module => {
        let option = document.createElement('option')
        option.innerHTML = module['name']
        option.value = module['id']
        selectModule.append(option)
    } )
}

function displayStudents(data){
    ulListStudents.innerHTML = ''
    data.forEach( student => {
        let div = createElementSimple('div', 'divStudentData')
        let liStudentData = createElementSimple('li', 'liStudentData')

        let radio = document.createElement('input')
        radio.classList.add('checkboxStudent')
        radio.setAttribute('type', `radio`)
        radio.setAttribute('name', 'student')
        radio.setAttribute('id', `student${student.id}`)
        radio.setAttribute('value', `${student.id}`)
        radio.addEventListener('click', (e) => {
            getQcmsDoneByStudentFromAjax(sessionId, moduleId, e.target.value)
        })

        let label = createElementSimple('label', 'labelStudent', `${student.firstName} ${student.lastName}`)
        label.setAttribute('for', `student${student.id}`)

        let input = document.createElement('input')
        input.setAttribute('type', 'hidden')
        input.dataset.level = student.level

        let img = createElementSimple('img', 'imgLevel')
        img = dislayImgLevel(student.level, img, liStudentData)

        liStudentData.append(radio, label, input)
        div.append(liStudentData, img)
        ulListStudents.append(div)
    })

    liStudentData = document.querySelectorAll('.liStudentData')
    positionLabelInput(liStudentData)
}

function displayQcmsDone(data){
    qcmsDiv.style.display = 'block'
    ulListQcms = qcmsDiv.querySelector('.ulListQcms')
    ulListQcms.innerHTML = ""
    data.forEach( qcm => {
        let li = createElementSimple('li', 'liQcmDone')
        li.addEventListener('click', (e) => {
            document.location.href = `/student/qcm/correction/${qcm.resultId}`
        })

        let pDifficulty = createElementSimple('p', 'pDifficulty')
        if (qcm.difficulty === 1)
        {
            pDifficulty.innerHTML = 'Facile'
        }
        else if (qcm.difficulty === 2)
        {
            pDifficulty.innerHTML = 'Moyen'
        }
        else if (qcm.difficulty === 3)
        {
            pDifficulty.innerHTML = 'Difficile'
        }

        let pIsOfficial = createElementSimple('p', 'pIsOfficial')
        if (qcm.isOfficial){
            pIsOfficial.innerHTML = 'Officiel'
        }else{
            pIsOfficial.innerHTML = 'Exercice'
        }

        let pTitle = createElementSimple('p', 'pTitle', qcm.title)

        let pDate = createElementSimple('p', 'pDate')
        let date = qcm.submittedAt.substring(0, 10)
        date = date.replaceAll('-','/')
        pDate.innerHTML = date

        let imgLevel = createElementSimple('img', 'qcmImgLevel')
        imgLevel = dislayImgLevel(qcm.level, imgLevel)

        li.append(pDifficulty, pIsOfficial, pTitle, pDate, imgLevel)
        ulListQcms.append(li)
    })
}


//Autres
function createElementSimple(elementName,className, textContent = null){
    let createdElement = document.createElement(elementName)
    createdElement.classList.add(className)
    if (textContent !== null){
        createdElement.innerHTML = textContent
    }
    return createdElement
}

function dislayImgLevel(level, img,  parent = null){
    if( level === 1 )
    {
        img.setAttribute('alt', 'Graine avec petit pousse')
        img.setAttribute('src', decouvre)
        if ( parent === null){
            img.setAttribute('id', 'ImgDecouvre')
        }
    }
    else if( level === 2 )
    {
        img.setAttribute('alt', 'Jeune arbre')
        img.setAttribute('src', explore)
    }
    else if( level === 3 )
    {
        img.setAttribute('alt', 'arbre adulte')
        img.setAttribute('src', maitrise)
    }
    else if( level === 4 )
    {
        img.setAttribute('alt', 'arbre adulte fleuri')
        img.setAttribute('src', domine)
    }

    return img
}

function positionLabelInput(parent){
    parent.forEach( element => {

        let label = element.querySelector('label')
        let input = element.querySelector('input')

        let widthInput = input.getBoundingClientRect().width
        let heightInput = input.getBoundingClientRect().height

        let widthLabel = label.getBoundingClientRect().width
        let heightLabel = label.getBoundingClientRect().height

        label.style.top = ((heightInput/2)-(heightLabel/2)) + 'px'

        if (widthInput < widthLabel){
            input.style.width = (widthLabel + 10) + 'px'
        }else {
            label.style.width = (widthInput-10) + 'px'
        }

        if (element.parentNode.querySelector('img')){
            let img = element.parentNode.querySelector('img')
            img.style.height = heightInput + 25 + 'px'
            element.parentNode.style.height = heightInput + 'px'
            input.style.width = element.getBoundingClientRect().width + 'px'
        }
    })
}


document.addEventListener("DOMContentLoaded", (event) => {

    // Position des label par rapport a leur input
    liSession = document.querySelectorAll('.liSession')
    positionLabelInput(liSession)
    liLevel = document.querySelectorAll('.liLevel')
    positionLabelInput(liLevel)

    levelDiv = document.querySelector('.level')
    levelDiv.style.display = 'none'
    studentsDiv = document.querySelector('.students')
    studentsDiv.style.display = 'none'
    qcmsDiv = document.querySelector('.qcms')
    qcmsDiv.style.display = 'none'

    // Display block or none (par rapport à la séléction des levels au click)
    namesLevel = document.querySelectorAll('.nameLevel')
    namesLevel.forEach( input => {
        input.addEventListener('click', (e)=>{
            qcmsDiv = document.querySelector('.qcms')
            console.log(qcmsDiv.style.display)
            if (qcmsDiv.style.display === 'block'){
                qcmsDiv.style.display = "none"
                ulListQcms.innerHTML = ""
            }
            liStudentsData = document.querySelectorAll('.liStudentData')
            liStudentsData.forEach( li => {
               let level = li.lastChild.dataset.level
                if(levelChoice !== level && e.target.value === '0'){
                    li.parentNode.style.display = 'flex'
                }else if(levelChoice !== level && level.toString() !== e.target.value){
                    li.parentNode.style.display = 'none'
                }else{
                    li.parentNode.style.display = 'flex'
                }
                levelChoice = level;
            })
        })
    })

    // Display module par rapport à la session séléctionnée
    nameSession = document.querySelectorAll('.nameSession')
    nameSession.forEach( input => {
        input.addEventListener('click', (e)=>{
            if (sessionId !== e.target.value){
                sessionId = e.target.value
                getModuleBySessionFromAjax(sessionId)
            }
        })
    } )

    // Display student par rapport au module séléctioné
    selectModule = document.getElementById('module-choice')
    selectModule.addEventListener('change', (e) => {
        studentsDiv.style.display = 'block'
        if (moduleId !== e.target.value){
            ulListStudents = studentsDiv.querySelector('.ulListStudents')
            ulListStudents.innerHTML = ""
            moduleId = e.target.value
            getStudentsByModuleFromAjax(sessionId, moduleId)
        }
    })




});