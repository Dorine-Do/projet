console.log("hello");
document.addEventListener("DOMContentLoaded", () => {
  // console.log("hi");
  let div_proposals = document.querySelectorAll(".divProposals");
  // console.log(div_proposals);
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
        let count = 0
        for (const proposal of proposals) {
          let id = parseInt(e.target.dataset.id);

          if (id === proposal.id_question) {

            let alphabet = ['A','B','C','D','E','F','G','H']

            let end = parseInt(count,10) + 1 // 4 +1 = 5    '4' + 1 = 41
            let begin = count
            let letter = alphabet.slice(begin, end)
            let p = document.createElement('p')
            p.className = 'pLetter'
            p.innerHTML = letter

            p_prop = document.createElement("p");
            p_prop.innerHTML = proposal.wording;
            div_js.append(p,p_prop);
            count++

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
  // let questionsLi = document.querySelector(".list_questions li");
  let questionsLi = document.querySelectorAll(".list_questions li");
  let questionsSpans = document.querySelectorAll(".list_questions li span");
  // let questionsLi = document.querySelector(".list_questions li");
  let questionslist = document.querySelector(".list_questions ");
  let forBtnQcm;
<<<<<<< HEAD

  ////////////////////
  //  NUMEROTATION DES QUESTIONS
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
    document.querySelector(
      ".bloc_link_btn_my_creations button"
    ).style.background = "#93AD6E";
  }

  ///////////////////
  // SELECTION UNIQUE DES BOUTONS QCMS ET DISPLAY DE LA LISTE DES QUESTIONS LIEES
=======
>>>>>>> 71c981c24b4182c23aec0d07b0e4e2a4f58dbca6

  for (forBtnQcm = 0; forBtnQcm < inputQcm.length; forBtnQcm++) {
    inputQcm[forBtnQcm].addEventListener("click", function (e) {
      // let eTarget = this.dataset.id;
      let questionsCache = JSON.parse(this.dataset.questionsCache);

      for (
        let forWording = 0;
        forWording < questionsCache.length;
        forWording++
      ) {
        console.log(
          (questionsLi[forWording].innerHTML = `<span>${forWording + 1}</span>${
            questionsCache[forWording].wording
          }`)
        );
      }

     

      // FAIRE UNE BOUCLE DE MON JSON ? PARSER LA VALEUR ET REMPLACER EN JS LES VALEURS DU LI DU TEMPLATE PAR CELLE CORRESPONDANTE DANS LE CACHE

      for (forBtnQcm = 0; forBtnQcm < inputQcm.length; forBtnQcm++) {
        if (this.dataset.id == inputQcm[forBtnQcm].dataset.id) {
          // questionslist.dataset.id = `${eTarget}`;
          inputQcm[forBtnQcm].classList.add("active_li");
        } else {
          inputQcm[forBtnQcm].classList.remove("active_li");
        }
      }
    });
  }

<<<<<<< HEAD
  // for (const qcm in questionslist.dataset.qcms) {
  //   console.log(questionslist.dataset.qcm);
  // }

  // let test = fetch(location.href);
  // console.log(test);
=======
  ////////////////////






>>>>>>> 71c981c24b4182c23aec0d07b0e4e2a4f58dbca6
});
