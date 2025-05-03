document.addEventListener('DOMContentLoaded', () => {
    // Load the quiz data from localStorage
    const quizData = JSON.parse(localStorage.getItem('currentQuiz') || '{}');
    
    // Check if the elements exist and quizData is not empty
    if (quizData && document.getElementById('quiz-title')) {
        document.getElementById('quiz-title').textContent = quizData.title || "Untitled Quiz";
        document.getElementById('quiz-subject').textContent = quizData.subject || "-";
        document.getElementById('quiz-questions').textContent = quizData.questions ? quizData.questions.length : "0";
        document.getElementById('quiz-mode').textContent = quizData.gameMode || "-";
    } else {
        alert('No quiz data found or elements missing!');
    }
});

function previewQuiz() {
    alert('Preview feature coming soon!');
}

function editQuiz() {
    window.location.href = 'edit-quiz.html';  // Ensure this page allows the lecturer to edit their quiz
}

function goBack() {
    window.history.back();
}

function publishQuiz() {
    alert('Quiz Published Successfully!');
    // Here you can save the quiz as "published" or move to the dashboard
    // Optionally clear quiz data if necessary after publishing
}
console.log(localStorage.getItem('currentQuiz'));
