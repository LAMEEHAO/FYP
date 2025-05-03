let currentSubject = null;

document.addEventListener('DOMContentLoaded', () => {
  clearAllQuestions();
  loadSubjects();

  const storedSubject = localStorage.getItem('currentSubject');
  if (storedSubject) {
    currentSubject = storedSubject;
    document.getElementById('subject-select').value = storedSubject;
    document.getElementById('quiz-builder').style.display = 'block';
    loadSubjectQuiz();
  }
});

function clearAllQuestions() {
  const quizBoxes = document.querySelectorAll('.quiz-box');
  quizBoxes.forEach(box => box.remove());
}

function addMoreQuestion() {
  const builder = document.getElementById('quiz-builder');
  const newBox = document.createElement('div');
  newBox.className = 'quiz-box';

  // Use a unique name for radio buttons
  const uniqueId = Date.now();
  const content = `
    <div class="quiz-header">
      <input type="text" placeholder="Enter Your Question">
      <input type="number" placeholder="points" min="0">
    </div>
    <div class="quiz-options">
      <label><input type="radio" name="correct_${uniqueId}" value="0"> <input type="text" placeholder="Option 1"></label>
      <label><input type="radio" name="correct_${uniqueId}" value="1"> <input type="text" placeholder="Option 2"></label>
      <label><input type="radio" name="correct_${uniqueId}" value="2"> <input type="text" placeholder="Option 3"></label>
      <label><input type="radio" name="correct_${uniqueId}" value="3"> <input type="text" placeholder="Option 4"></label>
    </div>
    <button class="delete-btn" onclick="deleteQuestion(this)">🗑 Delete</button>
  `;

  newBox.innerHTML = content;
  builder.insertBefore(newBox, document.querySelector('.add-question'));
  checkForDoneButton();
}

function deleteQuestion(btn) {
  if (confirm("Are you sure you want to delete this question?")) {
    btn.closest('.quiz-box').remove();
    checkForDoneButton();
  }
}

function checkForDoneButton() {
  const boxes = document.querySelectorAll('.quiz-box');
  const doneButton = document.getElementById('done-button');
  doneButton.style.display = boxes.length > 0 ? 'block' : 'none';
}

function loadSubjects() {
  const select = document.getElementById('subject-select');
  const loadingIndicator = document.getElementById('subject-loading');
  loadingIndicator.style.display = 'inline';

  // Fetch subjects from the database
  fetch('subjects.php?action=list', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    }
  })
    .then(response => {
      if (!response.ok) {
        return response.text().then(text => {
          throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        const subjects = data.subjects || [];
        select.innerHTML = `<option value="">-- Select Subject --</option>`;
        subjects.forEach(subject => {
          const option = document.createElement('option');
          option.value = subject.name;
          option.textContent = subject.name;
          select.appendChild(option);
        });

        // Update localStorage as a cache
        localStorage.setItem('subjects', JSON.stringify(subjects.map(s => s.name)));

        // Restore selected subject if applicable
        if (currentSubject && subjects.some(s => s.name === currentSubject)) {
          select.value = currentSubject;
          document.getElementById('quiz-builder').style.display = 'block';
          loadSubjectQuiz();
        }
      } else {
        throw new Error(data.message || 'Failed to load subjects');
      }
    })
    .catch(error => {
      console.error('Error loading subjects:', error);
      alert('Failed to load subjects: ' + error.message);

      // Fallback to localStorage
      const cachedSubjects = JSON.parse(localStorage.getItem('subjects') || '[]');
      select.innerHTML = `<option value="">-- Select Subject --</option>`;
      cachedSubjects.forEach(subject => {
        const option = document.createElement('option');
        option.value = subject;
        option.textContent = subject;
        select.appendChild(option);
      });
    })
    .finally(() => {
      loadingIndicator.style.display = 'none';
    });
}

function addSubject() {
  const subjectInput = document.getElementById('new-subject-name');
  const subjectName = subjectInput.value.trim();

  if (!subjectName) {
    alert("Please enter a subject name.");
    return;
  }

  // Add subject to the database
  fetch('subjects.php?action=add', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ subject: subjectName })
  })
    .then(response => {
      if (!response.ok) {
        return response.text().then(text => {
          throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
        });
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        // Reload subjects to update dropdown
        loadSubjects();
        subjectInput.value = '';
        currentSubject = subjectName;
        localStorage.setItem('currentSubject', currentSubject);
        document.getElementById('subject-select').value = subjectName;
        document.getElementById('quiz-builder').style.display = 'block';
        clearAllQuestions();
      } else {
        alert("Error adding subject: " + (data.message || "Unknown server error"));
      }
    })
    .catch(error => {
      console.error('Error adding subject:', error);
      alert("An error occurred while adding the subject: " + error.message);
    });
}

