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
            let div_question = e.target.parentElement.parentElement.parentElement.parentElement.childNodes[5];
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
                        p.innerText = letter;

                        p_prop = document.createElement("div");
                        p_prop.innerText = proposal.wording;
                        p_prop.classList.add("blocContentProposal");

                        div_js.append(p, p_prop);
                        // INSERTION DU PLETTER DEVANT LE PWORDING
                        p_prop.insertBefore(p, p_prop.lastElementChild);
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
                div_js.innerText = "";
                e.target.dataset.status = false;

                chevron.classList.remove("closed");
            }
        });
    });
    ////////////////////
    // DECLARATION DE VARIABLE

    let liBtnQcm = document.querySelectorAll(".liBtnQcm  ");
    let liBtnQcmOnly = document.querySelector(".liBtnQcm  ");
    let ulBtnQcm = document.querySelector(".listBtnQcm  ");
    let questionsLi = document.querySelectorAll(".listQuestions li");
    let questionslist = document.querySelector(".listQuestions ");
    let forBtnQcm;
    let blocUlQcm = document.querySelector(".blocQcm .divQcm ");
    let blocUlQuestion = document.querySelector(
        ".bloc-toggle-ul-question .divQuestions"
    );


    // SELECTION UNIQUE DES BOUTONS QCMS ET DISPLAY DE LA LISTE DES QUESTIONS LIEES

    let questionsCacheDefaultId = JSON.parse(liBtnQcmOnly.dataset.questionsCache);

    // TODO refactoriser avec uniquement la numerotation des spans car li par defaut fait voir twig
    for (
        let forWording = 0;
        forWording < questionsCacheDefaultId.length;
        forWording++
    ) {
        questionsLi[forWording].innerHTML = `<span>${forWording + 1}</span>${
            questionsCacheDefaultId[forWording].wording
        }`;
    }

    for (forBtnQcm = 0; forBtnQcm < liBtnQcm.length; forBtnQcm++) {
        //NUMEROTATION SPAN PAR DeFAUT
        //AFFICHAGGE PAR DEFAUT SELON ID PAR DEFAUT

        //BACKGROUND BTN PAR DEFAUT

        if (liBtnQcmOnly.dataset.id == liBtnQcm[0].dataset.id) {
            liBtnQcmOnly.classList.add("defaultBg");
        }

        //LI REMPLISSAGE QUESTION AU CLIC

        liBtnQcm[forBtnQcm].addEventListener("click", function (e) {
            let questionsCache = JSON.parse(this.dataset.questionsCache);
            let dataQcmId = e.target.dataset.id;
            //NUMEROTATION SPAN APReS REMPLISSAGE LI
            for (
                let forWording = 0;
                forWording < questionsCache.length;
                forWording++
            ) {
                questionsLi[forWording].innerHTML = `<span>${forWording + 1}</span>${
                    questionsCache[forWording].wording
                }`;
            }

            // FAIRE UNE BOUCLE DE MON JSON ? PARSER LA VALEUR ET REMPLACER EN JS LES VALEURS DU LI DU TEMPLATE PAR CELLE CORRESPONDANTE DANS LE CACHE

            for (forBtnQcm = 0; forBtnQcm < liBtnQcm.length; forBtnQcm++) {
                console.log(liBtnQcm[forBtnQcm].dataset.id)
                console.log(this.dataset.id)

                if (this.dataset.id == liBtnQcm[forBtnQcm].dataset.id) {

                    liBtnQcm[forBtnQcm].classList.add("activeLi");


                    if (
                        (this.dataset.id == liBtnQcm[forBtnQcm].dataset.id) !==
                        liBtnQcm[0].dataset.id
                    ) {
                        liBtnQcmOnly.classList.remove("defaultBg");
                    }
                } else {
                    liBtnQcm[forBtnQcm].classList.remove("activeLi");
                }
            }
        });
    }
    //calcule height pour scroll active
    //Condition pour que la suite des instructions ne blocque pas sur les autres pages
    if (blocUlQcm) {
        let blocUlQcmHeight = blocUlQcm.getBoundingClientRect().height;

        blocUlQcm.addEventListener("mouseover", function () {
            if (ulBtnQcm) {
                let ulQcmHeight = ulBtnQcm.getBoundingClientRect().height;
                if (ulQcmHeight > blocUlQcmHeight + 10) {
                    blocUlQcm.classList.add("scroll_active");
                }
            }
        });
    }
    if (blocUlQuestion) {
        let blocUlQuestionHeight = blocUlQuestion.getBoundingClientRect().height;
        if (questionslist) {
            let ulQuestionHeight = questionslist.getBoundingClientRect().height;
            blocUlQuestion.addEventListener("mouseover", function () {
                if (ulQuestionHeight > blocUlQuestionHeight + 5) {
                    blocUlQuestion.classList.add("scroll_active");
                }
            });
        }
    }

    ////////////////////
    // HOVER DECLENCHEMENT SCROLL-Y

    blocUlQcm.addEventListener("mouseout", function () {
        blocUlQcm.classList.remove("scroll_active");
    });

    blocUlQuestion.addEventListener("mouseout", function () {
        blocUlQuestion.classList.remove("scroll_active");
    });
};
