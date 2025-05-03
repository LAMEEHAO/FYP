let deleteMode = false;
let selectAllMode = false;

document.addEventListener('DOMContentLoaded', () => {
  loadQuizzes();
  loadMaterials();
});

function loadQuizzes() {
  fetch('fetch_quizzes.php')
    .then(response => response.json())
    .then(data => {
      const select = document.getElementById('quiz-select');
      select.innerHTML = '<option value="">Select Quiz (Optional)</option>';
      data.quizzes.forEach(quiz => {
        const option = document.createElement('option');
        option.value = quiz.id;
        option.textContent = quiz.quiz_title;
        select.appendChild(option);
      });
    })
    .catch(error => console.error('Error loading quizzes:', error));
}

function loadMaterials() {
  fetch('fetch_materials.php')
    .then(response => response.json())
    .then(data => {
      const list = document.getElementById('materials-list');
      list.innerHTML = '';

      data.materials.forEach((material, index) => {
        const card = document.createElement('div');
        card.className = 'material-card';
        if (deleteMode) card.classList.add('delete-mode');

        card.innerHTML = `
          <input type="checkbox" data-index="${index}" data-title="${material.title}">
          <h3>${material.title}</h3>
          <p>Quiz: ${material.quiz_title || 'None'}</p>
          <a href="${material.link}" target="_blank">View Material</a>
        `;
        list.appendChild(card);
      });

      if (deleteMode && selectAllMode) {
        document.querySelectorAll('.material-card input[type="checkbox"]').forEach(cb => cb.checked = true);
      }
    })
    .catch(error => console.error('Error loading materials:', error));
}

function toggleSelectAll() {
  selectAllMode = !selectAllMode;
  document.querySelectorAll('.material-card input[type="checkbox"]').forEach(cb => cb.checked = selectAllMode);
  document.getElementById('select-all-btn').textContent = selectAllMode ? 'Deselect All' : 'Select All';
}

function addMaterial() {
  const title = document.getElementById('material-title').value.trim();
  const link = document.getElementById('material-link').value.trim();
  const quiz_id = document.getElementById('quiz-select').value || null;

  if (!title || !link) {
    alert("Please fill in both title and link fields.");
    return;
  }

  fetch('add_material.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ title, link, quiz_id }),
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Material added successfully!');
        document.getElementById('material-title').value = '';
        document.getElementById('material-link').value = '';
        document.getElementById('quiz-select').value = '';
        loadMaterials();
      } else {
        alert('Error adding material: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('There was a problem with adding the material.');
    });
}

function handleDeleteClick() {
  const btn = document.getElementById('delete-material-btn');
  const selectAllBtn = document.getElementById('select-all-btn');

  if (!deleteMode) {
    deleteMode = true;
    btn.classList.add('delete-mode');
    selectAllBtn.style.display = 'inline-block';
    loadMaterials();
  } else {
    const checkboxes = document.querySelectorAll('.material-card input[type="checkbox"]');
    const titlesToDelete = Array.from(checkboxes)
      .filter(cb => cb.checked)
      .map(cb => cb.getAttribute('data-title'));

    if (titlesToDelete.length === 0) {
      deleteMode = false;
      selectAllMode = false;
      btn.classList.remove('delete-mode');
      selectAllBtn.style.display = 'none';
      loadMaterials();
    } else {
      fetch('delete_material.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ titles: titlesToDelete }),
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            deleteMode = false;
            selectAllMode = false;
            btn.classList.remove('delete-mode');
            selectAllBtn.style.display = 'none';
            loadMaterials();
          } else {
            alert('Failed to delete materials: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error deleting materials.');
        });
    }
  }
}