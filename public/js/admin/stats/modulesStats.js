// import {Chart} from 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.esm.js';

document.addEventListener('DOMContentLoaded', function(){



    const ctx = document.getElementById('modules-chart').getContext('2d');
    new Chart( ctx, {
        type: 'doughnut',
        data: {
            labels: [
                'January',
                'February',
                'March',
                'April',
                'May',
                'June',
            ],
            datasets: [{
                label: 'My First dataset',
                backgroundColor: ['red', 'green', 'blue', 'yellow', 'orange', 'skyblue'],
                borderColor: ['red', 'green', 'blue', 'yellow', 'orange', 'skyblue'],
                data: [0, 10, 5, 2, 20, 30, 45],
            }]
        }
    });

});