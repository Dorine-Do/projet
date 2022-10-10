document.addEventListener("DOMContentLoaded", (event) => {
    let select = document.getElementById('select_module');
    let imgLevel = document.querySelectorAll('.divLevel img')

    function getSelectValue(){
        let selectedValue = select.options[select.selectedIndex].text
        let realise = document.querySelectorAll('.realise')

        realise.forEach( qcm => {
            let moduleQcm = qcm.querySelector('.moduleQcm')
            if (selectedValue === 'Filtrer par module'){
                qcm.style.display = 'flex'
            }else if(moduleQcm.textContent.trim() !== selectedValue){
                qcm.style.display = 'none'
            }else{
                qcm.style.display = 'flex'
            }
        })
    }

    const mouseEnter = (e) =>{
        let pInfo = document.createElement('p');
        pInfo.style.position = 'absolute'
        pInfo.setAttribute('id', 'infoHover')
        pInfo.classList.add('pInfo')
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



    imgLevel.forEach( img =>{
        img.addEventListener('mouseenter', mouseEnter);
        img.addEventListener('mousemove', mouseMouve);
        img.addEventListener('mouseout', mouseOut);
    })
    select.addEventListener('change', getSelectValue)

})
