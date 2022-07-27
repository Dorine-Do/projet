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

  let liSpan = document.querySelector(".list_questions li span ");
  let ulListQuestions = document.querySelector(".list_questions ");
  let liAll = document.querySelectorAll(".list_questions li  ");
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

  for (let forBtn = 0; forBtn <= btnNav.length; forBtn++) {
    btnNav[forBtn].addEventListener("click", function (e) {
      console.log(e.target, "ici");
    });
    // btnNav[forBtn].style.border = "1px solid red";
  }
  // boucle span

  for (let numQuestion = 0; numQuestion < liAll.length; numQuestion++) {
    liSpan[numQuestion].innerHTML = 1 + numQuestion;
  }

  //   console.log(btnNav, "ici");
  //   btnNav.children.addEventListener("click", function (e) {
  //     console.log((e.target.innerHTML = "coucou"));
  //   });
  //   console.log(liSpan);
  //   ulListQuestions.addEventListener("click", function () {
  //     console.log(arrayListQuestions, "la");
  //   });
};
