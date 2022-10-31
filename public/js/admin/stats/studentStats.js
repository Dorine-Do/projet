async function fetchStudentModulesSuccessRate()
{
    return await fetch( '../fetch/student-modules-success-rate/' + document.querySelector('#studentId').value )
        .then( response => response.json() )
}

function displayModulesStats()
{
    fetchStudentModulesSuccessRate()
        .then( data => {
            let labels = [];
            let successRates = [];
            let averageScores = [];
            for( let i = 0; i < data.length; i++ )
            {
                labels.push( data[i].title );
                successRates.push( data[i].successRate );
                averageScores.push( data[i].averageScore );
            }

            let successRateDataset = {
                label: 'Taux de réussite',
                backgroundColor: '#ffac8f',
                data: successRates,
            }

            let averageScoreDataset = {
                label: 'Note moyenne',
                backgroundColor: '#ffac8f',
                data: averageScores,
            }

            const ctxRateModules = document.getElementById('modules-rate-chart').getContext('2d');
            new Chart( ctxRateModules, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [successRateDataset ]
                }
            });

            const ctxAverageModules = document.getElementById('modules-average-chart').getContext('2d');
            new Chart( ctxAverageModules, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [ averageScoreDataset]
                }
            });
    } )


}

async function fetchStudentStacksSuccessRate()
{
    return await fetch( '../fetch/student-stacks-success-rate/' + document.querySelector('#studentId').value )
        .then( response => response.json() )
}

function displayStacksStats()
{
    fetchStudentStacksSuccessRate()
        .then( data => {
            let labels = [];
            let successRates = [];
            let averageScores = [];
            for( let i = 0; i < data.length; i++ )
            {
                labels.push( data[i].title );
                successRates.push( data[i].successRate );
                averageScores.push( data[i].averageScore );
            }

            let successRateDataset = {
                label: 'Taux de réussite',
                backgroundColor: '#ffac8f',
                data: successRates,
            }

            let averageScoreDataset = {
                label: 'Note moyenne',
                backgroundColor: '#ffac8f',
                data: averageScores,
            }

            const ctxRateStacks = document.getElementById('stacks-rate-chart').getContext('2d');
            new Chart( ctxRateStacks, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [successRateDataset]
                }
            });

            const ctxAverageStacks = document.getElementById('stacks-average-chart').getContext('2d');
            new Chart( ctxAverageStacks, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [averageScoreDataset]
                }
            });
        } )


}

document.addEventListener('DOMContentLoaded', function(){

    displayModulesStats();
    displayStacksStats();

});