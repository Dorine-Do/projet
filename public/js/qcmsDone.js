document.addEventListener("DOMContentLoaded", (event) => {
    let select = document.getElementById('select-module');

    function getSelectValue(){
        let selectedValue = select.options[select.selectedIndex].text
        let module = document.querySelector('#module')
        module.innerHTML = "Module : " + selectedValue
    }



    select.addEventListener('change', getSelectValue)

})
