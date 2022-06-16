
document.addEventListener("DOMContentLoaded", (event) => {

    let div_proposals = document.querySelectorAll('.div_proposals')
    div_proposals.forEach(div => {
        div.classList.add('display_none')
    })

    let p_prop;
    let chevrons = document.querySelectorAll('.img_chevron')
    // console.log(chevrons)
    chevrons.forEach(chevron=>{

        chevron.addEventListener('click',(e)=>{

            console.log(e) // { 2 : false} // false

            let div_question = e.target.parentElement.parentElement.parentElement
            let div_js = div_question.querySelector('.div_js')
            // console.log(div_js)
            let status = (e.target.dataset.status === 'true') // return false un boolean si status !== 'true' et true si === true
            console.log(status)
            if(status === false ){ // Si fermé alors
                console.log("YOUPI!!!!!!!!!")

                for (const proposal of proposals) {
                    // console.log(proposal.id_question)
                    // console.log(id)
                    let id = parseInt(e.target.dataset.id)
                    if(id === proposal.id_question){
                        console.log("yeah")
                        p_prop = document.createElement('p')
                        p_prop.innerHTML = proposal.wording
                        div_js.append(p_prop);
                    }
                }
                e.target.dataset.status = true; // Chevron ouvert
            }
            else{ // si ouvert alors
                div_js.innerHTML="";
                e.target.dataset.status = false;
            }
        })
    })
});

