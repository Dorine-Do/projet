// window.onload = function (event) {
    //  Déclaration var display none + modal
//     let questionsCustom,
//         proposalWordingDiv,
//         questionModify,
//         partTwo,
//         modalExplaination,
//         crossExplaination,
//         btnExplaination;
//
//     // Display none
//     questionsCustom = document.querySelector(".questionsCustom");
//     questionsCustom.classList.add("displayNone");
//
//     proposalWordingDiv = document.querySelectorAll(".proposalWordingDiv");
//     proposalWordingDiv.forEach((div) => {
//         div.classList.add("displayNone");
//     });
//
//     questionModify = document.querySelectorAll(".questionModify");
//     questionModify.forEach((question) => {
//         question.classList.add("displayNone");
//     });
//
//     partTwo = document.querySelector(".partTwo");
//     partTwo.classList.add("displayNone");
//
//     // MODAL EXPLAINATION
//     modalExplaination = document.querySelector(".blocModalExplaination");
//     crossExplaination = document.querySelector(".modal > img");
//     btnExplaination = document.querySelector(
//         ".contentExplicationAndLegend button"
//     );
//
//     if (window.location.href.includes(location.pathname) && btnExplaination)
//     {
//         btnExplaination.addEventListener("click", function (e) {
//             modalExplaination.style.display = "flex";
//         });
//
//         crossExplaination.addEventListener("click", function (e) {
//             modalExplaination.style.display = "none";
//         });
//     }
//
//     // MODULE PAR NIVEAU DE DIFFICULTE DE QCM
//
//     let liDifficultyQcmModule = document.querySelectorAll(".listChoiceDifficulties li" );
//     let selectModule = document.getElementById("moduleOption");
//     let inputDifficulty = document.querySelector(".blocChoiceDifficulties input");
//
//     let pErrorSelectM = document.createElement("p");
//     pErrorSelectM.innerText = "Veuillez selectionner un module";
//     pErrorSelectM.className = "pRandomFetch"
//
//     document.querySelector(".blocBorderChoiceDifficulties").after(pErrorSelectM);
//     pErrorSelectM.style.display = "none";
//
//     if (selectModule.value === "null" && inputDifficulty.value === "")
//     {
//         for (let li = 0; li < liDifficultyQcmModule.length; li++)
//         {
//             liDifficultyQcmModule[li].addEventListener("click", function (e){
//                 pErrorSelectM.style.display = "block";
//             })
//         }
//
//         selectModule.addEventListener("change", function (e) {
//
//             let targetModule = e.target.value;
//
//             if ( (targetModule !== "null" || targetModule !== "Veuillez choisir un module") && inputDifficulty.value === "" )
//             {
//                 pErrorSelectM.style.display = "none";
//
//                 for (let li = 0; li < liDifficultyQcmModule.length; li++)
//                 {
//                     liDifficultyQcmModule[li].addEventListener("click", function (e) {
//
//                         if (selectModule.value !== "null")
//                         {
//                             inputDifficulty.value = `${e.target.dataset.difficulty}`;
//                             //ajout bg li
//                             liDifficultyQcmModule[li].style.background = "#ffac8f";
//                             //////si else a été exécuté et ensuite si condition ok , ici exécuté
//                             pErrorSelectM.style.display = "none";
//                             //////////
//
//                             for (let li = 0; li < liDifficultyQcmModule.length; li++)
//                             {
//                                 // suppression bg li
//                                 if (e.target !== liDifficultyQcmModule[li])
//                                 {
//                                     liDifficultyQcmModule[li].style.background = "grey";
//                                 }
//                             }
//                         }
//                         else
//                         {
//                             pErrorSelectM.style.display = "block";
//                         }
//                     });
//                 }
//             } else if ( selectModule.value === "null" && inputDifficulty.value !== "" )
//             {
//                 for (let li = 0; li < liDifficultyQcmModule.length; li++)
//                 {
//                     if ( liDifficultyQcmModule[li].style.background === "rgb(255, 172, 143)" )
//                     {
//                         liDifficultyQcmModule[li].style.background = "grey";
//                         inputDifficulty.value = "";
//                     }
//                 }
//             }
//         });
//     }
//
//     //Stop event  btn form si contrainte module qcm pas respecter
//
//     // déclaration var fetch / form/ contentPart2
//
//     let btnValidModuleDifficulty = document.querySelector(".buttonGenerate");
//     let ulForQuestionsCache = document.querySelector(".backWhite ul");
//     let ulQcmDragAndDropQuestionsCache = document.querySelector(".qcmChoisedMain ul" );
//     let questionsOfficialQcm = document.querySelector(".questionsOfficial ul");
//     let responselabel = document.querySelector(".responselabel");
//     let questionsCustomInDragAndDrop = document.querySelector(".questionsCustom ul" );
//     let letters = ["A", "B", "C", "D", "E", "F"];
//     let qcmNameI = document.querySelector(".blocBeforeValidation .qcmName");
//
//     // pError randomfetch
//     let pErrorRandomFetch = document.createElement("p");
//     document.querySelector(".choiceTypeOfQcm").append(pErrorRandomFetch);
//     pErrorRandomFetch.classList.add("pRandomFetch");
//     pErrorRandomFetch.style.display = "none";
//     //Parametre url fetch
//     let moduleOption = document.getElementById("moduleOption");
//     let selectDifficulty = document.getElementById("qcmDifficulty");
//
//     btnValidModuleDifficulty.addEventListener("click", function (e) {
//         e.preventDefault();
//         // Possibilité d'inclure le if du select Module -> a voir
//
//         if (inputDifficulty.value === "" && moduleOption.value === "null")
//         {
//             pErrorRandomFetch.style.display = "block";
//             // ajout erreur 1
//             pErrorRandomFetch.innerText =
//                 "veuillez selectionner un module et une difficulté ";
//         }
//         else if ( inputDifficulty.value === "null" && moduleOption.value !== "null" )
//         {
//             pErrorRandomFetch.style.display = "block";
//             // ajout erreur 2
//             pErrorRandomFetch.innerText = "veuillez selectionner  une difficulté ";
//         }
//         else
//         {
//             pErrorRandomFetch.style.display = "none";
//
//             ///////////////////////////////////   FETCH    /////////////////////////////////////
//             fetch(
//                 `/instructor/qcms/random_fetch/${moduleOption.value}/${selectDifficulty.value}`,
//                 {  method: "GET" }
//             )
//             .then((response) => {
//                 if (response)
//                 {
//                     let contentPartTwo = document.getElementById("contentPartTwo");
//                     contentPartTwo.style.display = "block";
//
//                     window.scroll({
//                         top: contentPartTwo.getBoundingClientRect().right + 200,
//                         behavior: "smooth",
//                     });
//                     return response.json();
//                 }
//             }) // promesse qui se termine en fin de page
//             .then((data) => {
//                 console.log('random_fetch',data);
//                 let proposalQuestionsCache = document.querySelectorAll(".proposalWordingDiv" );
//
//                 for ( let i = 0; i < data.generatedQcmQuestions.length; i++ )
//                 {
//                     ulForQuestionsCache.innerHTML += `
//                         <li class="questionLi">
//                             <div class="questionWordingDiv">
//                                 <p class="questionWordingP">
//                                     <span class="numeroForm"> ${i + 1}</span>
//                                     ${data.generatedQcmQuestions[i].wording}
//                                 </p>
//                                 <p class="chevronBasP">
//                                     <img src="" alt="Chevron ouvrant" class="chevronBasImg chevron">
//                                 </p>
//                             </div>
//                             <div class="proposalWordingDiv"></div>
//                         </li >
//                     `;
//
//                     for ( let j = 0; j < data.generatedQcmQuestions[i].proposals.length; j++ ) {
//                         proposalQuestionsCache[i].innerHTML += `
//                             <p class="proposalWordingP">
//                                 <span class="numeroProp">${letters[j]}</span>
//                                 ${data.generatedQcmQuestions[forQuestion].proposals[i].wording}
//                             </p>
//                         `;
//                     }
//
//                     // DRAG AND DROP QCM QUESTIONS CACHE
//
//                     ulQcmDragAndDropQuestionsCache.innerHTML += `
//                         <li class="qcmChoisedLi"  draggable="true">
//                             <div class="qcmChoisedLiDiv">
//                                 <div class="questionWordingDiv qcmChoisedQuestionWordingDiv">
//                                     <p class="qcmChoisedTreffleP">
//
//                                     </p>
//                                     <p class="qcmChoisedQuestionWordingP questionWordingP"
//                                         id ="${data.randomQuestion[forQuestion].id}"
//                                     >
//                                       <span> ${forQuestion + 1}</span>
//                                       ${data.randomQuestion[forQuestion].wording}
//                                     </p>
//                                     <p class="qcmChoisedchevronBasP">
//                                         <img src="/build/images/chevron_bas.216a40a5.svg" alt="Chevron ouvrant" class="qcmChoisedchevronBasImg chevron">
//                                     </p>
//                                 </div>
//                                 <div class="qcmChoisedProposalWordingDiv proposalWordingDiv">
//                                 </div>
//                             </div>
//                         </li>
//                         `;
//                     //IMG difficulty random question
//                     let imgQcmChoisedTreffleP = document.querySelectorAll(".questionWordingDiv.qcmChoisedQuestionWordingDiv .qcmChoisedTreffleP ");
//
//                     if (data.randomQuestion[forQuestion].difficulty === 1)
//                     {
//                         imgQcmChoisedTreffleP[
//                             forQuestion
//                             ].innerHTML += `<img src="/build/images/facile.png" alt="Trèfle à trois feuilles" class="qcmChoisedTrefle" data-level="easy">`;
//                     }
//                     else if (data.randomQuestion[forQuestion].difficulty === 2)
//                     {
//                         imgQcmChoisedTreffleP[forQuestion].innerHTML += ` <img src="/build/images/moyen.png" alt="Trèfle à quatre feuilles" class="qcmChoisedTrefle" data-level="medium">`;
//                     }
//                     else if (data.randomQuestion[forQuestion].difficulty === 3)
//                     {
//                         imgQcmChoisedTreffleP[forQuestion].innerHTML += ` <img src="/build/images/difficile.png" alt="Trèfle à quatre feuilles" class="qcmChoisedTrefle" data-level="difficult">`;
//                     }
//
//                     //PROPOSALS  random question
//                     let proposalDragAndDropQuestionsCache = document.querySelectorAll(
//                         ".qcmChoisedProposalWordingDiv.proposalWordingDiv "
//                     );
//
//                     for ( let forProposal = 0; forProposal < data.randomQuestion[forQuestion].proposals.length; forProposal++ )
//                     {
//                         proposalDragAndDropQuestionsCache[forQuestion].innerHTML += `
//                             <div class="qcmChoisedProposalWordingP proposalWordingP">
//                                  <span class="numeroProp nPropPartTwo">${letters[forProposal]}</span>
//                                  ${data.randomQuestion[forQuestion].proposals[forProposal].wording}
//                             </div>
//                             `;
//                     }
//                 }
//
//                 ////////////////// OFFICIAL QUESTIONS
//
//                 for ( let forQuestionOfficial = 0; forQuestionOfficial < data.officialQuestions.length; forQuestionOfficial++ )
//                 {
//                     questionsOfficialQcm.innerHTML += `
//                         <li class="officialQuestionLi" draggable="true">
//                             <div class="OfficialQuestionWordingDiv questionWordingDiv ">
//                                 <div class="qcmChoisedTreffleP"></div>
//                                 <div class="officialQuestionWordingP questionWordingP" id="${ data.officialQuestions[forQuestionOfficial].id }">
//                                     <span> ${forQuestionOfficial + 1}</span>
//                                     ${data.officialQuestions[forQuestionOfficial].wording}
//                                 </div>
//                                 <div class="officialChevronBasP chevronBasP">
//                                     <img src="" alt="Chevron ouvrant" class="officialChevronBasImg chevronBasImg chevron">
//                                 </div>
//                                 <div class="modifyQuestionImgDiv">
//                                     <img src="/build/images/edit.png" alt="bouton modifier" class="modifyQuestionImg" >
//                                 </div>
//                             </div>
//                             <div>
//                                 <div class="OfficialProposalWordingDiv proposalWordingDiv">
//                                     <p class="responselabel">Les réponses</p>
//                                 </div>
//                             </div>
//                         </li>
//                         `;
//                         //IMG difficulty official question
//                         let trefflesForQuestionsOfficial = document.querySelectorAll(".questionsOfficial .OfficialQuestionWordingDiv.questionWordingDiv .qcmChoisedTreffleP" );
//
//                         if (data.officialQuestions[forQuestionOfficial].difficulty === 1)
//                         {
//                             trefflesForQuestionsOfficial[
//                                 forQuestionOfficial
//                                 ].innerHTML += `<img src="/build/images/facile.png" alt="Trèfle à trois feuilles" class="qcmChoisedTrefle" data-level="easy">`;
//                         }
//                         else if ( data.officialQuestions[forQuestionOfficial].difficulty === 2 )
//                         {
//                             trefflesForQuestionsOfficial[
//                                 forQuestionOfficial
//                                 ].innerHTML += `<img src="/build/images/moyen.png" alt="Trèfle à quatre feuilles" class="qcmChoisedTrefle" data-level="medium">`;
//                         }
//                         else if ( data.officialQuestions[forQuestionOfficial].difficulty === 3 )
//                         {
//                             trefflesForQuestionsOfficial[ forQuestionOfficial ].innerHTML += `<img src="/build/images/difficile.png" alt="Trèfle à quatre feuilles" class="qcmChoisedTrefle" data-level="difficult">`;
//                         }
//
//                         //IMG modify
//                         let imgModifyQuestion = document.querySelector(".modifyQuestionImgDiv");
//
//                     if ( data.qcmInstancesByQuestion[ data.officialQuestions[forQuestionOfficial].id ] === 0 )
//                     {
//                         imgModifyQuestion.innerHTML = `<img src="/build/images/edit.png" alt="bouton modifier" class="modifyQuestionImg" >`;
//                     }
//
//                     let proposalsOfficialQcm = document.querySelectorAll( ".questionsOfficial .OfficialProposalWordingDiv.proposalWordingDiv " );
//
//                     //PROPOSALS  random question
//                     for ( let forProposalOfficial = 0; forProposalOfficial < data.officialQuestions[forQuestionOfficial].proposals.length; forProposalOfficial++ )
//                     {
//                         proposalsOfficialQcm[forQuestionOfficial].innerHTML += `
//                             <p class =" officialProposalWordingP proposalWordingP " data-status="${data.officialQuestions[forQuestionOfficial].proposals[forProposalOfficial].isCorrectAnswer}" id="${data.officialQuestions[forQuestionOfficial].proposals[forProposalOfficial].id}" >
//                                 <span class="numeroProp nPropPartTwo"> ${letters[forProposalOfficial]} </span><span class="spanWording" >${data.officialQuestions[forQuestionOfficial].proposals[forProposalOfficial].wording} </span>
//                             </p>
//                         `;
//                     }
//
//                     proposalsOfficialQcm[forQuestionOfficial].innerHTML += `
//                         <div class="questionModify">
//                             <button class="save">enregistrer</button>
//                         </div>
//                     `;
//                 }
//
//                 ///////////////////////////////////// CUSTOM QUESTION
//
//                 if (data.customQuestions.length !== 0)
//                 {
//                     for ( let question = 0; question < data.customQuestions.length; question++ )
//                     {
//                         questionsCustomInDragAndDrop.innerHTML += `
//                             <li class=" officialQuestionLi " draggable="true">
//                                 <div class="OfficialQuestionWordingDiv questionWordingDiv ">
//                                     <div class="qcmChoisedTreffleP"></div>
//                                     <div class="officialQuestionWordingP questionWordingP" id="${data.customQuestions[question].id}">
//                                       <span></span>  ${data.customQuestions[question].wording}
//                                     </div>
//                                     <div class="officialChevronBasP chevronBasP">
//                                         <img src="/build/images/chevron_bas.216a40a5.svg" alt="Chevron ouvrant" class="officialChevronBasImg chevronBasImg chevron">
//                                     </div>
//                                     <div class="modifyQuestionImgDiv ">
//                                         <img src="/build/images/edit.png" alt="bouton modifier" class="modifyQuestionImg">
//                                     </div>
//                                 </div>
//                                 <div>
//                                     <div class="OfficialProposalWordingDiv proposalWordingDiv"></div>
//                                 </div>
//                             </li>
//                         `;
//
//                         //IMG difficulty custom question
//                         let trefflesForQuestionsCustom = document.querySelectorAll(".questionsCustom .OfficialQuestionWordingDiv.questionWordingDiv .qcmChoisedTreffleP");
//
//                         if (data.customQuestions[question].difficulty === 1)
//                         {
//                             trefflesForQuestionsCustom[ question ].innerHTML += `<img src="/build/images/facile.png" alt="Trèfle à trois feuilles" class="qcmChoisedTrefle" data-level="easy">`;
//                         }
//                         else if (data.customQuestions[question].difficulty === 2)
//                         {
//                             trefflesForQuestionsCustom[ question ].innerHTML += ` <img src="/build/images/moyen.png" alt="Trèfle à quatre feuilles" class="qcmChoisedTrefle" data-level="medium">`;
//                         }
//                         else if (data.customQuestions[question].difficulty === 3)
//                         {
//                             trefflesForQuestionsCustom[ question ].innerHTML += ` <img src="/build/images/difficile.png" alt="Trèfle à quatre feuilles" class="qcmChoisedTrefle" data-level="difficult">`;
//                         }
//
//                         // let testi = document.querySelectorAll(".questionsCustom .officialQuestionLi" );
//
//                         let proposalsForQuestionsCustom = document.querySelectorAll(".questionsCustom .OfficialProposalWordingDiv.proposalWordingDiv");
//
//                         //PROPOSALS  random question
//                         if (data.customQuestions[question].proposals)
//                         {
//                             for ( let proposalCustom = 0; proposalCustom < data.customQuestions[question].proposals.length; proposalCustom++ )
//                             {
//                                 proposalsForQuestionsCustom[question].innerHTML += `
//                                     <p class =" officialProposalWordingP proposalWordingP " data-status="${data.customQuestions[question].proposals[proposalCustom].isCorrectAnswer}" id="${data.customQuestions[question].proposals[proposalCustom].id}" >
//                                          <span class="numeroProp nPropPartTwo"> ${letters[proposalCustom]} </span>
//                                          <span class="spanWording" >${data.customQuestions[question].proposals[proposalCustom].wording} </span>
//                                     </p>
//                                 `;
//                             }
//
//                             // responselabel.append(test);
//                             proposalsForQuestionsCustom[question].innerHTML += `
//                                 <div class="questionModify">
//                                     <button class="save">enregistrer</button>
//                                 </div>
//                             `;
//                         }
//                     }
//                 }
//
//                 //qcmName
//                 qcmNameI.innerHTML = `<input type="text" placeholder="Veuillez indiquer un nom pour le qcm" class="qcmNameInput" id="${data.randomQcmByModule}">`;
//
//                 // /////////////////////// SHOW QCM
//
//                 let btnShowQcm = document.querySelector(".btnShowQcm");
//                 let showQcm = document.querySelector(".backWhite");
//
//                 btnShowQcm.addEventListener("click", function (e) {
//                     showQcm.classList.toggle("activeQcm");
//                     if (showQcm.classList.contains("activeQcm"))
//                     {
//                         showQcm.scrollIntoView({ behavior: "smooth" } );
//                     }
//                 });
//
//                 /***************************************************************************/
//
//                 let qcmChoisedMainSide = document.querySelector(".qcmChoisedMain");
//                 if (qcmChoisedMainSide)
//                 {
//                     calcNbrQuestionByLevel(qcmChoisedMainSide);
//                 }
//
//                 let questionsOfficial = document.querySelector(".questionsOfficial");
//                 let buttonQuestionType = document.querySelectorAll(".btnChoiseQuestionType button" );
//
//                 //Event faire apparaitre la partie 2********************************************************************************
//                 let btnToggle = document.querySelector(".btnToggle");
//                 let dragAndDrop = document.querySelector(".dragAndDrop");
//                 let btnCustom = document.querySelector(".btnCustom");
//                 if (btnCustom)
//                 {
//                     btnCustom.addEventListener("click", (e) => {
//                         let partTwo = document.querySelector(".partTwo");
//                         partTwo.classList.remove("displayNone");
//                         dragAndDrop.scrollIntoView({ behavior: "smooth" });
//                         if ((partTwo.style.display = "block"))
//                         {
//                             btnToggle.style.display = "none";
//                         }
//                     });
//                 }
//
//                 //   //////////////NUMERO OF LI TEST QUESTIONS OFFICIELS/ PERSONNALISED
//
//                 let listQuestionsOfficials = document.querySelectorAll(
//                     ".questionsOfficial > ul>li"
//                 );
//
//                 // ////////////// QUESTIONS OFFICIELS/ PERSONNALISED COUNT
//
//                 let btnQuestionsOfficial = document.querySelector(".btnQuestionsOfficial" );
//
//                 // Event sur les button de choix du type de question****************************************************************
//                 buttonQuestionType.forEach((button) => {
//                     button.addEventListener("click", (e) => {
//                         let choiceBtn = e.target;
//                         let otherBtn;
//
//                         if (e.target.previousElementSibling === null)
//                         {
//                             otherBtn = choiceBtn.nextElementSibling;
//                         }
//                         else
//                         {
//                             otherBtn = choiceBtn.previousElementSibling;
//                         }
//
//                         if (choiceBtn.className === "btnQuestionsOfficial")
//                         {
//                             questionsCustom.classList.add("displayNone");
//                             questionsOfficial.classList.remove("displayNone");
//                             choiceBtn.style.backgroundColor = "#FFAC8F";
//                             otherBtn.style.backgroundColor = "#93AD6E";
//                         }
//                         else
//                         {
//                             questionsOfficial.classList.add("displayNone");
//                             questionsCustom.classList.remove("displayNone");
//                             choiceBtn.style.backgroundColor = "#93AD6E";
//                             otherBtn.style.backgroundColor = "#FFAC8F";
//                         }
//                     });
//                 });
//
//                 // Event sur les chevrons *****************************************************************************************
//                 let chevrons = document.querySelectorAll(".chevron");
//
//                 let officialQuestionLi = document.querySelector(".officialQuestionLi" );
//
//                 chevrons.forEach((chevron) => {
//                     chevron.addEventListener("click", (e) => {
//                         let proposalWordingDiv;
//
//                         officialQuestionLi.style.gridGap = "0.2em"; //a voir
//
//                         if (e.target.classList.contains("officialChevronBasImg"))
//                         {
//                             proposalWordingDiv = e.target.parentNode.parentNode.parentNode.lastElementChild.firstElementChild;
//                         } else {
//                             proposalWordingDiv = e.target.parentNode.parentNode.parentNode.lastElementChild;
//                         }
//
//                         proposalWordingDiv.classList.toggle("displayNone"); //a voir
//
//                         if (proposalWordingDiv.classList.contains("displayNone"))
//                         {
//                             officialQuestionLi.style.gridGap = "0";
//                         }
//                         else
//                         {
//                             officialQuestionLi.style.gridGap = "0";
//                         }
//                     });
//                 });
//
//                 let dbValues = [];
//
//                 // event pour modifier une question*********************************************************************************
//                 let modifyQuestionImgDiv = document.querySelectorAll(".modifyQuestionImgDiv" );
//
//                 modifyQuestionImgDiv.forEach((div) => {
//                     div.addEventListener("click", (e) => {
//                         let img = e.target;
//
//                         img.parentElement.previousElementSibling.firstElementChild.src = "/build/images/chevron_haut.acd8ac5d.svg";
//
//                         let divParent = img.parentNode.parentNode;
//                         let question = divParent.querySelector(".questionWordingP");
//
//                         let proposalWordingDiv = img.parentNode.parentNode.parentNode.lastElementChild.lastElementChild;
//
//                         let questionModify = img.parentNode.parentNode.parentNode.lastElementChild.lastElementChild.lastElementChild;
//
//                         // si la div des proposal n'est pas dérouler
//                         if (proposalWordingDiv.classList.contains("displayNone"))
//                         {
//                             proposalWordingDiv.classList.remove("displayNone");
//                         }
//
//                         // Afficher le bouton 'enregistrer'
//                         questionModify.classList.remove("displayNone");
//
//                         // Si la question a 6 proposal
//                         let children = proposalWordingDiv.querySelectorAll(".officialProposalWordingP" );
//
//                         if (children.length <= 6)
//                         {
//                             let buttonAdd = document.createElement("button");
//                             buttonAdd.setAttribute("class", "buttonAddProp");
//                             buttonAdd.setAttribute("id", "buttonAdd");
//                             buttonAdd.innerHTML = "Ajouter une réponse";
//                             buttonAdd.addEventListener("click", (e) => {
//                                 addProposal(e, proposalWordingDiv, children.length);
//                             });
//                             proposalWordingDiv.insertBefore(buttonAdd, questionModify);
//                         }
//
//                         // Bouton cancel
//                         let p = document.createElement("p");
//                         p.classList.add("cancel");
//                         p.innerHTML = "Annuler";
//                         proposalWordingDiv.append(p);
//                         p.addEventListener("click", cancelModifyQuestion);
//
//                         // Get db data
//                         let questionId = question.id;
//                         dbValues.push({
//                             idQuestion: questionId,
//                             proposals: [],
//                         });
//
//                         //Modification de la balise p
//                         for (let i = 0; i < children.length; i++)
//                         {
//                             children[i].classList.add("modifyP");
//                             let id = children[i].id;
//
//                             // textarea
//                             let value = children[i].children[1].textContent.trim();
//                             let textarea = document.createElement("textarea");
//                             textarea.textContent = value;
//                             textarea.setAttribute("name", id);
//
//                             let checkBox = document.createElement("input");
//                             checkBox.setAttribute("type", "checkbox");
//                             checkBox.classList.add("checkBoxIsCorrect");
//
//                             // img
//                             let img = document.createElement("img");
//                             img.setAttribute("src", deleteImg);
//                             img.classList.add("deleteProp");
//                             img.addEventListener("click", deleteProp);
//
//                             children[i].lastChild.remove();
//                             children[i].children[1].remove();
//                             children[i].append(textarea, checkBox);
//                             children[i].append(img);
//
//                             //Save former values of question
//
//                             dbValues.forEach((cel) => {
//                                 if (cel.idQuestion === questionId)
//                                 {
//                                     cel.proposals.push({
//                                         id: id,
//                                         value: value,
//                                     });
//                                 }
//                             });
//                         }
//                         //proposal input précoché voir data-status dans twig
//
//                         for ( let dataNumber = 0; dataNumber < children.length; dataNumber++ )
//                         {
//                             if (children[dataNumber].dataset.status === "true")
//                             {
//                                 let dataChildren = children[dataNumber];
//
//                                 if ( dataChildren.children[2].localName === "input" && dataChildren.children[2].classList.value === "checkBoxIsCorrect" )
//                                 {
//                                     dataChildren.children[2].checked = true;
//                                 }
//                             }
//                         }
//                     });
//                 });
//
//                 // Event Enregistrer une question***********************************************************************************
//                 let saveBtns = document.querySelectorAll(".save");
//
//                 saveBtns.forEach((btn) => {
//                     btn.addEventListener("click", (e) => {
//                         let officialQuestionLi = e.target.parentNode.parentNode.parentNode.parentNode;
//                         let question = officialQuestionLi.querySelector( ".officialQuestionWordingP" );
//                         let questionId = question.id;
//                         let div = e.target.parentNode.parentNode;
//                         let modifyP = div.querySelectorAll(".modifyP");
//                         let module = document.querySelector(".qcmNameInput").id;
//                         let values = {
//                             questionId: questionId,
//                             module: module,
//                             wording: question.textContent.trim(),
//                             proposals: [],
//                         };
//                         let countCorrectAnswer = 0;
//
//                         modifyP.forEach((p) => {
//                             values.proposals.push({
//                                 wording: p.children[1].textContent.trim(),
//                                 id: p.children[1].parentNode.id,
//                                 isCorrectAnswer: p.children[2].checked,
//                             });
//
//                             if (p.children[2].checked)
//                             {
//                                 countCorrectAnswer++;
//                             }
//                         });
//
//                         if (countCorrectAnswer > 1)
//                         {
//                             values.isMultiple = true;
//                         }
//                         else
//                         {
//                             values["isMultiple"] = false;
//                         }
//
//                         //   on passe pas dans le fetch VERIFICATION IMPORTANTE POUR TRANSMISSION DE DONNEES
//                         // `instructor/questions/upDate_fetch/${module}/${questionId}`;
//                         fetch(
//                             `/instructor/questions/upDate_fetch/${module}/${questionId}`,
//                             {
//                                 method: "POST",
//                                 body: JSON.stringify(values), // The data
//                                 headers: {
//                                     "Content-type": "application/json", // The type of data you're sending
//                                 },
//                             }
//                         )
//                         .then((response) => response.json())
//                         .then((data) => console.log(data));
//                     });
//                 });
//
//                 //Event select li to move*******************************************************************************************
//                 let liDiv = document.querySelectorAll( ".officialQuestionLi , .qcmChoisedLi " );
//                 liDiv.forEach((li) => {
//                     li.addEventListener("click", (e) => {
//                         if (e.target.tagName !== "IMG")
//                         {
//                             li.classList.toggle("borderColor");
//                         }
//                     });
//                 });
//
//                 // Event arrow right and left************************************************************************
//                 /*
//               Les questions dans la partie qcm choisie sont aussi dans la liste des questions que se soit custom ou officielle
//               Quand l'utilisateur déplace un question du qcm vers la liste de question est supprimer pour pas avoir de doublons
//            */
//                 let arrowRight = document.querySelector(".arrowRight");
//                 let arrowLeft = document.querySelector(".arrowLeft");
//                 let questionsOfficialSide = document.querySelector(".questionsOfficial");
//                 let qcmChoisedMain = document.querySelector(".qcmChoisedMain");
//
//                 //arrowRight QcmChoised -> questions
//                 if (arrowRight)
//                 {
//                     arrowRight.addEventListener("click", (e) => {
//                         let firstLi = questionsOfficialSide.firstElementChild.firstElementChild;
//                         let ulQuestionsOfficialSide = questionsOfficialSide.firstElementChild;
//                         let qcmChoisedLis = qcmChoisedMain.querySelectorAll(".borderColor");
//                         qcmChoisedLis.forEach((li) => {
//                             //questions officelles
//                             if (li.classList.contains("qcmChoisedLi"))
//                             {
//                                 let allP = li.firstElementChild.children;
//                                 // Bouton modifier la question
//                                 for (let i = 0; i < allP.length; i++)
//                                 {
//                                     if (allP[i].tagName === "DIV")
//                                     {
//                                         allP[i].classList.remove("displayNone");
//                                     }
//                                 }
//
//                                 let qcmChoisedLi = li;
//                                 li.remove();
//                                 ulQuestionsOfficialSide.insertBefore(qcmChoisedLi, firstLi);
//
//                                 ////nombre de questions choisies drop dans questions officielles
//                                 let liQcmChoicedInOfficialQcm = document.querySelectorAll(".questionsOfficial .qcmChoisedLi" );
//                                 if (liQcmChoicedInOfficialQcm)
//                                 {
//                                     for (let i = 0; i < liQcmChoicedInOfficialQcm.length; i++)
//                                     {
//                                         btnQuestionsOfficial.innerHTML = `Questions officielles :${ listQuestionsOfficials.length + liQcmChoicedInOfficialQcm.length }`;
//                                     }
//                                 }
//                             }
//                             else
//                             {
//                                 li.remove();
//                             }
//                         });
//
//                         let elementSelect = document.querySelectorAll(".borderColor");
//
//                         elementSelect.forEach((el) => {
//                             el.classList.remove("borderColor");
//                         });
//
//                         if (qcmChoisedMain)
//                         {
//                             calcNbrQuestionByLevel(qcmChoisedMain);
//                         }
//                     });
//                 }
//
//                 //arrowLeft qcmChoised <- question
//                 if (arrowLeft)
//                 {
//                     arrowLeft.addEventListener("click", (e) => {
//                         let firstLi = qcmChoisedMain.firstElementChild.firstElementChild;
//                         let ulQcmChoisedSide = qcmChoisedMain.firstElementChild;
//                         let officialLis = questionsOfficialSide.querySelectorAll(".borderColor");
//                         officialLis.forEach((li) => {
//                             //questions officelles
//                             if (li.classList.contains("officialQuestionLi"))
//                             {
//                                 let allP = li.firstElementChild.children;
//                                 // Bouton modifier la question
//                                 for (let i = 0; i < allP.length; i++)
//                                 {
//                                     //etant donné que les p sont transformées en div donc contition lié au class pour sélectionner la bonne div
//                                     if (allP[i].classList === "modifyQuestionImgDiv")
//                                     {
//                                         allP[i].classList.add("displayNone");
//                                     }
//                                 }
//
//                                 let officialLi = li;
//                                 li.remove();
//                                 ulQcmChoisedSide.insertBefore(officialLi, firstLi);
//                                 //count questions officiel dans question choisie
//                                 // FAIRE UNE FONCTION TOTALE DE listQuestionsOfficials.length +liQcmChoicedInOfficialQcm.length dans la fonction de déplacement vers ldroite et la rappeler en bas
//                                 //
//                                 let liQcmChoicedInOfficialQcm = document.querySelectorAll(".qcmChoisedLi");
//                                 let liQcmOfficialInQcmChoiced = document.querySelectorAll(".qcmChoisedMain .officialQuestionLi" );
//
//                                 if (liQcmChoicedInOfficialQcm)
//                                 {
//                                     for (let i = 0; i < liQcmChoicedInOfficialQcm.length; i++) {
//                                         btnQuestionsOfficial.innerHTML = `
//                                             Questions officielles :${ listQuestionsOfficials.length + liQcmChoicedInOfficialQcm.length - liQcmOfficialInQcmChoiced.length }
//                                         `;
//                                     }
//                                 }
//                             }
//                         });
//
//                         let elementSelect = document.querySelectorAll(".borderColor");
//                         elementSelect.forEach((el) => {
//                             el.classList.remove("borderColor");
//                         });
//                         if (qcmChoisedMain)
//                         {
//                             calcNbrQuestionByLevel(qcmChoisedMain);
//                         }
//                     });
//                 }
//
//                 /**********************************************************************************************************************/
//                 function addProposal(e, parent, lengthProp) {
//                     let pProp = document.createElement("p");
//                     pProp.classList.add(
//                         "officialProposalWordingP",
//                         "proposalWordingP",
//                         "modifyP"
//                     );
//
//                     let textarea = document.createElement("textarea");
//                     textarea.setAttribute("name", "newProp");
//
//                     let checkBox = document.createElement("input");
//                     checkBox.setAttribute("type", "checkbox");
//                     checkBox.classList.add("checkBoxIsCorrect");
//
//                     let img = document.createElement("img");
//                     img.setAttribute("src", deleteImg);
//                     img.classList.add("deleteProp");
//                     img.addEventListener("click", deleteProp);
//
//                     let span = document.createElement("span");
//                     span.classList.add("numeroProp", "nPropPartTwo");
//
//                     // Trouver la lettre
//                     let alphabet = ["A", "B", "C", "D", "E", "F"];
//                     let end = parseInt(lengthProp - 2, 10) + 1;
//                     let begin = lengthProp - 2;
//                     let letter = alphabet.slice(begin, end);
//                     span.innerHTML = letter;
//
//                     pProp.append(span, textarea, checkBox, img);
//                     parent.insertBefore(pProp, e.target);
//
//                     let children = parent.querySelectorAll(".modifyP");
//
//                     //Si < 6 réponses
//                     if (children.length >= 6)
//                     {
//                         e.target.classList.add("displayNone");
//                         let message = document.createElement("p");
//                         message.classList.add("message");
//                         message.textContent = "Le nombre maximal de 6 réponses possibles pour une question a été atteint";
//                         parent.append(message);
//                     }
//                 }
//
//                 function deleteProp(e) {
//                     let div = e.target.parentNode.parentNode;
//                     e.target.parentNode.remove();
//                     let p = div.querySelectorAll(".modifyP");
//                     let message;
//                     //Si reponse < 6
//                     if (typeof message !== undefined)
//                     {
//                         message = div.querySelector(".message");
//                         message.remove();
//                         let buttonAddProp = div.querySelector(".buttonAddProp");
//                         buttonAddProp.classList.remove("displayNone");
//                     }
//
//                     //Réactualiser les lettres des réponses
//                     let alphabet = ["A", "B", "C", "D", "E", "F"];
//                     let spanLetters = div.querySelectorAll(".nPropPartTwo");
//                     let count = 0;
//                     spanLetters.forEach((p) => {
//                         p.innerHTML = alphabet[count];
//                         count++;
//                     });
//                 }
//
//                 function cancelModifyQuestion(e) {
//                     let parent = e.target.parentNode.parentNode;
//                     let parentQuestion = e.target.parentNode.parentNode.parentNode;
//                     let question = parentQuestion.querySelector(".questionWordingP");
//                     let proposalWordingDiv = parent.querySelector(".proposalWordingDiv" );
//                     let divParentP = proposalWordingDiv.parentNode;
//
//                     //Css
//                     proposalWordingDiv.style.flexDirection = "column";
//                     let nPropPartTwo = proposalWordingDiv.querySelectorAll(".nPropPartTwo");
//                     nPropPartTwo.forEach((nbr) => {
//                         nbr.style.padding = "2px 12px";
//                     });
//                     parentQuestion.firstElementChild.lastElementChild.classList.remove(
//                         "displayNone"
//                     );
//
//                     proposalWordingDiv.remove();
//
//                     let div = document.createElement("div");
//                     div.classList.add(
//                         "proposalWordingDiv",
//                         "OfficialProposalWordingDiv"
//                     );
//                     divParentP.append(div);
//
//                     let id = question.dataset.id;
//                     let questionToEdit = dbValues.filter((question) => {
//                         return question.idQuestion === id;
//                     });
//                     questionToEdit[0].proposals.forEach( function callback( value, index) {
//                         let span = document.createElement("span");
//                         span.classList.add("numeroProp", "nPropPartTwo");
//
//                         // Trouver la lettre
//                         let alphabet = ["A", "B", "C", "D", "E", "F"];
//                         let end = parseInt(index, 10) + 1; // 4 +1 = 5    '4' + 1 = 41
//                         let begin = index;
//                         let letter = alphabet.slice(begin, end);
//                         span.innerHTML = letter;
//
//                         let p = document.createElement("p");
//                         p.classList.add("officialProposalWordingP", "proposalWordingP");
//                         let spanInP = document.createElement("span");
//                         spanInP.innerHTML = value.value;
//                         p.dataset.id = value.id;
//                         p.append(span, spanInP);
//                         div.append(p);
//                     });
//                 }
//
//
//             });
//         }
//     });
// };

