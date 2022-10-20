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
                backgroundColor: '#fff4e4',
                data: averageScores,
            }

            const ctxModules = document.getElementById('modules-chart').getContext('2d');
            new Chart( ctxModules, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [successRateDataset, averageScoreDataset]
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
                backgroundColor: '#fff4e4',
                data: averageScores,
            }

            const ctxStacks = document.getElementById('stacks-chart').getContext('2d');
            new Chart( ctxStacks, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [successRateDataset, averageScoreDataset]
                }
            });
        } )


}

document.addEventListener('DOMContentLoaded', function(){

    displayModulesStats();
    displayStacksStats();

});