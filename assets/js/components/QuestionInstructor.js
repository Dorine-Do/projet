import React, {Component} from 'react';


class DisplayQuestionsInstructor extends React.Component {
    constructor(props) {
        super(props);
        this.state = {questions: []};
    }
    getUsers() {
        fetch(`http://localhost:8000/instucteur/questions`)
            .then(response => {
                if( response.ok){
                return response.json()}
            })
            .then(questionsData => {
            this.setState({ questions: questionsData.data, loading: false})
                console.log(this.state.questions)
            })
    }
    render() {
        return (
            <div>
                <h1>Hello, world!</h1>
                <h2>It is </h2>
            </div>
        );
    }
}