// TODO
// info bouton modif une question retiré
// faire un event au survol pour signifier ce changement


// REFACTO TOTALE DU FICHIER
let moduleOption, difficultyOptions, selectedModule, selectedDifficulty, generateQcmBtn, generationErrorBlock;
let generatedQcmResumeBlock, showGeneratedQcmResumeBtn, validateQcmButtonWhitoutChange;
let personalizeQcmBlock, personalizeQcmBtn, pickableOfficialQuestionsList, pickableCustomQuestionsList, pickedQuestionsList;
let pickableCustomQuestionsBnt, pickableOfficialQuestionsBnt, moveTopickableQuestionsListBtn, moveTopickedQuestionsListBtn;
let qcmValidationBtn

const letters = ['A', 'B', 'C', 'D', 'E', 'F'];
const difficultyImages =
    [
        'trefle_facile_bon_vert.1ad9fa1e.png',
        'trefle_moyen_bon_vert.37c52905.png',
        'trefle_difficile_bon_vert.73839ef1.png'
    ]
const levels = ['easy', 'medium', 'difficult']

function generateRandomQcm()
{
    if( selectedModule && ( selectedDifficulty === '1' || selectedDifficulty === '2' || selectedDifficulty === '3' ) )
    {
        generationErrorBlock.innerText = '';
        fetchGeneratedQcm();
    }
    else
    {
        displayGenerationError(generationErrorBlock, 'Vueillez selectionner un module ET une difficulté');
    }
}

