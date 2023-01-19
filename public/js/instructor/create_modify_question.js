document.addEventListener("DOMContentLoaded", (event) => {
  let addbutton = document.querySelector(".addProposal");
  let removeButtons = document.querySelectorAll(".removeProposal");
  let li_proposal_v1 = document.querySelectorAll(".liProposal");
  addbutton.dataset.index = li_proposal_v1.length;
  let indexData = 0;
  let form = addbutton.dataset.form;
  let ul = document.querySelector("#listProposal");
  let modal = document.querySelector(".errorAddProposal ul");
  let containerModal = document.querySelector(".errorAddProposal");
  // .second temporaire voir blocProposal
  let proposalsPosition = document
    .querySelector(".second")
    .getBoundingClientRect().right;

  //////////////////////SCROLL UNTIL ERROR MODAL AFTER PROPOSALS
  if (modal) {
    containerModal.style.display = "flex";
    let modalPosition = modal.getBoundingClientRect().right;
    window.scroll({
      top: modalPosition + 200,
      behavior: "smooth",
    });
    modal.addEventListener("click", function () {
      containerModal.style.display = "none";
      window.scroll({
        top: proposalsPosition + 400,
        behavior: "smooth",
      });
    });
  }

  // *******************************************************************************************************
  // Add function
  function clickAdd(e) {
    // AJOUT D'UNE REPONSE LIMITE A 6
    if (indexData <= 5) {
      li_form(ul);
      if (indexData === 6) {
        addbutton.style.display = "none";
      }
    }
  }

  // *******************************************************************************************************
  // Remove Function
  function clickRemove() {
    this.parentElement.remove();
    let alphabet = ["A", "B", "C", "D", "E", "F"];
    let p_letters = document.querySelectorAll(".pletter");
    let count = 0;
    p_letters.forEach((p) => {
      let letters = p.textContent;
      p.innerText = alphabet[count];
      count++;
    });
    indexData--;

    if (indexData < 6) {
      addbutton.style.display = "inline-block";
    }
  }

  // *******************************************************************************************************
  // li_form Function
  function li_form(ul) {
    console.log(ul);
    let li = document.createElement("li");

    // Replace
    li.innerHTML += form.replace(/__name__/g, indexData);
    li.className = "liProposal";

    let checkbox = li.firstElementChild.lastElementChild.lastElementChild;
    checkbox.className = "isCorrect";

    let div_wording = li.firstElementChild.firstChild;
    div_wording.className = "divWording";

    let buttonRemoveNew = document.createElement("button");
    buttonRemoveNew.innerText = "Supprimer";
    buttonRemoveNew.classList.add("removeProposal");
    // Remove
    buttonRemoveNew.addEventListener("click", clickRemove);
    li.append(buttonRemoveNew);
    ul.append(li);

    // ************************************************************************************
    // AFFICHE LA LETTRE AU MOMENT DE L'AJOUT
    li.firstElementChild.className = "divProposal";

    letterProposal(li.firstElementChild, indexData);

    // INSERTION DU SCRIPT DE CKEDITOR OBLIGATOIRE POUR L'AJOUT DE CKEDITOR AU TEXTAREA DES NOUVELLES RÉPONSES
    if (CKEDITOR.instances[`create_question_proposals_${indexData}_wording`]) {
      CKEDITOR.instances[
        `create_question_proposals_${indexData}_wording`
      ].destroy(true);
      delete CKEDITOR.instances[
        `create_question_proposals_${indexData}_wording`
      ];
    }

    CKEDITOR.replace(`create_question_proposals_${indexData}_wording`, {
      uiColor: "#FFAC8F",
      toolbar: [
        [
          "Source",
          "Bold",
          "Italic",
          "Underline",
          "JustifyLeft",
          "JustifyCenter",
          "JustifyRight",
          "JustifyBlock",
          "CodeSnippet",
          "Blockquote",
          "Indent",
          "Outdent",
        ],
      ],
      extraPlugins: ["codesnippet"],
      codeSnippet_theme: "monokai",
      language: "fr",
    });

    // ************************************************************************************
    // INCREMENT LA LONGUEUR DU TABLEAU DES REPONSES
    indexData++;
  }

  // *******************************************************************************************************
  // letterProposal Function
  function letterProposal(div_proposal, indexData) {
    let label = div_proposal.firstElementChild.firstChild;

    let alphabet = ["A", "B", "C", "D", "E", "F", "G", "H"];

    let end = parseInt(indexData, 10) + 1; // 4 + 1 = 5    '4' + 1 = 41
    let begin = indexData;

    let letter = alphabet.slice(begin, end);

    let p = document.createElement("p");
    p.className = "circle greyCircle pletter";
    p.innerText = letter;

    div_proposal.firstElementChild.insertBefore(p, label);
  }

  // *******************************************************************************************************
  // Add
  addbutton.addEventListener("click", clickAdd);

  // *******************************************************************************************************
  //Remove
  for (const removeButton of removeButtons) {
    removeButton.addEventListener("click", clickRemove);
  }

  // *******************************************************************************************************
  // AFFICHE LA LETTRE DE LA REPONSE
  let div_proposal = document.querySelectorAll(".divProposal");
  Object.entries(div_proposal).forEach(([index, div]) => {
    letterProposal(div, indexData);
    indexData++;
  });

  // *******************************************************************************************************
  // DIV POUR CSS
  let div = document.querySelector("#create_question_difficulty");
  let div_input_label;
  let label = div.querySelectorAll("label");
  let input = div.querySelectorAll("input");
  for (let i = 0; i < 3; i++) {
    div_input_label = document.createElement("div");
    div_input_label.className = "divInputLabel";
    div_input_label.append(label[i], input[i]);
    div.append(div_input_label);
  }
  // *******************************************************************************************************
  // IMAGE TREFFLES

  let badgesInput = document.querySelectorAll(".divInputLabel input");

  for (
    let forBadgesInput = 0;
    forBadgesInput < badgesInput.length;
    forBadgesInput++
  ) {
    for (let forImgPath = 0; forImgPath < imgPath.length; forImgPath++) {
      if (forImgPath === forBadgesInput) {
        badgesInput[
          forBadgesInput
        ].style.backgroundImage = `url(${imgPath[forImgPath]})`;
      }
    }
  }
});
