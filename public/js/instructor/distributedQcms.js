let selectSession, selectModule, qcmsContainer, ul, qcmsName, studentsContainer, redirect


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

            qcmsContainer.innerHTML = ''
            data.forEach( qcm => {

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
                name.dataset.anchor = "#students-qcm"
                name.id = qcm.id
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


                document.querySelectorAll('li.button').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();

                        document.querySelector(this.dataset.anchor).scrollIntoView({
                            behavior: 'smooth'
                        });
                    });
                });

            })
        })
}

function fetchStudents(e){
    fetch('distributed_students/' + this.dataset.qcm, {method: 'GET'})
        .then((response) => response.json())
        .then((studentsResults) => {
            console.log("studentsResults" ,studentsResults)
                studentsContainer.innerHTML = ''
                if (typeof studentsResults === 'string'){
                    let p = document.createElement('p')
                    p.classList.add('noStudent')
                    p.innerHTML = "Aucun étudiant n'a encore de note sur ce QCM"
                    studentsContainer.append(p)
                    window.scrollTo(0,document.body.scrollHeight);
                }else{
                    let ulStudent = document.createElement('ul')
                    studentsResults.forEach( studentResult => {

                        let li = document.createElement('li')
                        let name = document.createElement('p')
                        let img = document.createElement('img')

                        if (studentResult.result !== null)
                        {
                            li.dataset.resultid = studentResult.result.id
                        }

                        li.className = 'liStudent'
                        ulStudent.className = 'studentQcm'
                        name.className = 'firstName'

                        let score = studentResult.result ? studentResult.result.score : 'QCM pas encore effectué'

                        name.innerHTML = studentResult.student.firstName +' '+ studentResult.student.lastName +' : '

                        li.append(name)

                        if(score < 25){
                            img.src = decouvre
                            img.className = 'decouvre'
                            img.dataset.level = 'Découvre'
                            img.addEventListener('mouseenter', mouseEnter);
                            img.addEventListener('mousemove', mouseMouve);
                            img.addEventListener('mouseout', mouseOut);
                            li.append(img)
                        }else if(score >= 25 && score < 50){
                            img.src = explore
                            img.className = 'explore'
                            img.dataset.level = 'Explore'
                            img.addEventListener('mouseenter', mouseEnter);
                            img.addEventListener('mousemove', mouseMouve);
                            img.addEventListener('mouseout', mouseOut);
                            li.append(img)
                        }else if(score >= 50 && score < 75){
                            img.src = maitrise
                            img.className = 'maitrise'
                            img.dataset.level = 'Maitrise'
                            img.addEventListener('mouseenter', mouseEnter);
                            img.addEventListener('mousemove', mouseMouve);
                            img.addEventListener('mouseout', mouseOut);
                            li.append(img)
                        }else if(score >= 75 && score <= 100){
                            img.src = domine
                            img.className = 'domine'
                            img.dataset.level = 'Domine'
                            img.addEventListener('mouseenter', mouseEnter);
                            img.addEventListener('mousemove', mouseMouve);
                            img.addEventListener('mouseout', mouseOut);
                            li.append(img)
                        }else{
                            name.innerHTML = studentResult.student.firstName +' '+ studentResult.student.lastName +' : Non effectué'
                        }

                        studentsContainer.append(ulStudent);
                        ulStudent.append(li);

                        li.addEventListener('click', function(e){
                            if (this.dataset.resultid){
                                window.location.href = '/student/qcm/correction/' + this.dataset.resultid;
                            }
                    });
                        window.scrollTo(0,document.body.scrollHeight);
                })
            }
        })
}

function showQcmsStudent()
{
    qcmsContainer.style.display = "block";
    studentsContainer.style.display = "block";
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
    studentsContainer = document.getElementById('students-qcm')
    selectSession.addEventListener('change', fetchModules)
    selectModule.addEventListener('change', fetchQcms)
    selectModule.addEventListener('change', showQcmsStudent)
    qcmsName.addEventListener('click', showQcmsStudent)
});