function displayGenerationError(element, text)
{
    element.classList.remove('displayNone')
    element.innerText = text;
}

function fetchGeneratedQcm()
{
    fetch(`/instructor/qcms/random_fetch/${selectedModule}/${selectedDifficulty}`, {method: 'GET'})
        .then( response => response.json() )
        .then( data => {
            let generatedQcmQuestions = Object.values(data.generatedQcmQuestions);
            let officialQuestions = Object.values(data.officialQuestions);
            let instructorQuestions = Object.values(data.instructorQuestions);
            fillGeneratedQcmResumeBlock( generatedQcmQuestions );
            fillQcmPersonalizer( officialQuestions, instructorQuestions, generatedQcmQuestions )
            generatedQcmResumeBlock.classList.remove('displayNone')
            smoothScrollTo('#generatedQcmResumeBlock');
        })
}

function fetchCreateQcmPerso()
{
    qcmValidationBtn.addEventListener("click", (e) => {
        let questionsSelect = {};
        let choosenQcmName = document.querySelector("#choosenQcmName").value;

        if (!choosenQcmName)
        {
            let errorMessageChoseNameQcm = document.getElementById('errorMessageChoseNameQcm')
            displayGenerationError(errorMessageChoseNameQcm, "Veuillez choisir un nom pour ce qcm")
        }

        let module = selectedModule;
        let isPublic = document.getElementById("isPublicInput").checked;
        /***********/
        let qcmChoisedLevel = document.getElementById("qcmChoisedLevel").textContent.trim();

        questionsSelect = {
            name: choosenQcmName,
            level: qcmChoisedLevel,
            module: module,
            isPublic: isPublic,
            questions: [],
        };

        let questions = pickedQuestionsList.querySelectorAll(
            ".qcmChoisedLi"
        );

        questions.forEach((question) => {
            let level = question.querySelector('.qcmChoisedTrefle').dataset.level;
            let wording = question.querySelector('.questionWordingP').lastChild.textContent.trim();
            let id = question.dataset.questionid
            let proposals = Array.from(question.querySelector('.proposalWordingDiv'));

            questionsSelect["questions"].push({
                id: id,
                level: level,
                wording: wording,
                proposals: [],
            });

            proposals.forEach((prop) => {
                questionsSelect["proposals"].push({
                    wording: prop.children[1].textContent.trim(),
                    id: prop.dataset.proposalid,
                });
            });
        });

        fetch(`/instructor/qcms/create_fetch/${module}`, {
            method: "POST",
            headers: {
                "Content-type": "application/json", // The type of data you're sending
            },
            body: JSON.stringify(questionsSelect), // The data
        })
            .then((res) => res.json())
            .then((result) => {
                if (result === "ok") {
                    window.location.href =
                        "https://127.0.0.1:8000/instructor";
                }
            });
    });
}

