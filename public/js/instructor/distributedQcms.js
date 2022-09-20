document.addEventListener("DOMContentLoaded", (event) => {

    let select = document.getElementById('session-choice')
    let options = select.querySelectorAll('option')

    options.forEach( option => {
        option.addEventListener('click', (e)=>{

            fetch( 'distributed_qcms/' + e.target.value, {method: 'GET'} )
                .then((response) => response.json())
                .then((data) => {
                    console.log(data)
                    let selectModule = document.getElementById('module-choice')

                    data.forEach( module => {
                        let option = document.createElement('option')
                        option.innerHTML = module['name']
                        option.value = module['id']

                        option.addEventListener('click', (e) => {

                        })


                        selectModule.append(option)
                    } )
                });




        })
    } )




});