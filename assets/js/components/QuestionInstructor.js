import React, {Component} from 'react';


class DisplayQuestionsInstructor extends React.Component {
    constructor(props) {
        super(props);
        this.state =
            {
                questions: [],
                proposals: [],
                word : "heelo"
            };
    }
    componentDidMount() {
        this.getUsers();
    }

    getUsers() {
        let myHeaders = new Headers({
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        });
        let myInit = {
            method: 'GET',
            headers: myHeaders,
            mode: 'cors',
            cache: 'default' };

        fetch(`/instucteur/questions`,myInit)
            .then(response => {
                if( response.ok){
                    return response.json()
                }else{
                    console.log(response)
                }
            })
            .then(questionsData => {
                this.setState({ proposals: questionsData.proposals})
                this.setState({ questions: questionsData.questions})
                console.log(questionsData.proposals);
            })
    }
    render() {
        console.log(this.state.word)
        return (
            <div>
                <h1>Hello, world!</h1>
                <h2>It is </h2>
                { this.state.proposals.map(proposal =>
                    <li key={proposal.id}>{proposal.wording}</li>
                )}
            </div>
        );
    }
}

function QuestionsINstructor(props){
    return (
        <div>
            <h1>Hello, world!</h1>
            <h2>It is </h2>
            { this.state.proposals.map(proposal =>
                <li key={proposal.id}>{proposal.wording}</li>
            )}
        </div>
    )
}

export default DisplayQuestionsInstructor;