function fillGeneratedQcmResumeBlock( questions )
{
    let questionsList = generatedQcmResumeBlock.querySelector('ul');
    questions.forEach( (question, i) => {
        let li = document.createElement('li');
        li.classList.add('questionLi');
        li.innerHTML = `
            <div class="questionWordingDiv">
                <p class="questionWordingP">
                    <span class="numeroForm"> ${i + 1}</span>
                    ${question.question.wording}
                </p>
            </div>
            <div class="proposalWordingDiv"></div>
        `;
        questionsList.append(li);
        let proposalsList = questionsList.querySelectorAll('.proposalWordingDiv')[i];
        question.proposals.forEach( ( proposal, index ) => {
            let p = document.createElement('p');
            p.classList.add('proposalWordingP');
            p.innerHTML = `
                <span class="numeroProp">${letters[index]}</span>
                ${proposal.wording}
            `;
            proposalsList.append(p);
        })
    })

    showGeneratedQcmResumeBtn.addEventListener('click', displayGeneratedQcmQuestionsList);
    personalizeQcmBtn.addEventListener('click', displayQcmPersonalizer);
    // TODO Voir Avec Matthieu pk ça ne fonciton pas
    // validateQcmButtonWhitoutChange.addEventListener('click', fetchCreateQcmPerso)
}

