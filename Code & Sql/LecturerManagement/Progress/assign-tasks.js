document.addEventListener('DOMContentLoaded', () => {
  loadQuizzes();
  loadStudents();
  loadTasks();
});

function loadQuizzes() {
  fetch('fetch_quizzes.php')
    .then(response => response.json())
    .then(data => {
      const select = document.getElementById('quiz-select');
      select.innerHTML = '<option value="">Select Quiz</option>';
      if (data.success && data.quizzes) {
        data.quizzes.forEach(quiz => {
          const option = document.createElement('option');
          option.value = quiz.id;
          option.textContent = quiz.quiz_title;
          select.appendChild(option);
        });
      } else {
        console.error('Failed to load quizzes:', data.message);
      }
    })
    .catch(error => console.error('Error loading quizzes:', error));
}

function loadStudents() {
  fetch('fetch_students.php')
    .then(response => response.json())
    .then(data => {
      const select = document.getElementById('student-select');
      select.innerHTML = '<option value="">Select Student</option>';
      if (data.success && data.students) {
        data.students.forEach(student => {
          const option = document.createElement('option');
          option.value = student.id;
          option.textContent = student.name;
          select.appendChild(option);
        });
      } else {
        console.error('Failed to load students:', data.message);
      }
    })
    .catch(error => console.error('Error loading students:', error));
}

function loadTasks() {
  fetch('assign_task.php?action=get_tasks')
    .then(response => response.json())
    .then(data => {
      const list = document.getElementById('task-list');
      list.innerHTML = '';

      if (data.error) {
        alert('Error: ' + data.error);
        return;
      }

      data.forEach(task => {
        const item = document.createElement('div');
        item.className = 'task-item';

        item.innerHTML = `
          <div>
            <h3>${task.quiz_title}</h3>
            <p><strong>Student:</strong> ${task.student_name}</p>
            <p class="description" data-full-text="${task.description}"><strong>Description:</strong> ${task.description}</p>
          </div>
          <div>
            <span>${task.type}</span>
            <button class="delete-btn" onclick="deleteTask(${task.id})">🗑️</button>
          </div>
        `;

        list.appendChild(item);
      });
    })
    .catch(error => {
      alert('Failed to load tasks: ' + error.message);
    });
}

function assignTask() {
  const quiz_id = document.getElementById('quiz-select').value;
  const student_id = document.getElementById('student-select').value;
  const description = document.getElementById('task-description').value.trim();
  const type = document.getElementById('task-type').value;

  if (!quiz_id || !student_id || !description) {
    alert('Please fill in all fields.');
    return;
  }

  fetch('assign_task.php?action=add_task', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ quiz_id, student_id, description, type })
  })
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        alert('Error: ' + data.error);
        return;
      }

      document.getElementById('quiz-select').value = '';
      document.getElementById('student-select').value = '';
      document.getElementById('task-description').value = '';
      document.getElementById('task-type').value = 'revision';
      loadTasks();
    })
    .catch(error => {
      alert('Failed to add task: ' + error.message);
    });
}

function deleteTask(id) {
  if (confirm('Are you sure you want to delete this task?')) {
    fetch('assign_task.php?action=delete_task', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id })
    })
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert('Error: ' + data.error);
          return;
        }
        loadTasks();
      })
      .catch(error => {
        alert('Failed to delete task: ' + error.message);
      });
  }
}