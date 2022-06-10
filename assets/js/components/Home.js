import React, {Component} from 'react';
import {Route, Switch,Redirect, Link, withRouter} from 'react-router-dom';
//
//
class Home extends Component {
//
    render() {
        return (
            <div>
                 <nav className="">
                        <ul>
                            <li><Link className={"nav-link"} to={"/posts"}> Posts </Link></li>
                        </ul>
                   </nav>
               </div>
        )
    }
}

export default Home;