function displayQcmPersonalizer(e)
{
    personalizeQcmBlock.classList.remove('displayNone')
    smoothScrollTo("#personalizeQcmBlock")

    let qcmPersonalizerButtonShow = personalizeQcmBlock.querySelector('#qcmPersonalizerButtonShow');
    qcmPersonalizerButtonShow.addEventListener('click', (e)=>{
        let containerQcmPersonalizer = personalizeQcmBlock.querySelector('#containerQcmPersonalizer');
        containerQcmPersonalizer.classList.toggle("displayNone");

        if( !containerQuestionsList.className.contains('displayNone') )
        {
            e.target.innerText = 'Masquer la partie personalisation du qcm';
        }
        else if( containerQuestionsList.className.contains('displayNone') )
        {
            e.target.innerText = 'Voir la partie personalisation du qcm';
        }
    })
}

function displayGeneratedQcmQuestionsList(e)
{
    let containerQuestionsList = generatedQcmResumeBlock.querySelector('#containerQuestionsList');
    containerQuestionsList.classList.toggle("displayNone");

    if( !containerQuestionsList.classList.contains('displayNone') )
    {
        e.target.innerText = 'Masquer les questions';
    }
    else if( containerQuestionsList.classList.contains('displayNone') )
    {
        e.target.innerText = 'Voir les questions';
    }
    pickedQuestionsList = document.querySelector('#pickedQuestionsList');
    calcNbrQuestionByLevel(pickedQuestionsList)
}

