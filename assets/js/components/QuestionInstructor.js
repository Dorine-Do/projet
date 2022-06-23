import React, {Component} from 'react';


class DisplayQuestionsInstructor extends React.Component {
    constructor(props) {
        super(props);
        this.state = {questions: []};
    }
    getUsers() {
        let myHeaders = new Headers({
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Origin': 'https://127.0.0.1:8000/instucteur/questions',
        });
        let myInit = {
            method: 'GET',
            headers: myHeaders,
            mode: 'cors',
            cache: 'default' };

        fetch(`http://localhost:8000/instucteur/questions`, myInit)
            .then(response => {
                if( response.ok){
                return response.json()
                }
                else{
                    console.log(response)
                }
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