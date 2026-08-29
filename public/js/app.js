// Main Application JavaScript

const API_URL = '/api';
let currentUser = null;
let lessons = [];
let vocabulary = [];

// Initialize app on page load
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    loadLessons();
});

// Authentication Functions

async function handleRegister(e) {
    e.preventDefault();
    
    const data = {
        username: document.getElementById('regUsername').value,
        email: document.getElementById('regEmail').value,
        password: document.getElementById('regPassword').value,
        first_name: document.getElementById('regFirstName').value,
        last_name: document.getElementById('regLastName').value,
        learning_language: document.getElementById('regLanguage').value
    };

    try {
        const response = await fetch(`${API_URL}/auth/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            alert('Registration successful! Please login.');
            toggleForms();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Register error:', error);
        alert('An error occurred during registration');
    }
}

async function handleLogin(e) {
    e.preventDefault();
    
    const data = {
        email: document.getElementById('loginEmail').value,
        password: document.getElementById('loginPassword').value
    };

    try {
        const response = await fetch(`${API_URL}/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data),
            credentials: 'include'
        });

        const result = await response.json();
        if (result.success) {
            currentUser = result.user;
            showDashboard();
            loadUserProgress();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('An error occurred during login');
    }
}

async function handleLogout() {
    try {
        await fetch(`${API_URL}/auth/logout`, {
            method: 'POST',
            credentials: 'include'
        });
        
        currentUser = null;
        showAuthSection();
    } catch (error) {
        console.error('Logout error:', error);
    }
}

async function checkAuth() {
    try {
        const response = await fetch(`${API_URL}/auth/me`, {
            credentials: 'include'
        });
        
        if (response.ok) {
            const result = await response.json();
            currentUser = result.user;
            showDashboard();
        }
    } catch (error) {
        console.error('Auth check error:', error);
    }
}

// Navigation Functions

function scrollToSection(sectionId) {
    if (!currentUser && sectionId !== 'home') {
        showAuthSection();
        return;
    }
    hideAllSections();
    document.getElementById(sectionId).style.display = 'block';
}

function hideAllSections() {
    const sections = document.querySelectorAll('section');
    sections.forEach(section => {
        section.style.display = 'none';
    });
}

function showAuthSection() {
    hideAllSections();
    document.getElementById('auth').style.display = 'block';
    document.getElementById('loginBtn').textContent = 'Login';
}

function showDashboard() {
    hideAllSections();
    document.getElementById('dashboard').style.display = 'block';
    document.getElementById('userName').textContent = `Welcome, ${currentUser.first_name || currentUser.username}!`;
    document.getElementById('loginBtn').textContent = 'Logout';
    document.getElementById('loginBtn').onclick = handleLogout;
}

function toggleForms() {
    document.getElementById('loginForm').style.display = 
        document.getElementById('loginForm').style.display === 'none' ? 'block' : 'none';
    document.getElementById('registerForm').style.display = 
        document.getElementById('registerForm').style.display === 'none' ? 'block' : 'none';
}

// Lesson Functions

async function loadLessons() {
    try {
        const response = await fetch(`${API_URL}/lessons?limit=20`);
        const result = await response.json();
        
        if (result.success) {
            lessons = result.data;
            displayLessons(lessons);
        }
    } catch (error) {
        console.error('Load lessons error:', error);
    }
}

function displayLessons(lessonsToDisplay) {
    const container = document.getElementById('lessonsList');
    container.innerHTML = '';
    
    lessonsToDisplay.forEach(lesson => {
        const card = document.createElement('div');
        card.className = 'lesson-card';
        card.innerHTML = `
            <div class="lesson-card-header">
                <h3>${lesson.title}</h3>
                <span class="lesson-badge">${lesson.level}</span>
                <span class="lesson-badge">${lesson.type}</span>
            </div>
            <div class="lesson-card-body">
                <p>${lesson.description || 'No description available'}</p>
            </div>
            <div class="lesson-card-footer">
                <span class="language">🌍 ${lesson.language.toUpperCase()}</span>
                <button class="btn btn-primary" onclick="selectLesson(${lesson.id})">Start</button>
            </div>
        `;
        container.appendChild(card);
    });
}

function filterLessons() {
    const language = document.getElementById('languageFilter').value;
    const level = document.getElementById('levelFilter').value;
    
    let filtered = lessons;
    
    if (language) {
        filtered = filtered.filter(l => l.language === language);
    }
    
    if (level) {
        filtered = filtered.filter(l => l.level === level);
    }
    
    displayLessons(filtered);
}

function selectLesson(lessonId) {
    if (!currentUser) {
        showAuthSection();
        return;
    }
    alert(`Lesson ${lessonId} selected. Full implementation coming soon!`);
}

// Vocabulary Functions

async function loadVocabulary() {
    try {
        const response = await fetch(`${API_URL}/vocabulary/random?limit=20`);
        const result = await response.json();
        
        if (result.success) {
            vocabulary = result.data;
            displayVocabulary(vocabulary);
        }
    } catch (error) {
        console.error('Load vocabulary error:', error);
    }
}

function displayVocabulary(vocabToDisplay) {
    const container = document.getElementById('vocabList');
    container.innerHTML = '';
    
    vocabToDisplay.forEach(word => {
        const card = document.createElement('div');
        card.className = 'vocab-card';
        card.innerHTML = `
            <h4>${word.word}</h4>
            <div class="translation">📝 ${word.translation}</div>
            ${word.pronunciation ? `<div>🔊 ${word.pronunciation}</div>` : ''}
            ${word.example_sentence ? `<div class="example">"${word.example_sentence}"</div>` : ''}
        `;
        container.appendChild(card);
    });
}

async function searchVocabulary() {
    const query = document.getElementById('vocabSearch').value;
    
    if (query.length < 2) {
        loadVocabulary();
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}/vocabulary/search?q=${encodeURIComponent(query)}`);
        const result = await response.json();
        
        if (result.success) {
            displayVocabulary(result.data);
        }
    } catch (error) {
        console.error('Search vocabulary error:', error);
    }
}

// Progress Functions

async function loadUserProgress() {
    if (!currentUser) return;
    
    try {
        const response = await fetch(`${API_URL}/progress/dashboard`, {
            credentials: 'include'
        });
        
        const result = await response.json();
        
        if (result.success) {
            const stats = result.stats || {};
            document.getElementById('lessonsCompleted').textContent = stats.total_lessons_completed || 0;
            document.getElementById('quizzesPassed').textContent = stats.quizzes_passed || 0;
            document.getElementById('averageScore').textContent = (stats.average_quiz_score || 0).toFixed(1) + '%';
            document.getElementById('studyStreak').textContent = '🔥 0 days';
            
            // Display recent activity
            displayRecentActivity(result.recent_lessons);
        }
    } catch (error) {
        console.error('Load progress error:', error);
    }
}

function displayRecentActivity(activities) {
    const container = document.getElementById('recentActivity');
    container.innerHTML = '';
    
    if (!activities || activities.length === 0) {
        container.innerHTML = '<p>No recent activity</p>';
        return;
    }
    
    activities.forEach(activity => {
        const item = document.createElement('div');
        item.className = 'activity-item';
        item.innerHTML = `
            <div>
                <strong>${activity.lesson_title || 'Activity'}</strong>
                <p>${activity.lesson_type || ''}</p>
            </div>
            <span class="activity-time">${formatDate(activity.completed_at)}</span>
        `;
        container.appendChild(item);
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}
