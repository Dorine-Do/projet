let createQuestionFormSubmit, createQuestionFormErrors;
let isCreateQuestionFormValidable = false;

function checkCreateQuestionForm(e)
{
    e.preventDefault();
    if( !isCreateQuestionFormValidable )
    {
        let errorMessages = document.querySelectorAll('.errorMsg');
        let errorcontainers = document.querySelectorAll('.errorBorder');

        errorMessages.forEach( errorMessage => errorMessage.remove() );
        errorcontainers.forEach( errorContainer => errorContainer.classList.remove('errorBorder') );

        let questionWordingInput = document.querySelector( '#cke_create_question_wording iframe' ).contentWindow.document.querySelector('body > p');
        let proposalsIFrames        = document.querySelectorAll( '#listProposal iframe' );
        let proposalsWordingInputs  = [];
        proposalsIFrames.forEach( proposalIFrame => proposalsWordingInputs.push( proposalIFrame.contentWindow.document.querySelector('body > p') ) );
        let proposalsIsRightAnswers = document.querySelectorAll( '#listProposal .isCorrect:checked' );

        createQuestionFormErrors = [];

        if( questionWordingInput.innerHTML === '<br>' )
        {
            createQuestionFormErrors.push({
                errorMessage: 'L\'intitulé de la question ne peut pas être vide',
                errorParentContainer: parent.document.querySelector('#cke_create_question_wording')
            });
            parent.document.querySelector('#cke_create_question_wording').parentElement.classList.add('errorBorder');
        }
        else
        {
            if( parent.document.querySelector('#cke_create_question_wording').classList.contains('errorBorder') )
            {
                parent.document.querySelector('#cke_create_question_wording').classList.remove('errorBorder');
            }
        }

        for( let i = 0; i < proposalsWordingInputs.length; i++ )
        {
            proposalWordingInput = proposalsWordingInputs[i];
            let proposalContainer = parent.document.querySelectorAll( '#listProposal .liProposal' )[i];
            if( proposalWordingInput.innerHTML === '<br>' )
            {
                createQuestionFormErrors.push({
                    errorMessage: 'L\'intitulé de la réponse ne peut pas être vide',
                    errorParentContainer: proposalContainer
                });

                proposalContainer.classList.add('errorBorder');
            }
            else
            {
                if( proposalContainer.classList.contains('errorBorder') )
                {
                    proposalContainer.classList.remove('errorBorder');
                }
            }
        }

        if( proposalsWordingInputs.length > 6 )
        {
            let listProposal = document.querySelector( '#listProposal').parentElement;
            createQuestionFormErrors.push({
                errorMessage: 'Une question ne peux comporter plus de six réponses',
                errorParentContainer: listProposal
            });
            listProposal.classList.add('errorBorder');
        }
        else
        {
            if( listProposal.classList.contains('errorBorder') )
            {
                listProposal.classList.remove('errorBorder');
            }
        }

        if( proposalsWordingInputs.length < 2 )
        {
            let listProposal = document.querySelector( '#listProposal').parentElement;
            createQuestionFormErrors.push({
                errorMessage: 'Une question doit comporter au moins deux réponses',
                errorParentContainer: listProposal
            });
            listProposal.classList.add('errorBorder');
        }
        else
        {
            if( listProposal.classList.contains('errorBorder') )
            {
                listProposal.classList.remove('errorBorder');
            }
        }

        if( proposalsIsRightAnswers.length === 0 )
        {
            let listProposal = document.querySelector( '#listProposal').parentElement;
            createQuestionFormErrors.push({
                errorMessage: 'Une question doit comporter au moins une bonne réponse',
                errorParentContainer: listProposal
            });
            listProposal.classList.add('errorBorder');
        }
        else
        {
            if( listProposal.classList.contains('errorBorder') )
            {
                listProposal.classList.remove('errorBorder');
            }
        }

        if( createQuestionFormErrors.length < 1 )
        {
            isCreateQuestionFormValidable = true;
            checkCreateQuestionForm();
        }
        else
        {
            createQuestionFormErrors.forEach( error => {
               let errorTxtElement = document.createElement('span');
                errorTxtElement.classList.add('errorMsg');
                errorTxtElement.innerText = error.errorMessage;
                error.errorParentContainer.prepend( errorTxtElement );

            });
        }
    }
    else
    {
        document.querySelector( 'input[name="create_question"]' ).submit();
    }
}

document.addEventListener('DOMContentLoaded', function(){

    createQuestionFormSubmit = document.querySelector( 'form[name="create_question"] button.valid' );
    createQuestionFormSubmit.addEventListener('click', checkCreateQuestionForm)

});