import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter as Router, Switch, Route } from 'react-router-dom';
import './styles/app.css';
import DisplayQuestionsInstructor from './js/components/QuestionInstructor';


class Home extends React.Component {
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
}

function App () {

}

const container = document.getElementById('root');
const root = createRoot(container);
    root.render(
        <div className={"container"}>
            <Home/>
        </div>
    );
