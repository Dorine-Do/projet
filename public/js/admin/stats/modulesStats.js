async function fetchModulesSuccessRate()
{
    return await fetch( './fetch/modules-success-rate' )
        .then( response => response.json() )
}

function displayModulesStats()
{
    fetchModulesSuccessRate()
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

async function fetchStacksSuccessRate()
{
    return await fetch( './fetch/stacks-success-rate' )
        .then( response => response.json() )
}

function displayStacksStats()
{
    fetchStacksSuccessRate()
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