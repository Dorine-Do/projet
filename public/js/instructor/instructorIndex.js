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
  // DECLARATION DE VARIABLE

  let inputQcm = document.querySelectorAll(".li_btn_qcm ");
  let questions = document.querySelectorAll(".list_questions li");
  let questionsSpans = document.querySelectorAll(".qcm_question_number");
  // let questionsLi = document.querySelector(".list_questions li");
  let questionslist = document.querySelector(".list_questions ");
  let forBtnQcm;

  ////////////////////
  //  NUMEROTATION DES QUESTION
  for (
    let numQuestion = 0;
    numQuestion < questionsSpans.length;
    numQuestion++
  ) {
    questionsSpans[numQuestion].innerHTML = 1 + numQuestion;
  }

  /////////////////////
  //  TEST url
  if (window.location.href.includes(location.pathname)) {
    console.log(
      (document.querySelector(
        ".bloc_link_btn_my_creations button"
      ).style.background = "#93AD6E")
    );
  }

  ///////////////////
  // SELECTION UNIQUE DES BOUTONS QCMS ET DISPLAY DE LA LISTE DES QUESTIONS LIEES

  for (forBtnQcm = 0; forBtnQcm < inputQcm.length; forBtnQcm++) {
    inputQcm[forBtnQcm].addEventListener("click", function (e) {
      let eTarget = e.target.dataset.id;

      for (forBtnQcm = 0; forBtnQcm < inputQcm.length; forBtnQcm++) {
        if (e.target.dataset.id == inputQcm[forBtnQcm].dataset.id) {
          console.log("yes");
          questionslist.dataset.id = `${eTarget}`;
          inputQcm[forBtnQcm].classList.add("active_li");
        } else {
          inputQcm[forBtnQcm].classList.remove("active_li");
          console.log("no");

          // questionslist.style.display = "none";

          // if (e.target.dataset.id !== questionslist.dataset.id) {
          //   questionslist.style.background = "blue";
          //   questionslist.style.display = "none";
          // }
        }
      }
    });
  }
  for (const qcm in questionslist.dataset.qcms) {
    console.log(questionslist.dataset.qcm);
  }

  // let test = fetch(location.href);
  // console.log(test);
});
