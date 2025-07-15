async function drawChart() {
    try {

        const id = +$('.standard').text();
        const Url = './analysis?id=' + id;

        // 1. Fetch clause scores from the backend
        const response = await fetch(Url);
        const rawArray = await response.json();

        // 2. Flatten into a map: { clause: score }
        const clauseData = {};
        rawArray.forEach(obj => {
            const [clause, value] = Object.entries(obj)[0]; // Destructure the only entry
            clauseData[clause] = parseFloat(value); // Ensure score is numeric
        });
        console.log('Pie chart data ...');
        console.log(clauseData);

        // Count how many clauses fall into each implementation level initially
        const levelCounts = {
            'Not Implemented': 0,
            'Partially Implemented': 0,
            'Mostly Implemented': 0,
            'Fully Implemented': 0
        };

        if (Object.keys(clauseData).length < 1) {
            console.warn('Not enough data for pie chart');
            return;
        }

        Object.values(clauseData).forEach(score => {
            console.log('values');
            console.log(score);
            if (score < 0.5) levelCounts['Not Implemented']++;
            else if (score < 1.5) levelCounts['Partially Implemented']++;
            else if (score < 2.5) levelCounts['Mostly Implemented']++;
            else levelCounts['Fully Implemented']++;
        });

        // Pie chart config
        const pieConfig = {
            type: 'pie',
            data: {
                labels: Object.keys(levelCounts),
                datasets: [{
                    data: Object.values(levelCounts),
                    backgroundColor: [
                        '#e74c3c', // Not Implemented
                        '#f39c12', // Partially Implemented
                        '#f1c40f', // Mostly Implemented
                        '#2ecc71'  // Fully Implemented
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Clause Implementation Levels Summary'
                    }
                }
            }
        };


        // Render the pie chart
        new Chart(document.getElementById('levelPieChart'), pieConfig);

    } catch (error) {
        console.error('Error fetching clause data:', error);

    }
}

drawChart()