//ONLOAD ET NON DOM CHARGEMENT 1 FOIS ET NON 2 COMME DOM CAR ERREUR ET PROBLEME D AFFICHAGE
window.onload = function (event) {
  let spanFlashAddIns = document.querySelector(".flash-notice div");
  let addFlashIns = document.querySelector(".flash-notice ");
  let spanFlashQcmPerso = document.querySelector(".flash-notice-qcm-perso div");
  let addFlashQcmPerso = document.querySelector(".flash-notice-qcm-perso ");

  ///////////////////////FLASH MESSAGE
  if (spanFlashAddIns) {
    spanFlashAddIns.addEventListener("click", function () {
      addFlashIns.style.display = "none";
    });
  }
  if (spanFlashQcmPerso) {
    spanFlashQcmPerso.addEventListener("click", function () {
      addFlashQcmPerso.style.display = "none";
    });
  }

  //////////////PROPOSALS
  let div_proposals = document.querySelectorAll(".divProposals");
  div_proposals.forEach((div) => {
    div.style.display = "none";
  });
  ///////////DIV PARENT PROPOSALS
  let divParentProposal = document.querySelectorAll(".blocDivProposal");

  let p_prop;
  let chevrons = document.querySelectorAll(".imgChevron");

  chevrons.forEach((chevron) => {
    chevron.addEventListener("click", (e) => {
      let div_question =
        e.target.parentElement.parentElement.parentElement.parentElement
          .childNodes[5];
      // let div_question =  e.target.parentElement.parentElement.parentElement.parentElement
      div_question.style.display = "block";
      let div_js = div_question.querySelector(".divJs");
      // return false un boolean si status !== 'true' et true si === true
      let status = e.target.dataset.status === "true";

      if (status === false) {
        // Si fermé alors
        let count = 0;
        for (const proposal of proposals) {
          let id = parseInt(e.target.dataset.id);

          if (id === proposal.id_question) {
            let alphabet = ["A", "B", "C", "D", "E", "F", "G", "H"];

            let end = parseInt(count, 10) + 1; // 4 +1 = 5    '4' + 1 = 41
            let begin = count;
            let letter = alphabet.slice(begin, end);
            let p = document.createElement("p");
            p.className = "pLetter";
            p.innerHTML = letter;

            p_prop = document.createElement("div");
            p_prop.innerHTML = proposal.wording;
            p_prop.classList.add("blocContentProposal");

            // console.log(p_prop.insertBefore(p, p_prop.lastElementChild));
            // p_prop.lastElementChild.insertAdjacentElement("beforebegin", p);
            // p_prop.insertBefore(p, p_prop.lastElementChild);
            // console.log(p_prop.lastElementChild, "ici");
            div_js.append(p, p_prop);
            // INSERTION DU PLETTER DEVANT LE PWORDING
            p_prop.insertBefore(p, p_prop.lastElementChild);
            console.log(p_prop.lastElementChild.nodeName);
            // WORDING SANS P
            if (
              p_prop.childNodes[0].nodeName == "#text" &&
              p_prop.lastElementChild.className ==
                p_prop.childNodes[1].className
            ) {
              let wordingText = p_prop.childNodes[0].data;
              p_prop.innerHTML = `<p>${wordingText}</p>`;
              p_prop.insertBefore(p, p_prop.lastElementChild);
            }

            count++;
          }
        }
        e.target.dataset.status = true; // Chevron ouvert
        chevron.classList.add("closed");
      } else {
        // si ouvert alors
        div_question.style.display = "none";
        div_js.innerHTML = "";
        e.target.dataset.status = false;

        chevron.classList.remove("closed");
      }
    });
  });

  ////////////////////
  // DECLARATION DE VARIABLE

  let liBtnQcm = document.querySelectorAll(".li_btn_qcm  ");
  let ulBtnQcm = document.querySelector(".list_btn_qcm  ");
  // console.log(liBtnQcm);
  let questionsLi = document.querySelectorAll(".list_questions li");
  let questionsLiOne = document.querySelector(".list_questions li");
  let questionsSpans = document.querySelectorAll(".list_questions li span");
  let questionslist = document.querySelector(".list_questions ");
  let forBtnQcm;
  let blocUlQcm = document.querySelector(".bloc_qcm ");
  let blocUlQuestion = document.querySelector(".bloc-toggle-ul-question");
  let navLinkChoice = document.querySelector(".choix");
  let linkQcmGiven = document.querySelector(".qcmGiven");
  let linkCreation = document.querySelector(".creation");
  // let liCreate = (questionslist.innerHTML = "<li></li>");
  // console.log(liCreate);

  //calcule height pour scroll active
  //Condition pour que la suite des instructions ne blocque pas sur les autres pages
  if (blocUlQcm) {
    let blocUlQcmHeight = blocUlQcm.getBoundingClientRect().height;
  }
  if (blocUlQuestion) {
    let blocUlQuestionHeight = blocUlQuestion.getBoundingClientRect().height;
  }
  if (ulBtnQcm) {
    let ulQcmHeight = ulBtnQcm.getBoundingClientRect().height;
  }
  if (questionslist) {
    let ulQuestionHeight = questionslist.getBoundingClientRect().height;
  }

  ////////////////////
  // HOVER DECLENCHEMENT SCROLL-Y
  blocUlQcm.addEventListener("mouseover", function () {
    if (ulQcmHeight > blocUlQcmHeight + 10) {
      blocUlQcm.classList.add("scroll_active");
    }
  });
  blocUlQcm.addEventListener("mouseout", function () {
    blocUlQcm.classList.remove("scroll_active");
  });

  blocUlQuestion.addEventListener("mouseover", function () {
    if (ulQuestionHeight > blocUlQuestionHeight + 5) {
      blocUlQuestion.classList.add("scroll_active");
    }
  });
  blocUlQuestion.addEventListener("mouseout", function () {
    blocUlQuestion.classList.remove("scroll_active");
  });

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
    linkCreation.classList.add("activeBorderLinkNav");
    navLinkChoice.style.width = "90%";
    navLinkChoice.style.gridTemplateColumns = "repeat(4,1fr)";
    linkCreation.style.display = "inline";
    linkQcmGiven.style.display = "inline";
  }

  ///////////////////
  // SELECTION UNIQUE DES BOUTONS QCMS ET DISPLAY DE LA LISTE DES QUESTIONS LIEES

  for (forBtnQcm = 0; forBtnQcm < liBtnQcm.length; forBtnQcm++) {
    // TEST
    // let questionsCache = JSON.parse(liBtnQcm[forBtnQcm].dataset.questionsCache);
    // for (let forWording = 0; forWording < questionsCache.length; forWording++) {
    //   // questionslist.innerHTML = `<li><span>${forWording + 1}</span>${
    //   //   questionsCache[forWording].wording
    //   // }</li>`;
    //   questionsLi[forWording].innerHTML = `<span>${forWording + 1}</span>${
    //     questionsCache[forWording].wording
    //   }`;
    // }
    //  click
    liBtnQcm[forBtnQcm].addEventListener("click", function (e) {
      // let eTarget = this.dataset.id;
      let questionsCache = JSON.parse(this.dataset.questionsCache);
      console.log(questionsCache);
      for (
        let forQuestionCache = 0;
        forQuestionCache < questionsCache.length;
        forQuestionCache++
      ) {
        console.log(questionsCache[forQuestionCache].wording);
      }

      for (
        let forWording = 0;
        forWording < questionsCache.length;
        forWording++
      ) {
        // questionslist.innerHTML = `<li><span>${forWording + 1}</span>${
        //   questionsCache[forWording].wording
        // }</li>`;
        questionsLi[forWording].innerHTML = `<span>${forWording + 1}</span>${
          questionsCache[forWording].wording
        }`;
      }

      // FAIRE UNE BOUCLE DE MON JSON ? PARSER LA VALEUR ET REMPLACER EN JS LES VALEURS DU LI DU TEMPLATE PAR CELLE CORRESPONDANTE DANS LE CACHE

      for (forBtnQcm = 0; forBtnQcm < liBtnQcm.length; forBtnQcm++) {
        if (this.dataset.id == liBtnQcm[forBtnQcm].dataset.id) {
          // questionslist.dataset.id = `${eTarget}`;
          liBtnQcm[forBtnQcm].classList.add("active_li");
        } else {
          liBtnQcm[forBtnQcm].classList.remove("active_li");
        }
      }
    });
  }
};
