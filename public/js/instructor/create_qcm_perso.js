window.onload = function (event) {
  console.log("hello");

  // display none
  let questionsCustom = document.querySelector(".questionsCustom");
  if (questionsCustom) {
    questionsCustom.classList.add("displayNone");
  }

  let proposalWordingDiv = document.querySelectorAll(".proposalWordingDiv");
  if (proposalWordingDiv) {
    proposalWordingDiv.forEach((div) => {
      div.classList.add("displayNone");
    });
  }

  let questionModify = document.querySelectorAll(".questionModify");
  if (questionModify) {
    questionModify.forEach((question) => {
      question.classList.add("displayNone");
    });
  }

  let partTwo = document.querySelector(".partTwo");
  if (partTwo) {
    partTwo.classList.add("displayNone");
  }

  ////////////////////////// Modal explaination
  let modalExplaination = document.querySelector(".bloc_modal_explaination");
  let crossExplaination = document.querySelector(".modal > img");
  let btnExplaination = document.querySelector(
    ".contentExplicationAndLegend button"
  );
  let moduleChoice = document.querySelectorAll(".selectModule option");
  let m = document.querySelector(".selectModule");
  let params, newUrlWithParam;
  //  recupération des données get dans l'url du select
  // if (window.location.href.includes(location.pathname)) {
  //   console.log(window.location.href);
  //   let url = window.location.href;
  //   // split("") sans paramètres découpe une chaine de caractère et la retourne dans un tableau
  //   // split("#") avec le séparateur (caractère spéciaux ex reg ) retourne toute la chaîne de caractère sans la découper dans un tableau
  //   // let queryString = url.split("#")[0].split("?");
  //   let queryString = url.split("#")[0].split("?");

  //   // split découpe l'url dans en retournant un tableau dont l'index est 0 sachant que c'est l'url sans param
  //   // let queryString = url.split("?")[0];
  //   // queryString = queryString.split("?");
  //   // let arr = queryString.split("&");
  //   let startParam = queryString[0] + "?";
  //   console.log(startParam.split("?")[0]);
  //   console.log(startParam);

  //   console.log(url);

  //   moduleChoice.forEach((o) => {
  //     // remplacer par addEventListener("select") pour selct option
  //     o.addEventListener("click", (e) => {
  //       let firstKindOfParam = e.target.parentNode.name;

  //       let secondKindOfFirstParam = e.target.value;
  //       newUrlWithParam = new URL(
  //         startParam + `${firstKindOfParam}=` + `${secondKindOfFirstParam}`
  //       );
  //       params = newUrlWithParam.search;
  //       // paramètre ajouter a l'url sans recharger de la page
  //       window.history.pushState({}, "", params);
  //       // document.location.href = `${newUrlWithParam}`;

  //       // if (e.target.dataset.qcmModuleCache) {
  //       //   let dataQcmModule = JSON.parse(e.target.dataset.qcmModuleCache);

  //       //   console.log("yep");
  //       //   console.log(Object.values(dataQcmModule));
  //       // }
  //     });
  //   });
  // }

  //  modal

  if (window.location.href.includes(location.pathname) && btnExplaination) {
    if (btnExplaination) {
      btnExplaination.addEventListener("click", function (e) {
        modalExplaination.style.display = "flex";
      });
    } else {
      btnExplaination = null;
    }

    if (modalExplaination) {
      crossExplaination.addEventListener("click", function (e) {
        modalExplaination.style.display = "none";
      });
    }
  }
  console.log("hello");
  let inputDifficulty = document.querySelector(".blocChoiceDifficulties input");

  //Stop event  btn form si contrainte module qcm pas respecter

  let btnValidModuleDifficulty = document.querySelector(".buttonGenerate");
  // btnValidModuleDifficulty.addEventListener("click", function (e) {
  //   if (inputDifficulty.value == "" ) {
  //     e.preventDefault();
  //     let pError = document.createElement("p");
  //     pErrorText = pError.innerText = "veuillez selectionner un niveau";
  //     document.querySelector(".choiceTypeOfQcm").append(pErrorText);
  //   }
  // });

  // déclaration var fetch
  let questionLiQuestionsCache = document.querySelectorAll(".questionLi");
  let ulForQuestionsCache = document.querySelector(".backWhite ul ");
  let wordindQuestionsCache = document.querySelectorAll(".questionWordingP ");
  let ulQcmDragAndDropQuestionsCache = document.querySelector(
    ".qcmChoisedMain ul "
  );
  let questionsOfficialQcm = document.querySelector(".questionsOfficial ");
  let responselabel = document.querySelector(".responselabel ");

  let letters = ["A", "B", "C", "D", "E", "F"];
  btnValidModuleDifficulty.addEventListener("click", function (e) {
    e.preventDefault();
    // racine de l'url sans param
    // let urlFetch = location.href.split("#")[0].split("?");
    let moduleOption = document.getElementById("moduleOption");
    let selectDifficulty = document.getElementById("qcmDifficulty");
    // test 1
    fetch(
      `/instructor/qcms/random_fetch/${moduleOption.value}/${selectDifficulty.value}`,
      {
        method: "GET",
      }
    )
      .then((response) => {
        if (response) {
          return response.json();
          // console.log(response);
          // console.log(response.json());
          // console.log(response.bodyUsed);

          // test.then((response) => console.log(response));

          // return console.log(response);
        }
        // return response.json();
      })
      .then((data) => {
        console.log(data);
        console.log(data.randomQuestion.length);

        // Object.keys(data.randomQuestion).forEach(function (question, index) {
        //   // console.log((questionLiQuestionsCache.innerHTML = `${question}`));

        //   questionLiQuestionsCache.innerHTML = `

        //   <div class="questionWordingDiv">
        //   <p class="questionWordingP">
        //       <span class="numeroForm">{{ count }}</span>
        //       ${question.wording}

        //   </p>
        //   <p class="chevronBasP">
        //       <img src="{{asset('build/images/chevron_bas.png')}}" alt="Chevron ouvrant" class="chevronBasImg chevron">
        //   </p>
        // </div>
        // <div class="proposalWordingDiv">
        //     <p class="proposalWordingP"><span class="numeroProp">hello</span>
        //       popo
        //     </p>
        // </div>

        //   `;
        //   // console.log(data.randomQuestion[question]); // affiche "3", "5", "7", "coucou"
        //   // console.log(data.randomQuestion[index]); // affiche "3", "5", "7", undefined
        // });
        for (
          let forQuestion = 0;
          forQuestion < data.randomQuestion.length;
          forQuestion++
        ) {
          ulForQuestionsCache.innerHTML += `
            <li class="questionLi">
              <div class="questionWordingDiv">
                <p class="questionWordingP">
                  <span class="numeroForm"> ${forQuestion + 1}</span>
                  ${data.randomQuestion[forQuestion].wording}
                </p>
                <p class="chevronBasP">
                  <img src="" alt="Chevron ouvrant" class="chevronBasImg chevron">
                </p>
              </div>
              <div class="proposalWordingDiv">
              </div>
            </li >
            `;
          let proposalQuestionsCache = document.querySelectorAll(
            ".proposalWordingDiv "
          );
          for (
            let forProposal = 0;
            forProposal < data.randomQuestion[forQuestion].proposals.length;
            forProposal++
          ) {
            proposalQuestionsCache[forQuestion].innerHTML += `
            
              <p class="proposalWordingP"><span class="numeroProp">${
                forProposal + 1
              }</span>
              ${data.randomQuestion[forQuestion].proposals[forProposal].wording}
              </p>

            `;
          }

          // DRAG AND DROP QCM QUESTIONS CACHE

          ulQcmDragAndDropQuestionsCache.innerHTML += `
                <li class="qcmChoisedLi"  draggable="true">
                    <div class="qcmChoisedLiDiv">
                        <div class="questionWordingDiv qcmChoisedQuestionWordingDiv">
                            <p class="qcmChoisedTreffleP">
                          
                            </p>
                            <p class="qcmChoisedQuestionWordingP questionWordingP" data-id = 
                              <span> ${forQuestion + 1}</span>  
                               ${data.randomQuestion[forQuestion].wording}
                            </p>
                            <p class="qcmChoisedchevronBasP">
                                <img src="/build/images/chevron_bas.216a40a5.svg" alt="Chevron ouvrant" class="qcmChoisedchevronBasImg chevron">
                            </p>
                        </div>
                        <div class="qcmChoisedProposalWordingDiv proposalWordingDiv">        
                        </div>
                    </div>
                </li>
        `;
          let imgQcmChoisedTreffleP = document.querySelectorAll(
            ".questionWordingDiv.qcmChoisedQuestionWordingDiv .qcmChoisedTreffleP "
          );
          if (data.randomQuestion[forQuestion].difficulty == 1) {
            imgQcmChoisedTreffleP[
              forQuestion
            ].innerHTML += `<img src="{{asset('build/images/trefle_facile_bon_vert.png')}}" alt="Trèfle à trois feuilles" class="qcmChoisedTrefle" data-level="easy">`;
          } else if (data.randomQuestion[forQuestion].difficulty == 2) {
            imgQcmChoisedTreffleP[
              forQuestion
            ].innerHTML += ` <img src="{{asset('build/images/trefle_moyen_bon_vert.png')}}" alt="Trèfle à quatre feuilles" class="qcmChoisedTrefle" data-level="medium">`;
          } else if (data.randomQuestion[forQuestion].difficulty == 3) {
            imgQcmChoisedTreffleP[
              forQuestion
            ].innerHTML += ` <img src="{{asset('build/images/trefle_difficile_bon_vert.png')}}" alt="Trèfle à quatre feuilles" class="qcmChoisedTrefle" data-level="difficult">`;
          }
          let proposalDragAndDropQuestionsCache = document.querySelectorAll(
            ".qcmChoisedProposalWordingDiv.proposalWordingDiv "
          );
          for (
            let forProposal = 0;
            forProposal < data.randomQuestion[forQuestion].proposals.length;
            forProposal++
          ) {
            proposalDragAndDropQuestionsCache[forQuestion].innerHTML += `
            
            <div class="qcmChoisedProposalWordingP proposalWordingP">
                 <span class="numeroProp nPropPartTwo">${letters[forProposal]}</span>
                 ${data.randomQuestion[forQuestion].proposals[forProposal].wording}
            </div>
  
            `;
          }
        }

        // OFFICIAL QUESTIONS

        for (
          let forQuestionOfficial = 0;
          forQuestionOfficial < data.officialQuestions.length;
          forQuestionOfficial++
        ) {
          questionsOfficialQcm.innerHTML += `
              <li class="officialQuestionLi" draggable="true">
                <div class="OfficialQuestionWordingDiv questionWordingDiv ">
                    <div class="qcmChoisedTreffleP"></div>
                    <div class="officialQuestionWordingP questionWordingP" data-id="{{question.id}}">
                      <span> ${forQuestionOfficial + 1}</span>  
                      ${data.officialQuestions[forQuestionOfficial].wording}
                    </div>
                    <div class="officialChevronBasP chevronBasP">
                        <img src="{{asset('build/images/chevron_bas.png')}}" alt="Chevron ouvrant" class="officialChevronBasImg chevronBasImg chevron">
                    </div>
                    <div class="modifyQuestionImgDiv ">
                               <img src="{{asset('build/images/edit.png')}}" alt="bouton modifier" class="modifyQuestionImg" >
                    </div>
                </div>
                <div>
                    <div class="OfficialProposalWordingDiv proposalWordingDiv">
                      <p class="responselabel">Les réponses</p>
                      
                    </div>
                </div>

            </li>
          `;
          let proposalsOfficialQcm = document.querySelectorAll(
            ".OfficialProposalWordingDiv.proposalWordingDiv "
          );
          for (
            let forProposalOfficial = 0;
            forProposalOfficial <
            data.officialQuestions[forQuestionOfficial].proposals.length;
            forProposalOfficial++
          ) {
            console.log(data.officialQuestions[forQuestionOfficial]);

            proposalsOfficialQcm[forQuestionOfficial].innerHTML += `
                  <p class =" officialProposalWordingP proposalWordingP " data-status="${
                    data.officialQuestions[forQuestionOfficial].proposals[
                      forProposalOfficial
                    ].isCorrectAnswer
                  }" data-id="${
              data.officialQuestions[[forQuestionOfficial]].proposals[
                forProposalOfficial
              ].id
            }" >
                       <span class="numeroProp nPropPartTwo"> ${
                         letters[forProposalOfficial]
                       } </span><span class="spanWording" >${
              data.officialQuestions[forQuestionOfficial].proposals[
                forProposalOfficial
              ].wording
            } </span>
                  </p>
                `;
          }
          // responselabel.append(test);
          proposalsOfficialQcm[forQuestionOfficial].innerHTML += `
              <div class="questionModify">
                <button class="save">{{ 'button.register'|trans }}</button>
              </div>
           `;
        }

        // if (result) {
        //   // console.log(result);
        //   console.log(result[0]);
        //   let monObj = document.createElement("p");
        //   let pObj = (monObj.innerText = `${result.officialQuestions}`);
        //   document.querySelector("choiceDifficultiesAndTypeOfQcm").append(pObj);
        // }
        // for (let i = 0; i < data.randomQuestion.length; i++) {
        //   questionLiQuestionsCache.innerHTML += `${data.randomQuestion[i].wording}`;
        // }
      });
  });

  // /////////////////////// SHOW QCM

  let btnShowQcm = document.querySelector(".btnShowQcm");
  let showQcm = document.querySelector(".backWhite");

  btnShowQcm.addEventListener("click", function (e) {
    showQcm.classList.toggle("activeQcm");
    console.log(e);
    if (showQcm.classList.contains("activeQcm")) {
      showQcm.scrollIntoView({
        behavior: "smooth",
      });
    }
  });
  /***************************************************************************/
  let qcmChoisedMainSide = document.querySelector(".qcmChoisedMain");
  if (qcmChoisedMainSide) {
    calcNbrQuestionByLevel(qcmChoisedMainSide);
  }

  let questionsOfficial = document.querySelector(".questionsOfficial");
  let buttonQuestionType = document.querySelectorAll(
    ".btnChoiseQuestionType button"
  );

  // ///////////MODULE PAR NIVEAU DE DIFFICULTE DE QCM

  let btnDifficultyQcmModule = document.querySelectorAll(
    ".listChoiceDifficulties li"
  );
  // inputDifficulty.value = 3;

  // for (let btn = 0; btn < btnDifficultyQcmModule.length; btn++) {
  //   console.log(location.search);
  //   btnDifficultyQcmModule[btn].addEventListener("click", function (e) {
  //     console.log(e.target, "la");

  //     // e.stopPropagation();
  //     if (location.href.includes(location.search) && location.search !== "") {
  //       console.log(location.href);
  //       console.log(location.search);
  //       console.log("yeah");
  //       inputDifficulty.value = `${e.target.dataset.difficulty}`;
  //       if (
  //         this.dataset.difficulty ==
  //         btnDifficultyQcmModule[btn].dataset.difficulty
  //       ) {
  //         e.target.classList.add("colorDifficulty");

  //         // console.log(
  //         //   (inputDifficulty.value = `${e.target.dataset.difficulty}`)
  //         // );
  //       }
  //       for (let btn = 0; btn < btnDifficultyQcmModule.length; btn++) {
  //         if (
  //           this.dataset.difficulty !==
  //           btnDifficultyQcmModule[btn].dataset.difficulty
  //         ) {
  //           btnDifficultyQcmModule[btn].classList.remove("colorDifficulty");
  //         }
  //       }
  //     } else {
  //       let pError = document.createElement("p");
  //       // retirer les doublons au clic
  //       pErrorText = pError.innerText = "veuillez selectionner un module";
  //       document
  //         .querySelector(".blocBorderChoiceDifficulties")
  //         .after(pErrorText);
  //       e.target;
  //     }
  //   });
  // }

  //Event faire apparaitre la partie 2********************************************************************************
  let btnToggle = document.querySelector(".btnToggle");
  let dragAndDrop = document.querySelector(".dragAndDrop");
  let btnCustom = document.querySelector(".btnCustom");
  if (btnCustom) {
    btnCustom.addEventListener("click", (e) => {
      let partTwo = document.querySelector(".partTwo");
      partTwo.classList.remove("displayNone");
      dragAndDrop.scrollIntoView({
        behavior: "smooth",
      });
      if ((partTwo.style.display = "block")) {
        btnToggle.style.display = "none";
      }
    });
  }

  //   //////////////NUMERO OF LI TEST QUESTIONS OFFICIELS/ PERSONNALISED
  let questionWordingSpan = document.querySelectorAll(
    ".officialQuestionWordingP span"
  );

  let questionQcmChoicedSpan = document.querySelectorAll(
    ".qcmChoisedQuestionWordingP span"
  );
  let listQuestionsOfficials = document.querySelectorAll(
    ".questionsOfficial > ul>li"
  );
  let questionQcmChoiced = document.querySelectorAll(
    ".qcmChoisedQuestionWordingP"
  );
  for (let i = 0; i < listQuestionsOfficials.length; i++) {
    questionWordingSpan[i].innerHTML = `${i + 1}`;
    questionWordingSpan[i].style.color = "red";
  }
  for (let a = 0; a < questionQcmChoiced.length; a++) {
    questionQcmChoicedSpan[a].innerHTML = `${a + 1}`;
    questionQcmChoicedSpan[a].style.color = "green";
  }

  // ////////////// QUESTIONS OFFICIELS/ PERSONNALISED COUNT
  // console.log(listQuestionsOfficials.length);
  let btnQuestionsOfficial = document.querySelector(".btnQuestionsOfficial");

  // Event sur les button de choix du type de question****************************************************************
  buttonQuestionType.forEach((button) => {
    button.addEventListener("click", (e) => {
      let choiceBtn = e.target;
      let otherBtn;

      if (e.target.previousElementSibling === null) {
        otherBtn = choiceBtn.nextElementSibling;
      } else {
        otherBtn = choiceBtn.previousElementSibling;
      }

      if (choiceBtn.className === "btnQuestionsOfficial") {
        questionsCustom.classList.add("displayNone");
        questionsOfficial.classList.remove("displayNone");
        choiceBtn.style.backgroundColor = "#FFAC8F";
        otherBtn.style.backgroundColor = "#93AD6E";
      } else {
        questionsOfficial.classList.add("displayNone");
        questionsCustom.classList.remove("displayNone");
        choiceBtn.style.backgroundColor = "#93AD6E";
        otherBtn.style.backgroundColor = "#FFAC8F";
      }
    });
  });

  // Event sur les chevrons*******************************************************************************************
  let chevrons = document.querySelectorAll(".chevron");
  let officialQuestionLi = document.querySelector(".officialQuestionLi");
  chevrons.forEach((chevron) => {
    chevron.addEventListener("click", (e) => {
      let proposalWordingDiv;
      officialQuestionLi.style.gridGap = "0.2em"; //a voir
      if (e.target.classList.contains("officialChevronBasImg")) {
        proposalWordingDiv =
          e.target.parentNode.parentNode.parentNode.lastElementChild
            .firstElementChild;
      } else {
        proposalWordingDiv =
          e.target.parentNode.parentNode.parentNode.lastElementChild;
      }

      proposalWordingDiv.classList.toggle("displayNone"); //a voir

      if (proposalWordingDiv.classList.contains("displayNone")) {
        e.target.setAttribute("src", chevronBas);
        officialQuestionLi.style.gridGap = "0";

        //img modify remove display none quand chevron vers le bas
        //a revoir
        // document
        //   .querySelector(".modifyQuestionImg")
        //   .parentNode.classList.remove("displayNone");
      } else {
        e.target.setAttribute("src", chevronHaut);
        officialQuestionLi.style.gridGap = "0";
      }
    });
  });

  let dbValues = [];
  //correct answer précoché

  // event pour modifier une question*********************************************************************************
  let modifyQuestionImgDiv = document.querySelectorAll(".modifyQuestionImgDiv");
  modifyQuestionImgDiv.forEach((div) => {
    div.addEventListener("click", (e) => {
      let img = e.target;
      img.parentNode.previousElementSibling.firstElementChild.setAttribute(
        "src",
        chevronHaut
      );
      // console.log(
      //   document.querySelectorAll(".proposalWordingP").dataset.status,
      //   "hi"
      // );
      //   console.log(
      //     img.parentNode.previousElementSibling.firstElementChild,
      //     "hello"
      //   );
      // document
      //   .querySelector(".modifyQuestionImg")
      //   .parentNode.classList.remove("displayNone");
      //   img.parentNode.classList.add("displayNone");

      let divParent = img.parentNode.parentNode;
      let question = divParent.querySelector(".questionWordingP");

      let proposalWordingDiv =
        img.parentNode.parentNode.parentNode.lastElementChild.lastElementChild;

      let questionModify =
        img.parentNode.parentNode.parentNode.lastElementChild.lastElementChild
          .lastElementChild;

      //Css
      //   proposalWordingDiv.style.flexDirection = "column";
      //   let nPropPartTwo = proposalWordingDiv.querySelectorAll(".nPropPartTwo");
      //   nPropPartTwo.forEach((nbr) => {
      //     nbr.style.padding = "2px 10px";
      //   });

      // si la div des proposal n'est pas dérouler
      if (proposalWordingDiv.classList.contains("displayNone")) {
        proposalWordingDiv.classList.remove("displayNone");
        img.parentNode.classList.add("displayNone");
      }

      // Afficher le bouton 'enregistrer'
      questionModify.classList.remove("displayNone");

      // Si la question a 6 proposal
      let children = proposalWordingDiv.querySelectorAll(
        ".officialProposalWordingP"
      );
      if (children.length <= 6) {
        let buttonAdd = document.createElement("button");
        buttonAdd.setAttribute("class", "buttonAddProp");
        buttonAdd.setAttribute("id", "buttonAdd");
        buttonAdd.innerHTML = "Ajouter une réponse";
        buttonAdd.addEventListener("click", (e) => {
          addProposal(e, proposalWordingDiv, children.length);
        });
        proposalWordingDiv.insertBefore(buttonAdd, questionModify);
      }

      // Bouton cancel
      let p = document.createElement("p");
      p.classList.add("cancel");
      p.innerHTML = "Annuler";
      proposalWordingDiv.append(p);
      p.addEventListener("click", cancelModifyQuestion);

      // Get db data
      let questionId = question.dataset.id;
      dbValues.push({
        idQuestion: questionId,
        proposals: [],
      });

      //Modification de la balise p
      for (let i = 0; i < children.length; i++) {
        children[i].classList.add("modifyP");
        let id = children[i].dataset.id;

        // textarea
        let value = children[i].children[1].textContent.trim();
        let textarea = document.createElement("textarea");
        textarea.textContent = value;
        textarea.setAttribute("name", id);

        let checkBox = document.createElement("input");
        checkBox.setAttribute("type", "checkbox");
        checkBox.classList.add("checkBoxIsCorrect");

        // img
        let img = document.createElement("img");
        img.setAttribute("src", deleteImg);
        img.classList.add("deleteProp");
        img.addEventListener("click", deleteProp);

        children[i].lastChild.remove();
        children[i].children[1].remove();
        children[i].append(textarea, checkBox);
        children[i].append(img);

        //Save former values of question

        dbValues.forEach((cel) => {
          if (cel.idQuestion === questionId) {
            cel.proposals.push({
              id: id,
              value: value,
            });
          }
        });
      }
      //proposal input précoché voir data-status dans twig

      for (let dataNumber = 0; dataNumber < children.length; dataNumber++) {
        if (children[dataNumber].dataset.status == 1) {
          let dataChildren = children[dataNumber];
          if (
            dataChildren.children[2].localName === "input" &&
            dataChildren.children[2].classList.value === "checkBoxIsCorrect"
          ) {
            dataChildren.children[2].checked = true;
          }
        }
      }
    });
  });

  // Event Enregistrer une question***********************************************************************************
  let saveBtns = document.querySelectorAll(".save");
  saveBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      let officialQuestionLi =
        e.target.parentNode.parentNode.parentNode.parentNode;
      let question = officialQuestionLi.querySelector(
        ".officialQuestionWordingP "
      );
      let questionId = question.dataset.id;
      let div = e.target.parentNode.parentNode;
      let modifyP = div.querySelectorAll(".modifyP");
      let module = document.querySelector(".qcmNameInput").dataset.module;

      let values = {
        questionId: questionId,
        module: module,
        wording: question.textContent.trim(),
        proposals: [],
      };
      let countCorrectAnswer = 0;
      modifyP.forEach((p) => {
        values.proposals.push({
          wording: p.children[1].textContent.trim(),
          id: p.children[1].parentNode.dataset.id,
          isCorrectAnswer: p.children[2].checked,
        });
        if (p.children[2].checked) {
          countCorrectAnswer++;
        }
      });
      if (countCorrectAnswer > 1) {
        values.isMultiple = true;
      } else {
        values["isMultiple"] = false;
      }
      //   on passe pas dans le fetch VERIFICATION IMPORTANTE POUR TRANSMISSION DE DONNEES
      //   let res = fetch(route);
      //   console.log(alert(response.headers.get("Content-Type")));
      fetch(route, {
        method: "POST",
        body: JSON.stringify(values), // The data
        headers: {
          "Content-type": "application/json", // The type of data you're sending
        },
      })
        .then((response) => response.json())
        .then((data) => console.log(data));
    });
  });

  //Event select li to move*******************************************************************************************
  let liDiv = document.querySelectorAll(".officialQuestionLi , .qcmChoisedLi ");
  liDiv.forEach((li) => {
    li.addEventListener("click", (e) => {
      if (e.target.tagName !== "IMG") {
        li.classList.toggle("borderColor");
      }
    });
  });

  // Event arrow right and left************************************************************************
  /*
        Les questions dans la partie qcm choisie sont aussi dans la liste des questions que se soit custom ou officielle
        Quand l'utilisateur déplace un question du qcm vers la liste de question est supprimer pour pas avoir de doublons
     */
  let arrowRight = document.querySelector(".arrowRight");
  let arrowLeft = document.querySelector(".arrowLeft");

  let questionsOfficialSide = document.querySelector(".questionsOfficial");
  let qcmChoisedMain = document.querySelector(".qcmChoisedMain");

  //arrowRight QcmChoised -> questions
  if (arrowRight) {
    arrowRight.addEventListener("click", (e) => {
      let firstLi = questionsOfficialSide.firstElementChild.firstElementChild;
      let ulQuestionsOfficialSide = questionsOfficialSide.firstElementChild;

      let qcmChoisedLis = qcmChoisedMain.querySelectorAll(".borderColor");
      qcmChoisedLis.forEach((li) => {
        //questions officelles
        if (li.classList.contains("qcmChoisedLi")) {
          let allP = li.firstElementChild.children;
          // Bouton modifier la question
          for (let i = 0; i < allP.length; i++) {
            if (allP[i].tagName === "DIV") {
              allP[i].classList.remove("displayNone");
            }
          }

          let qcmChoisedLi = li;
          li.remove();
          ulQuestionsOfficialSide.insertBefore(qcmChoisedLi, firstLi);

          ////nombre de questions choisies drop dans questions officielles
          let liQcmChoicedInOfficialQcm = document.querySelectorAll(
            ".questionsOfficial .qcmChoisedLi"
          );
          if (liQcmChoicedInOfficialQcm) {
            for (let i = 0; i < liQcmChoicedInOfficialQcm.length; i++) {
              btnQuestionsOfficial.innerHTML = `Questions officielles :${
                listQuestionsOfficials.length + liQcmChoicedInOfficialQcm.length
              }`;
            }
          }
        } else {
          li.remove();
        }
      });
      let elementSelect = document.querySelectorAll(".borderColor");
      elementSelect.forEach((el) => {
        el.classList.remove("borderColor");
      });
      if (qcmChoisedMain) {
        calcNbrQuestionByLevel(qcmChoisedMain);
      }
    });
  }

  //arrowLeft qcmChoised <- question
  if (arrowLeft) {
    arrowLeft.addEventListener("click", (e) => {
      let firstLi = qcmChoisedMain.firstElementChild.firstElementChild;
      let ulQcmChoisedSide = qcmChoisedMain.firstElementChild;

      let officialLis = questionsOfficialSide.querySelectorAll(".borderColor");
      officialLis.forEach((li) => {
        console.log(li, "la");
        //questions officelles
        if (li.classList.contains("officialQuestionLi")) {
          let allP = li.firstElementChild.children;
          console.log(allP[3], "YES");
          // Bouton modifier la question
          for (let i = 0; i < allP.length; i++) {
            //etant donné que les p sont transformées en div donc contition lié au class pour sélectionner la bonne div
            if (allP[i].classList === "modifyQuestionImgDiv") {
              allP[i].classList.add("displayNone");
            }
          }

          let officialLi = li;
          console.log(officialLi, "la");
          li.remove();
          ulQcmChoisedSide.insertBefore(officialLi, firstLi);
          //count questions officiel dans question choisie
          // FAIRE UNE FONCTION TOTALE DE listQuestionsOfficials.length +liQcmChoicedInOfficialQcm.length dans la fonction de déplacement vers ldroite et la rappeler en bas
          //
          let liQcmChoicedInOfficialQcm =
            document.querySelectorAll(".qcmChoisedLi");
          let liQcmOfficialInQcmChoiced = document.querySelectorAll(
            ".qcmChoisedMain .officialQuestionLi"
          );
          if (liQcmChoicedInOfficialQcm) {
            console.log(liQcmChoicedInOfficialQcm, "yo");
            for (let i = 0; i < liQcmChoicedInOfficialQcm.length; i++) {
              btnQuestionsOfficial.innerHTML = `Questions officielles :${
                listQuestionsOfficials.length +
                liQcmChoicedInOfficialQcm.length -
                liQcmOfficialInQcmChoiced.length
              }`;
            }
          }
        }
      });

      let elementSelect = document.querySelectorAll(".borderColor");
      elementSelect.forEach((el) => {
        el.classList.remove("borderColor");
      });
      if (qcmChoisedMain) {
        calcNbrQuestionByLevel(qcmChoisedMain);
      }
    });
  }

  ////////////////////// COUNT LI QCM CHOICED IN OFFICIAL QCM

  // Event Validation qcm
  let qcmValidationBtn = document.querySelector(".qcmValidation");
  if (qcmValidationBtn) {
    qcmValidationBtn.addEventListener("click", (e) => {
      let questionsSelect = {};
      let qcmNameInput = document.querySelector(".qcmNameInput").value;
      let module = document.querySelector(".qcmNameInput").dataset.module;
      let isPublic = document.getElementById("isPublicInput").checked;
      let qcmChoisedLevel = document
        .getElementById("qcmChoisedLevel")
        .textContent.trim();
      questionsSelect = {
        name: qcmNameInput,
        level: qcmChoisedLevel,
        module: module,
        isPublic: isPublic,
        questions: [],
      };
      let questions = document.querySelectorAll(
        ".qcmChoisedQuestionWordingDiv"
      );
      questions.forEach((question) => {
        let level = question.firstElementChild.firstElementChild.dataset.level;
        let wording = question.children[1].textContent.trim();
        let id = question.children[1].dataset.id;
        questionsSelect["questions"].push({
          id: id,
          level: level,
          wording: wording,
        });
        console.log(questionsSelect["question"]);
      });
      console.log(routeInstructorQcmFetch);
      console.log(questionsSelect);
      fetch(routeInstructorQcmFetch, {
        method: "POST",
        headers: {
          "Content-type": "application/json", // The type of data you're sending
        },
        body: JSON.stringify(questionsSelect), // The data
      })
        .then((res) => res.json())
        .then((result) => {
          if (result == "ok") {
            window.location.href =
              "https://127.0.0.1:8000/instructor/creations/questions";
          }
        });
    });
  }

  /**********************************************************************************************************************/
  function addProposal(e, parent, lengthProp) {
    let pProp = document.createElement("p");
    pProp.classList.add(
      "officialProposalWordingP",
      "proposalWordingP",
      "modifyP"
    );

    let textarea = document.createElement("textarea");
    textarea.setAttribute("name", "newProp");

    let checkBox = document.createElement("input");
    checkBox.setAttribute("type", "checkbox");
    checkBox.classList.add("checkBoxIsCorrect");

    let img = document.createElement("img");
    img.setAttribute("src", deleteImg);
    img.classList.add("deleteProp");
    img.addEventListener("click", deleteProp);

    let span = document.createElement("span");
    span.classList.add("numeroProp", "nPropPartTwo");

    // Trouver la lettre
    let alphabet = ["A", "B", "C", "D", "E", "F"];
    let end = parseInt(lengthProp - 2, 10) + 1; // 4 +1 = 5    '4' + 1 = 41
    let begin = lengthProp - 2;
    let letter = alphabet.slice(begin, end);
    span.innerHTML = letter;

    pProp.append(span, textarea, checkBox, img);
    parent.insertBefore(pProp, e.target);

    let children = parent.querySelectorAll(".modifyP");

    //Si < 6 réponses
    if (children.length >= 6) {
      e.target.classList.add("displayNone");
      let message = document.createElement("p");
      message.classList.add("message");
      message.textContent =
        "Le nombre maximal de 6 réponses possibles pour une question a été atteint";
      parent.append(message);
    }
  }

  function deleteProp(e) {
    let div = e.target.parentNode.parentNode;
    e.target.parentNode.remove();
    let p = div.querySelectorAll(".modifyP");
    let message;
    //Si reponse < 6
    if (typeof message !== undefined) {
      message = div.querySelector(".message");
      message.remove();
      let buttonAddProp = div.querySelector(".buttonAddProp");
      buttonAddProp.classList.remove("displayNone");
    }

    //Réactualiser les lettres des réponses
    let alphabet = ["A", "B", "C", "D", "E", "F"];
    let spanLetters = div.querySelectorAll(".nPropPartTwo");
    let count = 0;
    spanLetters.forEach((p) => {
      p.innerHTML = alphabet[count];
      count++;
    });
  }

  function cancelModifyQuestion(e) {
    let parent = e.target.parentNode.parentNode;
    let parentQuestion = e.target.parentNode.parentNode.parentNode;
    let question = parentQuestion.querySelector(".questionWordingP");
    let proposalWordingDiv = parent.querySelector(".proposalWordingDiv");
    let divParentP = proposalWordingDiv.parentNode;

    //Css
    proposalWordingDiv.style.flexDirection = "column";
    let nPropPartTwo = proposalWordingDiv.querySelectorAll(".nPropPartTwo");
    nPropPartTwo.forEach((nbr) => {
      nbr.style.padding = "2px 12px";
    });
    parentQuestion.firstElementChild.lastElementChild.classList.remove(
      "displayNone"
    );

    proposalWordingDiv.remove();

    let div = document.createElement("div");
    div.classList.add("proposalWordingDiv", "OfficialProposalWordingDiv");
    divParentP.append(div);

    let id = question.dataset.id;
    let questionToEdit = dbValues.filter((question) => {
      return question.idQuestion === id;
    });
    questionToEdit[0].proposals.forEach(function callback(value, index) {
      let span = document.createElement("span");
      span.classList.add("numeroProp", "nPropPartTwo");

      // Trouver la lettre
      let alphabet = ["A", "B", "C", "D", "E", "F"];
      let end = parseInt(index, 10) + 1; // 4 +1 = 5    '4' + 1 = 41
      let begin = index;
      let letter = alphabet.slice(begin, end);
      span.innerHTML = letter;

      let p = document.createElement("p");
      p.classList.add("officialProposalWordingP", "proposalWordingP");
      let spanInP = document.createElement("span");
      spanInP.innerHTML = value.value;
      p.dataset.id = value.id;
      p.append(span, spanInP);
      div.append(p);
    });
  }

  function calcNbrQuestionByLevel(side) {
    let easyLevel = 0;

    let mediumLevel = 0;
    let difficultyLevel = 0;

    let questions = side.querySelectorAll(".qcmChoisedTrefle");
    if (side) {
      questions.forEach((question) => {
        if (question.dataset.level === "easy") {
          easyLevel++;
        } else if (question.dataset.level === "medium") {
          mediumLevel++;
        } else {
          difficultyLevel++;
        }
      });
    }

    let pEasy = document.getElementById("easy");
    pEasy.innerHTML = easyLevel;
    let pMedium = document.getElementById("medium");
    pMedium.innerHTML = mediumLevel;
    let pDifficult = document.getElementById("difficulty");
    pDifficult.innerHTML = difficultyLevel;

    let qcmChoisedLevel = document.getElementById("qcmChoisedLevel");
    if (difficultyLevel > mediumLevel && difficultyLevel > mediumLevel) {
      qcmChoisedLevel.innerHTML = "Difficile";
    } else if (mediumLevel > difficultyLevel && mediumLevel > easyLevel) {
      qcmChoisedLevel.innerHTML = "Moyen";
    } else {
      qcmChoisedLevel.innerHTML = "Facile";
    }
  }
};

// TODO
// info bouton modif une question retiré
// faire un event au survol pour signifier ce changement
