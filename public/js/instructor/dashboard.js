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
    let liSession = document.querySelectorAll('.liSession')
    positionLabelInput(liSession)
    let liLevel = document.querySelectorAll('.liLevel')
    positionLabelInput(liLevel)

    let levelDiv = document.querySelector('.level')
    levelDiv.style.display = 'none'
    let studentsDiv = document.querySelector('.students')
    studentsDiv.style.display = 'none'


    let namesLevel = document.querySelectorAll('.nameLevel')
    let levelChoice;
    namesLevel.forEach( input => {
        input.addEventListener('click', (e)=>{
            let liStudentsData = document.querySelectorAll('.liStudentData')
            liStudentsData.forEach( li => {
                console.log(li)
               let level = li.lastChild.dataset.level
                console.log(level)

                if(levelChoice === e.target.value || e.target.value.include(levelChoice)){
                    console.log('ok')
                }
                    // let ulListStudents = studentsDiv.querySelector('.ulListStudents')

            })
        })
    })




    let inputs = document.querySelectorAll('.nameSession')
    let students;
    let moduleId
    let sessionId = null
    inputs.forEach( input => {
        input.addEventListener('click', (e)=>{
            if (sessionId !== e.target.value){
                sessionId = e.target.value
                fetch( 'dashboard/' + sessionId, {method: 'GET'} )
                    .then((response) => response.json())
                    .then((data) => {
                        levelDiv.style.display = 'block'
                        console.log(data)
                        let selectModule = document.getElementById('module-choice')

                        data.forEach( module => {
                            let option = document.createElement('option')
                            option.innerHTML = module['name']
                            option.value = module['id']

                            option.addEventListener('click', (e) => {
                                studentsDiv.style.display = 'block'

                                if (moduleId !== e.target.value){
                                    let ulListStudents = studentsDiv.querySelector('.ulListStudents')
                                    moduleId = e.target.value

                                    fetch( 'dashboard/' + sessionId + '/' + moduleId, {method: 'GET'} )
                                        .then((response) => response.json())
                                        .then((data) => {
                                            console.log(data)
                                            console.log('stop')
                                            ulListStudents.innerHTML = ''
                                            data.forEach( student => {
                                                let div =  document.createElement('div')
                                                div.classList.add('divStudentData')
                                                let li = document.createElement('li')
                                                li.classList.add('liStudentData')

                                                let checkbox = document.createElement('input')
                                                checkbox.classList.add('checkboxStudent')
                                                checkbox.setAttribute('type', `checkbox`)
                                                checkbox.setAttribute('name', 'student')
                                                checkbox.setAttribute('id', `student${student.id}`)
                                                checkbox.setAttribute('value', `${student.id}`)

                                                let label = document.createElement('label')
                                                label.innerHTML = `${student.firstName} ${student.lastName}`
                                                label.classList.add('labelStudent')
                                                label.setAttribute('for', `student${student.id}`)

                                                let input = document.createElement('input')
                                                input.setAttribute('type', 'hidden')
                                                input.dataset.level =  student.level

                                                let img = document.createElement('img')
                                                img.classList.add('imgLevel')

                                                if( student.level === 1 )
                                                {
                                                    img.setAttribute('alt', 'Graine avec petit pousse')
                                                    img.setAttribute('src', decouvre)
                                                }
                                                else if( student.level === 2 )
                                                {
                                                    img.setAttribute('alt', 'Jeune arbre')
                                                    img.setAttribute('src', explore)
                                                }
                                                else if( student.level === 3 )
                                                {
                                                    img.setAttribute('alt', 'arbre adulte')
                                                    img.setAttribute('src', maitrise)
                                                }
                                                else if( student.level === 4 )
                                                {
                                                    img.setAttribute('alt', 'arbre adulte fleuri')
                                                    img.setAttribute('src', domine)
                                                }
                                                li.append(checkbox, label, input)
                                                div.append(li, img)
                                                ulListStudents.append(div)
                                            })

                                            let liStudentData = document.querySelectorAll('.liStudentData')
                                            positionLabelInput(liStudentData)


                                        })
                                }
                            })

                            selectModule.append(option)
                        } )
                    });
            }
        })
    } )




});