function loadSubjectQuiz() {
  currentSubject = document.getElementById('subject-select').value;
  localStorage.setItem('currentSubject', currentSubject);
  clearAllQuestions();

  const builder = document.getElementById('quiz-builder');
  builder.style.display = currentSubject ? 'block' : 'none';

  if (!currentSubject) return;

  const quizData = JSON.parse(localStorage.getItem(`quiz_${currentSubject}`) || "[]");

  quizData.forEach(data => {
    addMoreQuestion();
  });

  const allBoxes = document.querySelectorAll('.quiz-box');
  quizData.forEach((data, index) => {
    const box = allBoxes[index];
    box.querySelector('input[type="text"]').value = data.question;
    box.querySelector('input[type="number"]').value = data.points;

    const inputs = box.querySelectorAll('.quiz-options input[type="text"]');
    data.options.forEach((ans, i) => {
      if (inputs[i]) inputs[i].value = ans;
    });

    const radios = box.querySelectorAll('.quiz-options input[type="radio"]');
    if (typeof data.correctAnswerIndex === 'number' && radios[data.correctAnswerIndex]) {
      radios[data.correctAnswerIndex].checked = true;
    }
  });

  checkForDoneButton();
}

function sanitizeText(text) {
  // Basic sanitization: remove excessive whitespace and potentially harmful characters
  return text.replace(/\s+/g, ' ').replace(/[<>]/g, '').trim();
}

function goToNextPage() {
  if (!currentSubject) {
    alert("Please select a subject.");
    return;
  }

  const quizTitle = document.getElementById('quiz-title').value.trim();
  if (!quizTitle) {
    alert("Please enter a quiz title.");
    return;
  }

  const quizBoxes = document.querySelectorAll('.quiz-box');
  const quizData = [];
  const doneButton = document.getElementById('done-button');

  for (const [index, box] of Array.from(quizBoxes).entries()) {
    const question = box.querySelector('.quiz-header input[type="text"]').value.trim();
    const points = parseInt(box.querySelector('.quiz-header input[type="number"]').value) || 0;
    const options = Array.from(box.querySelectorAll('.quiz-options input[type="text"]')).map(input => sanitizeText(input.value));
    const correctAnswerRadio = box.querySelector('.quiz-options input[type="radio"]:checked');
    const correctAnswerIndex = correctAnswerRadio ? parseInt(correctAnswerRadio.value) : -1;

    if (!question) {
      alert(`Question ${index + 1}: Please enter question text.`);
      return;
    }
    if (options.length !== 4 || options.some(opt => !opt)) {
      alert(`Question ${index + 1}: Please provide exactly four non-empty options.`);
      return;
    }
    if (correctAnswerIndex === -1) {
      alert(`Question ${index + 1}: Please select a correct answer.`);
      return;
    }
    const uniqueOptions = new Set(options);
    if (uniqueOptions.size !== options.length) {
      alert(`Question ${index + 1}: Options must be unique.`);
      return;
    }

    quizData.push({
      question,
      points,
      options,
      correctAnswerIndex
    });
  }

  if (quizData.length === 0) {
    alert("Please add at least one complete question with four options and a correct answer.");
    return;
  }

  const dataToSend = {
    subject: currentSubject,
    quizTitle: quizTitle,
    questions: quizData
  };

  console.log('Data to send:', JSON.stringify(dataToSend, null, 2));

  // Show loading state
  doneButton.classList.add('loading', 'disabled');
  doneButton.disabled = true;

  // Send data to PHP script via AJAX
  fetch('save_quiz.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(dataToSend)
  })
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok) {
        return response.text().then(text => {
          throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
        });
      }
      return response.json();
    })
    .then(result => {
      console.log('Server response:', result);
      if (result.success) {
        localStorage.setItem(`quiz_${currentSubject}`, JSON.stringify(quizData));
        alert("Quiz saved successfully!");
        window.location.href = "Mode-selection.html";
      } else {
        alert("Error saving quiz: " + (result.message || "Unknown server error"));
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      alert("An error occurred while saving the quiz: " + error.message);
    })
    .finally(() => {
      // Remove loading state
      doneButton.classList.remove('loading', 'disabled');
      doneButton.disabled = false;
    });
}

function deleteSubject() {
  const select = document.getElementById('subject-select');
  const subjectToDelete = select.value;

  if (!subjectToDelete) {
    alert("Please select a subject to delete.");
    return;
  }

  if (!confirm(`Are you sure you want to delete "${subjectToDelete}"? This will also delete all its quizzes and questions.`)) {
    return;
  }

  // Send AJAX request to delete subject from database
  fetch('subjects.php?action=delete', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ subject: subjectToDelete })
  })
    .then(response => {
      if (!response.ok) {
        return response.text().then(text => {
          throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
        });
      }
      return response.json();
    })
    .then(result => {
      if (result.success) {
        // Update localStorage
        let subjects = JSON.parse(localStorage.getItem('subjects') || "[]");
        subjects = subjects.filter(subject => subject !== subjectToDelete);
        localStorage.setItem('subjects', JSON.stringify(subjects));
        localStorage.removeItem(`quiz_${subjectToDelete}`);

        // Update UI
        loadSubjects();
        clearAllQuestions();
        document.getElementById('quiz-builder').style.display = 'none';
        currentSubject = null;
        localStorage.removeItem('currentSubject');

        alert(`"${subjectToDelete}" has been deleted.`);
      } else {
        alert("Error deleting subject: " + (result.message || "Unknown server error"));
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      alert("An error occurred while deleting the subject: " + error.message);
    });
}