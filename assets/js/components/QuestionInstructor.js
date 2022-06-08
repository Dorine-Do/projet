import React, {Component} from 'react';


class DisplayQuestionsInstructor extends React.Component {
    constructor(props) {
        super(props);
        this.state = {date: new Date()};
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