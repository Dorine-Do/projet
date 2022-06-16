
document.addEventListener("DOMContentLoaded", (event) => {
    console.log(proposals)
    $status = false; // Chevron fermé
    let p_prop;
    let chevrons = document.querySelectorAll('.img_chevron')
    console.log(chevrons)
    chevrons.forEach(chevron=>{
        chevron.addEventListener('click',(e)=>{
            if($status === false){ // Si fermé alors
                let id = chevron.dataset.id
                let div_question = e.target.parentElement.parentElement.parentElement
                $div_proposals = div_question.querySelector('.div_proposals')
                for (const proposal of proposals) {
                    if(id == proposal.id_question){
                        p_prop = document.createElement('p')
                        p_prop.innerHTML = proposal.wording
                        $div_proposals.append(p_prop);
                    }
                }
                $status = true; // Chevron ouvert
            }else{ // si ouvert alors
                $div_proposals.innerHTML="";
                $status = false;
            }
        })
    })
});

