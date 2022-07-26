document.addEventListener("DOMContentLoaded", (event) => {
  // Partie question
  let div_proposals = document.querySelectorAll(".div_proposals");

  div_proposals.forEach((div) => {
    div.classList.add("display_none");
  });

  let p_prop;
  let chevrons = document.querySelectorAll(".img_chevron");

  chevrons.forEach((chevron) => {
    chevron.addEventListener("click", (e) => {
      let div_question = e.target.parentElement.parentElement.parentElement;
      let div_js = div_question.querySelector(".div_js");
      // return false un boolean si status !== 'true' et true si === true
      let status = e.target.dataset.status === "true";

      if (status === false) {
        // Si fermé alors

        for (const proposal of proposals) {
          let id = parseInt(e.target.dataset.id);

          if (id === proposal.id_question) {
            p_prop = document.createElement("p");
            p_prop.innerHTML = proposal.wording;
            div_js.append(p_prop);
          }
        }
        e.target.dataset.status = true; // Chevron ouvert
      } else {
        // si ouvert alors
        div_js.innerHTML = "";
        e.target.dataset.status = false;
      }
    });
  });

  // partie Qcm
  let liSpanAll = document.querySelectorAll(".list_questions li span ");
  let liSpan = document.querySelector(".list_questions li span ");
  let ulListQuestions = document.querySelector(".list_questions ");
  console.log(ulListQuestions);
  console.log(liSpan);

  let arrayListQuestions = Array.from(ulListQuestions.children);
  let numQuestion;

  for (numQuestion = 0; numQuestion <= liSpanAll.length; numQuestion++) {
    liSpanAll[numQuestion].innerHTML = 1 + numQuestion;
    // console.log((liSpan.innerHTML = numQuestion));
    // console.log(numQuestion, "ici");
  }

  ulListQuestions.addEventListener("click", function () {
    console.log(arrayListQuestions);
  });
});


