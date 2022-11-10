let liSession, liLevel, levelDiv, studentsDiv, qcmsDiv = null
let namesLevel, pNoStudents, liStudentsData, nameSession, moduleId, sessionId, selectModule, ulListQcms, ulListStudents, liStudentData = null

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
    ulListStudents = document.querySelector('.ulListStudents')
    studentsDiv = document.querySelector('.students')
    studentsDiv.style.display = "none"
    ulListStudents.innerHTML = ""

    qcmsDiv = document.querySelector('.qcms')
    ulListQcms = document.querySelector('.ulListQcms')
    qcmsDiv.style.display = "none"
    ulListQcms.innerHTML = ""


    levelDiv.style.display = 'block'

    selectModule.innerHTML = ""

    let option = document.createElement('option')
    option.innerHTML = 'Séléctionner un module'
    selectModule.append(option)

    data.forEach( module => {
        let option = document.createElement('option')
        option.innerHTML = module['name']
        option.value = module['id']
        option.id = 'option-module'
        option.dataset.anchor = '#option-module'
        selectModule.append(option)
    } )

    document.getElementById('section-module').scrollIntoView({
        behavior: 'smooth'
    });
}


function displayStudents(data){
    qcmsDiv.style.display = "none"
    ulListQcms.innerHTML = ""


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
        radio.dataset.anchor = '#qcms-list'
        radio.addEventListener('click', (e) => {
            getQcmsDoneByStudentFromAjax(sessionId, moduleId, e.target.value, e)
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
    if (data.length === 0){
        let div = createElementSimple('p', 'noStudent')
        div.innerHTML = "Aucun étudiant n'a encore de note dans cette session"
        ulListStudents.append(div)
    }

    document.getElementById('section-students').scrollIntoView({
        behavior: 'smooth'
    });

}


function displayQcmsDone(data){
    qcmsDiv.style.display = 'block'
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

        let p = document.createElement('p')
        p.classList.add('pImageLevel')

        p.append(imgLevel)
        li.append(pDifficulty, pIsOfficial, pTitle, pDate, p)
        ulListQcms.append(li)
    })

    document.getElementById('section-qcms').scrollIntoView({
        behavior: 'smooth'
    });
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
        img.dataset.level = 'Découvre'
        if ( parent === null){
            img.setAttribute('id', 'img-decouvre')
        }
    }
    else if( level === 2 )
    {
        img.setAttribute('alt', 'Jeune arbre')
        img.setAttribute('src', explore)
        img.dataset.level = 'Explore'
    }
    else if( level === 3 )
    {
        img.setAttribute('alt', 'arbre adulte')
        img.setAttribute('src', maitrise)
        img.dataset.level = 'Maîtrise'
    }
    else if( level === 4 )
    {
        img.setAttribute('alt', 'arbre adulte fleuri')
        img.setAttribute('src', domine)
        img.dataset.level = 'Domine'
    }

    img.addEventListener('mouseenter', mouseEnter);
    img.addEventListener('mousemove', mouseMouve);
    img.addEventListener('mouseout', mouseOut);

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

        label.style.top = ((heightInput/2)-(heightLabel/2) - 2) + 'px'

        if (element.className === 'liStudentData'){
            label.style.left = 11 + 'px'
        }

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

function showStudentByModules(e){
    studentsDiv.style.display = 'block'
    if (moduleId !== e.target.value){
        qcmsDiv.style.display = 'none';
        ulListStudents.innerHTML = ""
        moduleId = e.target.value
        getStudentsByModuleFromAjax(sessionId, moduleId)
    }
}

function showModulesBySessions(){
    nameSession.forEach( input => {
        input.addEventListener('click', (e)=>{
            if (sessionId !== e.target.value){
                sessionId = e.target.value
                getModuleBySessionFromAjax(sessionId)
            }
        })
    })
}

function displayContact(){
    namesLevel.forEach( input => {
        input.addEventListener('click', (e)=>{
            if (qcmsDiv.style.display === 'block'){
                qcmsDiv.style.display = "none"
                ulListQcms.innerHTML = ""
            }
            pNoStudents.style.display = "none"

            let hasStudent = false

            liStudentsData = document.querySelectorAll('.liStudentData')
            liStudentsData.forEach( li => {

                let level = li.lastChild.dataset.level
                console.log(li)
                console.log(level.toString())
                console.log(e.target.value)
                if ( e.target.value === '0'){
                    li.parentNode.style.display = 'flex'
                    hasStudent = true
                }
                else if(e.target.value === level){
                    li.parentNode.style.display = 'flex'
                    hasStudent = true
                }
                else if(level.toString() !== e.target.value){
                    li.parentNode.style.display = 'none'
                }
            })

            pNoStudents.innerText = "Aucun étudiant n'a ce niveau pour ce module"
            pNoStudents.className = "pNoStudents"
            pNoStudents.style.display = "none"
            ulListStudents.append(pNoStudents)

            if (!hasStudent){
                pNoStudents.style.display = "block"
            }else{
                pNoStudents.style.display = "none"
            }

        })
    })
}

const mouseEnter = (e) =>{
    let pInfo = document.createElement('p');
    pInfo.style.position = 'absolute'
    pInfo.setAttribute('id', 'infoHover')
    pInfo.classList.add('imgHover')
    pInfo.innerHTML = e.target.dataset.level
    e.target.parentNode.append(pInfo)
    console.log(e)
    console.log(e.pageX)
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

    liSession = document.querySelectorAll('.liSession')
    liLevel = document.querySelectorAll('.liLevel')
    nameSession = document.querySelectorAll('.nameSession')
    namesLevel = document.querySelectorAll('.nameLevel')
    levelDiv = document.querySelector('.level')
    studentsDiv = document.querySelector('.students')
    qcmsDiv = document.querySelector('.qcms')
    selectModule = document.getElementById('module-choice')
    ulListStudents = document.querySelector('.ulListStudents')

    pNoStudents = document.createElement('p')

    selectModule.addEventListener('change', showStudentByModules)
    positionLabelInput(liSession)
    positionLabelInput(liLevel)
    levelDiv.style.display = 'none'
    studentsDiv.style.display = 'none'
    qcmsDiv.style.display = 'none'
    displayContact();
    showModulesBySessions();

});