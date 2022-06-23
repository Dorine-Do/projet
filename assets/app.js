import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter as Router, Switch, Route } from 'react-router-dom';
import './styles/app.css';


import Home from './js/components/Home';

class DisplayQuestionsInstructor extends React.Component {
    constructor(props) {
        super(props);
        this.state =
            {
                questions: [{'name' :'Yeah!'}],
                word : "heelo"
            };
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

                // let test = questionsData[0]
                // let test = [{"id":1}]
                // console.log(test)
                // this.setState({ questions: [test]})
                this.setState({ word: "Youpi"})

                console.log(this.state.word)
                console.log(questionsData);
            })
    }
    render() {
        console.log(this.state.word)
        return (
            <div>
                <h1>Hello, world!</h1>
                <h2>It is </h2>
                {/*{this.state.questions}*/}

                {this.state.word}
                { this.state.questions.map(question =>
                    <li>{question.name}</li>
                )}
            </div>
        );
    }
}

const container = document.getElementById('root');
const root = createRoot(container);
    root.render(
        <DisplayQuestionsInstructor/>
    );
