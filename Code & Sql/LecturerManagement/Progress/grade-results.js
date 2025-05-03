document.addEventListener('DOMContentLoaded', () => {
  loadQuizzes();
  loadResults();
});

function loadQuizzes() {
  fetch('get-grades.php?action=quizzes')
    .then(res => res.json())
    .then(quizzes => {
      const select = document.getElementById('quiz-title');
      quizzes.forEach(quiz => {
        const option = document.createElement('option');
        option.value = quiz.id;
        option.textContent = quiz.quiz_title;
        select.appendChild(option);
      });
    })
    .catch(err => alert("Failed to load quizzes: " + err));
}

function loadResults() {
  fetch('get-grades.php')
    .then(res => res.json())
    .then(results => {
      const list = document.getElementById('results-list');
      list.innerHTML = '';

      results.forEach((res) => {
        const card = document.createElement('div');
        card.className = 'material-card';

        card.innerHTML = `
          <h3>${res.name}</h3>
          <p><strong>Quiz:</strong> ${res.quiz_title}</p>
          <p><strong>Grade:</strong> ${res.grade}%</p>
          <div class="result-actions">
            <button class="edit" onclick="editResult(${res.id})">✏️ Edit</button>
            <button onclick="deleteResult(${res.id})">🗑️ Delete</button>
          </div>
        `;

        list.appendChild(card);
      });
    })
    .catch(err => alert("Failed to load grades: " + err));
}

function addResult() {
  const name = document.getElementById('student-name').value.trim();
  const quiz_id = document.getElementById('quiz-title').value;
  const grade = document.getElementById('grade-score').value.trim();

  if (!name || !quiz_id || !grade) {
    alert("Please fill in all fields.");
    return;
  }

  fetch('add-grade.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, quiz_id, grade })
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Grade result added successfully!");
        document.getElementById('student-name').value = '';
        document.getElementById('quiz-title').value = '';
        document.getElementById('grade-score').value = '';
        loadResults();
      } else {
        alert("Error: " + data.error);
      }
    })
    .catch(error => alert("Request failed: " + error));
}

function editResult(id) {
  fetch('get-grade-by-id.php?id=' + id)
    .then(res => res.json())
    .then(result => {
      const name = prompt("Edit student name:", result.name);
      const quiz_id = prompt("Edit quiz ID (e.g., 1 for Basic Arithmetic):", result.quiz_id);
      const grade = prompt("Edit grade (%):", result.grade);

      if (!name || !quiz_id || !grade) {
        alert("All fields must be filled.");
        return;
      }

      fetch('edit-grade.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, name, quiz_id, grade })
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert("Grade updated successfully!");
            loadResults();
          } else {
            alert("Update failed: " + data.error);
          }
        })
        .catch(err => alert("Request failed: " + err));
    })
    .catch(err => alert("Failed to fetch record: " + err));
}

function deleteResult(id) {
  if (!confirm("Are you sure you want to delete this result?")) return;

  fetch('delete-grade.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        loadResults();
      } else {
        alert("Delete failed: " + data.error);
      }
    })
    .catch(err => alert("Request failed: " + err));
}