function fillQcmPersonalizer( officialQuestions ,customQuestions, pickedQuestions )
{
    for (let i = 0; i < officialQuestions.length; i++)
    {
        createQuestionLi(officialQuestions[i], i, pickableOfficialQuestionsList, true );
    }

    for (let i = 0; i < customQuestions.length; i++)
    {
        createQuestionLi(customQuestions[i], i, pickableCustomQuestionsList, false);
    }

    for (let i = 0; i < pickedQuestions.length; i++)
    {
        createQuestionLi(pickedQuestions[i], i, pickedQuestionsList, true );
    }

    let chevrons = document.querySelectorAll('.qcmChoisedLi .chevron')
    for (let i = 0; i < chevrons.length; i++)
    {
        chevrons[i].addEventListener('click', function(e){
            let proposalBlock = this.closest('.qcmChoisedLi').querySelector('.proposalWordingDiv');
            proposalBlock.classList.toggle('displayNone')
            chevrons[i].classList.toggle('rotate')
        })
    }
}

function createQuestionLi( sourceQuestion, questionIndex, elementsList, isOfficial )
{
    const {question, proposals, isEditable} = sourceQuestion;
    let modifyIcon =  isEditable ? '<div class="modifyQuestionImgDiv"><img src="/build/images/modifier.cd0cd657.png" class="modifyQuestionImg" alt="bouton modifier"/></div>' : '';
    let li = document.createElement('li');
    li.classList.add('qcmChoisedLi');
    li.dataset.questionid = question.id;
    li.dataset.isofficial = isOfficial
    li.innerHTML = `
        <div class="qcmChoisedLiDiv">
            <div class="questionWordingDiv qcmChoisedQuestionWordingDiv">
                <p class="qcmChoisedTreffleP">
                    <img 
                        src="/build/images/${difficultyImages[question.difficulty - 1]}" 
                        alt="trefle difficulté ${question.difficulty}"
                        data-level="${levels[question.difficulty - 1]}"
                        class="qcmChoisedTrefle"
                    >
                </p>
                <p class="qcmChoisedQuestionWordingP questionWordingP" 
                    data-questionid="${ question.id }" 
                    data-isofficial="${isOfficial}">
                    <span>${questionIndex + 1}</span>  
                    ${question.wording}
                </p>
                <p class="qcmChoisedchevronBasP">
                    <img src="/build/images/chevron_bas.de9c9a9d.png" alt="Chevron ouvrant" class="qcmChoisedchevronBasImg chevron">
                </p>
                ${modifyIcon}
            </div>
            <div class="qcmChoisedProposalWordingDiv proposalWordingDiv"></div>
        </div>
    `;

    elementsList.append(li)
    let proposalsList = elementsList.querySelectorAll('.proposalWordingDiv')[questionIndex];
    proposals.forEach( (proposal, index) => {
        let div = document.createElement('div');
        div.classList.add('proposalWordingP');
        div.dataset.proposalid = `${proposal.id}`
        div.innerHTML = `
            <span class="numeroProp nPropPartTwo">${letters[index]}</span>
            ${proposal.wording}
        `;
        proposalsList.append(div);
    });
    let qcmChoisedQuestionWordingP = li.querySelector(".qcmChoisedQuestionWordingP")
    qcmChoisedQuestionWordingP.addEventListener('click', chooseQuestionToMove)
}

