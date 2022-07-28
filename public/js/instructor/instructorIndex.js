window.onload = function (event) {
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

  ////////////////////////////////////////////////////////partie Qcm ////////////////////////////////////////////////

  let liSpan = document.querySelector(".list_questions li  ");
  let ulListQuestions = document.querySelector(".list_questions ");
  let liAll = document.querySelectorAll(".list_questions li span ");
  let arrayListQuestions = Array.from(ulListQuestions.children);
  let listBtnNav = document.querySelector(".bloc_btns_nav");

  //   test url
  if (window.location.href.includes(location.pathname)) {
    console.log(
      (document.querySelector(
        ".bloc_link_btn_my_creations button"
      ).style.background = "#93AD6E")
    );
  }

  let btnNav = document.querySelectorAll(".bloc_btns_nav div button");
  console.log(btnNav);

  //a revoir////////////////////////////

  //   for (let forBtn = 0; forBtn < btnNav.length; forBtn++) {
  //     btnNav[forBtn].addEventListener("click", function (e) {
  //       e.target.style.background = "#93AD6E";
  //       //   for (let i = 0; i < e.target.length; i - 1) {
  //       //     e.target[i].style.background = "#93AD6E";

  //       //     // if (window.location.href.includes(location.pathname)) {
  //       //     //   e.target[i].style.background = "#93AD6E";
  //       //     //   console.log(e.target, "hi");
  //       //     // }
  //       //   }
  //     });

  // btn vert en fonction de l' url
  if (location.pathname.includes("2")) {
    console.log("yes");
  } else {
    console.log("false");
  }

  //Affichage d'une liste en fonction de l'id

  let liQcm = document.querySelectorAll(".list_btn_qcm li");
  let btnQcmSelected = document.querySelector(".bloc_btn_selected_qcm a");
  let href = window.location.href;
  for (let numBtnQcm = 0; numBtnQcm < liQcm.length; numBtnQcm++) {
    console.log(liQcm[numBtnQcm]);

    liQcm[numBtnQcm].addEventListener("click", function (e) {
      let idBtnQcm = e.target.id;
      btnQcmSelected.setAttribute("id", `${idBtnQcm}`);
      //   temporaire
      btnQcmSelected.href = `https://127.0.0.1:8000/instructor/questions/${idBtnQcm}/`;
      //   remplacer le btn par le a pour le href etdans l 'url faire de l'injectiond e varaiable
    });
  }

  let after = document.querySelector(".list_btn_qcm li::after");
  console.log(after);

  // boucle span

  for (let numQuestion = 0; numQuestion < liAll.length; numQuestion++) {
    liAll[numQuestion].innerHTML = 1 + numQuestion;
  }

  //   voir pour remplacer les li par des imnput car c'est a partir des input qu'on passe des infos tel que le id ou le name
  //   donc au clic des btnqcm il faut que le etarget.id du btnqcm soit egale a l'id qcm posé sur les li alias input
};
