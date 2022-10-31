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

            div_js.append(p, p_prop);
            // INSERTION DU PLETTER DEVANT LE PWORDING
            p_prop.insertBefore(p, p_prop.lastElementChild);

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

};
