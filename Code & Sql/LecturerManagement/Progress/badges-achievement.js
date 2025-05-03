let deleteMode = false;

document.addEventListener('DOMContentLoaded', () => {
  loadQuizzes();
  loadBadges();
});

async function loadQuizzes() {
  try {
    const response = await fetch('assign_badge.php?action=quizzes', {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
    });
    const quizzes = await response.json();

    if (!response.ok) {
      throw new Error(quizzes.error || 'Failed to load quizzes');
    }

    const select = document.getElementById('quiz-title');
    quizzes.forEach(quiz => {
      const option = document.createElement('option');
      option.value = quiz.id;
      option.textContent = quiz.quiz_title;
      select.appendChild(option);
    });
  } catch (error) {
    console.error('Error loading quizzes:', error);
    alert('Failed to load quizzes: ' + error.message);
  }
}

async function loadBadges() {
  try {
    const response = await fetch('assign_badge.php', {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
    });
    const badges = await response.json();

    if (!response.ok) {
      throw new Error(badges.error || 'Failed to load badges');
    }

    const list = document.getElementById('badge-list');
    list.innerHTML = '';

    badges.forEach((badge) => {
      const item = document.createElement('div');
      item.className = 'badge-item';

      let badgeClass = '';
      switch (badge.badge) {
        case '🏅 90% and Above':
          badgeClass = 'badge-90';
          break;
        case '🏆 High Achiever':
          badgeClass = 'badge-high';
          break;
        case '🎖️ Excellent':
          badgeClass = 'badge-excellent';
          break;
        case '👏 Good Effort':
          badgeClass = 'badge-good';
          break;
        case '🎯 Keep Trying':
          badgeClass = 'badge-keep';
          break;
      }

      item.innerHTML = `
        <div>
          <h3>${badge.name}</h3>
          <p><strong>Score:</strong> ${badge.score}%</p>
          <p><strong>Quiz:</strong> ${badge.quiz_title || 'N/A'}</p>
        </div>
        <div>
          <span class="${badgeClass}">${badge.badge}</span>
          <button class="delete-btn" onclick="deleteBadge(${badge.id})">🗑️</button>
        </div>
      `;

      list.appendChild(item);
    });
  } catch (error) {
    console.error('Error loading badges:', error);
    alert('Failed to load badges: ' + error.message);
  }
}

async function assignBadge() {
  const name = document.getElementById('student-name').value.trim();
  const score = parseInt(document.getElementById('student-score').value.trim(), 10);
  const quizId = document.getElementById('quiz-title').value;

  if (!name || isNaN(score) || !quizId) {
    alert('Please fill in all fields.');
    return;
  }

  let badge = '';
  if (score >= 90) {
    badge = '🏅 90% and Above';
  } else if (score >= 80) {
    badge = '🏆 High Achiever';
  } else if (score >= 70) {
    badge = '🎖️ Excellent';
  } else if (score >= 50) {
    badge = '👏 Good Effort';
  } else {
    badge = '🎯 Keep Trying';
  }

  try {
    const response = await fetch('assign_badge.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, score, badge, quiz_id: quizId }),
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.error || 'Failed to assign badge');
    }

    document.getElementById('student-name').value = '';
    document.getElementById('student-score').value = '';
    document.getElementById('quiz-title').value = '';
    loadBadges();
  } catch (error) {
    console.error('Error assigning badge:', error);
    alert('Failed to assign badge: ' + error.message);
  }
}

async function deleteBadge(id) {
  if (!confirm('Are you sure you want to delete this badge?')) {
    return;
  }

  try {
    const response = await fetch(`delete_badge.php?id=${id}`, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
    });

    const result = await response.json();

    if (!response.ok) {
      throw new Error(result.error || 'Failed to delete badge');
    }

    loadBadges();
  } catch (error) {
    console.error('Error deleting badge:', error);
    alert('Failed to delete badge: ' + error.message);
  }
}

function toggleDeleteMode() {
  deleteMode = !deleteMode;
  const badgeList = document.getElementById('badge-list');
  
  if (deleteMode) {
    badgeList.classList.add('delete-mode');
  } else {
    badgeList.classList.remove('delete-mode');
  }
}