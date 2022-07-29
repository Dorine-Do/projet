console.log("hello");
document.addEventListener("DOMContentLoaded", () => {
  console.log("hi");
  let div_proposals = document.querySelectorAll(".divProposals");
  console.log(div_proposals);
  div_proposals.forEach((div) => {
    div.style.display = "none";
  });

  let p_prop;
  let chevrons = document.querySelectorAll(".imgChevron");

  chevrons.forEach((chevron) => {
    chevron.addEventListener("click", (e) => {
      let div_question = e.target.parentElement.parentElement.parentElement;
      let div_js = div_question.querySelector(".divJs");
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

  ////////////////////

  let inputQcm = document.querySelectorAll(".li_btn_qcm input");
  //   let questions = document.querySelectorAll(".list_questions li");
  let forBtnQcm;
  console.log(questions);

  for (forBtnQcm = 0; forBtnQcm < inputQcm.length; forBtnQcm++) {
    console.log(inputQcm[forBtnQcm]);
    inputQcm[forBtnQcm].addEventListener("click", function (e) {
      console.log(e.target);
    });
  }
});
