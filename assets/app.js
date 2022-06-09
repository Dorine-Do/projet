import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter as Router } from 'react-router-dom';
import './styles/app.css';

import Home from './js/components/Home';

const container = document.getElementById('root');
const root = createRoot(container);
    root.render(
        <Home>
            <h1>Hello, world!</h1>
        </Home>

    );