function smoothScrollTo( targetElement )
{
    document.querySelector( targetElement ).scrollIntoView({
        behavior: "smooth"
    })
}

function chooseQuestionToMove(e)
{
    let li = e.target.closest('.qcmChoisedLi')
    li.classList.toggle('chosenElement')
}

function moveTo(e)
{
    let questionsChosen;
    let destination;
    if (e.target.id === 'moveToPickedQuestionsList')
    {
        let officialQuestionsChosen = Array.from(pickableOfficialQuestionsList.querySelectorAll('.chosenElement'))
        let customQuestionsChosen = Array.from(pickableCustomQuestionsList.querySelectorAll('.chosenElement'))
        questionsChosen = customQuestionsChosen.concat(officialQuestionsChosen)
        destination = "picked"
    }
    else if (e.target.id === 'moveToPickableQuestionsList')
    {
        questionsChosen = pickedQuestionsList.querySelectorAll('.chosenElement')
        destination = "pickable"
    }
    questionsChosen.forEach( question => {
        question.classList.remove('chosenElement')
        question.remove()
        if (destination === "pickable")
        {
            if (question.dataset.isofficial === "true")
            {
                pickableOfficialQuestionsList.append(question)
            }else
            {
                pickableCustomQuestionsList.append(question)
            }
        }else
        {
            pickedQuestionsList.append(question)
            calcNbrQuestionByLevel(pickedQuestionsList)
        }
    })
}

