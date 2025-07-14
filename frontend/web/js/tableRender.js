function renderScoreTable(dataMap) {
    const container = document.getElementById('scoreTableContainer');
    const rows = Object.entries(dataMap).map(([clause, score]) => {
        let label;
        if (score < 0.5) label = 'Not Implemented';
        else if (score < 1.5) label = 'Partially Implemented';
        else if (score < 2.5) label = 'Mostly Implemented';
        else label = 'Fully Implemented';

        return `<tr>
      <td>${clause}</td>
      <td>${score}</td>
      <td>${label}</td>
    </tr>`;
    });

    container.innerHTML = `
    <h3 class="text-center leading-tight">Score Summary Table</h3>
    <div class="table-responsive">
    <table border="1" cellpadding="6" class="table table-bordered">
      <thead>
        <tr><th>Clause</th><th>Score</th><th>Status</th></tr>
      </thead>
      <tbody>${rows.join('')}</tbody>
    </table>
    </div>
  `;
}