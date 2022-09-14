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
            console.log(data)
            console.log('stop')
            displayStudents(data)
        })
}

function getQcmsDoneByStudentFromAjax(sessionId, moduleId, studentId){
    fetch( 'dashboard/' + sessionId + '/' + moduleId + '/' + studentId, {method: 'GET'} )
        .then((response) => response.json())
        .then((data) => {
            console.log(data)
            displayQcmsDone(data)
        })
}


//Display
function displayModules(data){
    levelDiv.style.display = 'block'
    console.log(data)
    selectModule = document.getElementById('module-choice')

    data.forEach( module => {
        let option = document.createElement('option')
        option.innerHTML = module['name']
        option.value = module['id']

        selectModule.addEventListener('change', (e) => {
            studentsDiv.style.display = 'block'

            if (moduleId !== e.target.value){
                ulListStudents = studentsDiv.querySelector('.ulListStudents')
                moduleId = e.target.value
                getStudentsByModuleFromAjax(sessionId, moduleId)
            }
        })

        selectModule.append(option)
    } )
}

function displayStudents(data){
    ulListStudents.innerHTML = ''
    data.forEach( student => {
        let div = createElementSimple('div', 'divStudentData')
        let liStudentData = createElementSimple('li', 'liStudentData')

        let checkbox = document.createElement('input')
        checkbox.classList.add('checkboxStudent')
        checkbox.setAttribute('type', `checkbox`)
        checkbox.setAttribute('name', 'student')
        checkbox.setAttribute('id', `student${student.id}`)
        checkbox.setAttribute('value', `${student.id}`)
        checkbox.addEventListener('click', (e) => {
            getQcmsDoneByStudentFromAjax(sessionId, moduleId, e.target.value)
        })

        let label = createElementSimple('label', 'labelStudent', `${student.firstName} ${student.lastName}`)
        label.setAttribute('for', `student${student.id}`)

        let input = document.createElement('input')
        input.setAttribute('type', 'hidden')
        input.dataset.level = student.level

        let img = createElementSimple('img', 'imgLevel')
        img = dislayImgLevel(student.level, img, liStudentData)

        liStudentData.append(checkbox, label, input)
        div.append(liStudentData, img)
        ulListStudents.append(div)
    })

    liStudentData = document.querySelectorAll('.liStudentData')
    positionLabelInput(liStudentData)
}

function displayQcmsDone(data){
    qcmsDiv.style.display = 'block'
    ulListQcms = qcmsDiv.querySelector('.ulListQcms')

    data.forEach( qcm => {
        let li = createElementSimple('li', 'liQcmDone')
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
    qcmsDiv = document.querySelector('.Qcms')
    qcmsDiv.style.display = 'none'


    namesLevel = document.querySelectorAll('.nameLevel')
    namesLevel.forEach( input => {
        input.addEventListener('click', (e)=>{
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


    nameSession = document.querySelectorAll('.nameSession')
    nameSession.forEach( input => {
        input.addEventListener('click', (e)=>{
            if (sessionId !== e.target.value){
                sessionId = e.target.value
                getModuleBySessionFromAjax(sessionId)
            }
        })
    } )




});