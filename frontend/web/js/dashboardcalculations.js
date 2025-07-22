
async function fetchData() {
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

    const getColor = (score) => {
        if (score < 0.5) return '#e74c3c';         // Not Implemented
        if (score < 1.5) return '#f39c12';         // Partially Implemented
        if (score < 2.5) return '#f1c40f';         // Mostly Implemented
        return '#2ecc71';                          // Fully Implemented
    };

    const avgScore = dataValues.reduce((sum, score) => sum + score, 0) / dataValues.length;
    displayAverageProgress(avgScore);
}


function displayAverageProgress(averageScore) {
    console.log(`Average Score: ${averageScore}`);
    const percentage = (averageScore / 3) * 100;
    const percentageElement = document.querySelector('.averagecompliance h5');
    const progressBarElement = document.querySelector('.averagecompliance .progress-bar');
    const barColor = getColor(averageScore); // Get color based on average score

    console.log(`Percentage: ${percentage}%`);

    if (percentageElement) {
        percentageElement.textContent = `${Math.round(percentage)}%`;
    }

    if (progressBarElement) {
        progressBarElement.style.width = `${percentage}%`;
        progressBarElement.style.backgroundColor = barColor; // Set the background color
    }
}

fetchData();