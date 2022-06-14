
document.addEventListener("DOMContentLoaded", (event) => {
    console.log(proposals)

    let chevrons = document.querySelectorAll('.chevron')
    chevrons.forEach(chevron=>{
        // console.log(chevron)
        chevron.addEventListener('click',(e)=>{
            let id = chevron.dataset.id
            // console.log(id)
            let div_question = e.target.parentElement.parentElement.parentElement
            $div_proposals = div_question.querySelector('.div_proposals')

            for (const proposal in proposals) {
                proposal.forEach(prop=>{
                    if(id === prop.question.id){
                        let p_prop = document.createElement('p')
                        p_prop.innerHTML = prop.wording
                        $div_proposals.append(p_prop);
                    }
                })
            }
        })
    })
});