function displayPickableQuestionList(e)
{
    if (e.target.id === "pickableCustomQuestionsBnt")
    {
        pickableOfficialQuestionsList.parentNode.classList.add('displayNone')
        pickableCustomQuestionsList.parentNode.classList.remove('displayNone')

        e.target.classList.add('btnQuestionsActive')
        pickableOfficialQuestionsBnt.classList.remove('btnQuestionsActive')
    }
    else if (e.target.id === "pickableOfficialQuestionsBnt")
    {
        pickableOfficialQuestionsList.parentNode.classList.remove('displayNone')
        pickableCustomQuestionsList.parentNode.classList.add('displayNone')

        e.target.classList.add('btnQuestionsActive')
        pickableCustomQuestionsBnt.classList.remove('btnQuestionsActive')
    }
}

function calcNbrQuestionByLevel(side)
{
    let easyLevel = 0;
    let mediumLevel = 0;
    let difficultyLevel = 0;

    let questions = side.querySelectorAll(".qcmChoisedTrefle");
    questions.forEach((question) => {
        if (question.dataset.level === "easy") {
            easyLevel++;
        } else if (question.dataset.level === "medium") {
            mediumLevel++;
        } else {
            difficultyLevel++;
        }
    });

    let pEasy = document.getElementById("easy");
    pEasy.innerHTML = easyLevel;
    let pMedium = document.getElementById("medium");
    pMedium.innerHTML = mediumLevel;
    let pDifficult = document.getElementById("difficulty");
    pDifficult.innerHTML = difficultyLevel;

    let qcmChoisedLevel = document.getElementById("qcmChoisedLevel");
    if ( difficultyLevel > mediumLevel && difficultyLevel > mediumLevel )
    {
        qcmChoisedLevel.innerHTML = "Difficile";
    }
    else if ( mediumLevel > difficultyLevel && mediumLevel > easyLevel )
    {
        qcmChoisedLevel.innerHTML = "Moyen";
    }
    else if ( easyLevel > mediumLevel && easyLevel > difficultyLevel )
    {
        qcmChoisedLevel.innerHTML = "Facile";
    }
    else
    {
        qcmChoisedLevel.innerHTML = "Moyen";
    }
}

function displayModal()
{
    let modalExplaination = document.querySelector(".blocModalExplaination");
    let crossExplaination = document.querySelector(".modal > img");
    let btnExplaination = document.querySelector("#showModalExplanationBtn");

        btnExplaination.addEventListener("click", function (e) {
            modalExplaination.style.display = "flex";
        });

        crossExplaination.addEventListener("click", function (e) {
            modalExplaination.style.display = "none";
        });
}

document.addEventListener('DOMContentLoaded', function(){
    moduleOption = document.querySelector('#moduleOption');
    difficultyOptions = document.querySelectorAll('#difficultyOption li');
    generateQcmBtn = document.querySelector('#generateQcmButton');
    generationErrorBlock = document.querySelector('#generationError');
    generatedQcmResumeBlock = document.querySelector('#generatedQcmResumeBlock');
    showGeneratedQcmResumeBtn = document.querySelector('#showGeneratedQcmResumeButton');

    personalizeQcmBtn = document.querySelector('#personalizeQcmButton');
    personalizeQcmBlock = document.querySelector('#personalizeQcmBlock');

    validateQcmButtonWhitoutChange = document.querySelector('#validateQcmButtonWhitoutChange')

    pickableOfficialQuestionsList = document.querySelector('#pickableOfficialQuestionsList');
    pickableCustomQuestionsList = document.querySelector('#pickableCustomQuestionsList');

    pickedQuestionsList = document.querySelector('#pickedQuestionsList');

    pickableCustomQuestionsBnt = document.querySelector('#pickableCustomQuestionsBnt');
    pickableOfficialQuestionsBnt = document.querySelector('#pickableOfficialQuestionsBnt');

    moveTopickableQuestionsListBtn = document.querySelector('#moveToPickableQuestionsList');
    moveTopickedQuestionsListBtn = document.querySelector('#moveToPickedQuestionsList');

    qcmValidationBtn = document.querySelector("#validationCreationQcmBnt");

    displayModal()


    moduleOption.addEventListener('change', function() {
        selectedModule = this.value;
    });

    for( let i = 0; i < difficultyOptions.length; i++ )
    {
        difficultyOptions[i].addEventListener('click', function(e){
            selectedDifficulty = this.dataset.difficulty;

            Array.from(e.target.parentNode.children).forEach( li => {
                li.style.backgroundColor = '#616161'
            } )

            e.target.style.backgroundColor = '#93ad6e'
        })
    }

    generateQcmBtn.addEventListener('click', generateRandomQcm);

    moveTopickableQuestionsListBtn.addEventListener('click', moveTo)
    moveTopickedQuestionsListBtn.addEventListener('click', moveTo)

    pickableCustomQuestionsBnt .addEventListener('click', displayPickableQuestionList)
    pickableOfficialQuestionsBnt .addEventListener('click', displayPickableQuestionList)

    qcmValidationBtn.addEventListener('click', fetchCreateQcmPerso)
})