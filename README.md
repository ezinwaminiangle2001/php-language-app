# PHP Language Learning System

A comprehensive web-based language learning platform built with PHP that enables users to learn multiple languages through structured lessons, vocabulary practice, and interactive quizzes.

## Features

### User Management
- User registration and authentication
- User profile management
- Learning language and level preferences
- Session management with secure password hashing

### Lessons
- Structured lessons organized by language, level, and type
- Multiple lesson types: vocabulary, grammar, conversation, listening, reading, writing
- Lesson content with media support (audio, images, videos)
- Progress tracking for each lesson

### Vocabulary
- Comprehensive vocabulary database
- Words with translations, pronunciations, and example sentences
- Audio and image support for better learning
- Difficulty levels (beginner to expert)
- Search functionality
- Random vocabulary for practice

### Quizzes
- Quiz creation with multiple question types
- Question and answer management
- Automatic grading system
- Passing score configuration
- Quiz result tracking and analytics

### Progress Tracking
- User dashboard with comprehensive statistics
- Lesson completion tracking
- Quiz result history
- Learning streaks
- Level-based progress tracking
- Time spent on lessons

### Achievements
- Badge system for milestones
- Streak tracking (current and longest)
- Achievement unlocking based on activities

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite enabled
- Composer (for dependency management, optional)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/ezinwaminiangle2001/php-language-app.git
cd php-language-app
```

### 2. Database Setup

```bash
# Create a new database
mysql -u root -p -e "CREATE DATABASE language_learning CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import the schema
mysql -u root -p language_learning < database/schema.sql
```

### 3. Configuration

Edit `config/config.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'language_learning');
define('DB_USER', 'root');
define('DB_PASSWORD', 'your_password');
```

### 4. Directory Permissions

```bash
chmod -R 755 public/
chmod -R 755 logs/
```

### 5. Web Server Setup

**For Apache**, create a `.htaccess` file in the `public` directory:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

### 6. Start Learning

Open your browser and navigate to `http://localhost/php-language-app`

## API Endpoints

### Authentication

#### Register User
```
POST /api/auth/register
Content-Type: application/json

{
  "username": "john_doe",
  "email": "john@example.com",
  "password": "SecurePassword123",
  "first_name": "John",
  "last_name": "Doe",
  "learning_language": "es"
}
```

#### Login
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "SecurePassword123"
}
```

#### Logout
```
POST /api/auth/logout
```

#### Get Current User
```
GET /api/auth/me
```

### Lessons

#### Get All Lessons
```
GET /api/lessons?limit=50&offset=0
```

#### Get Lesson by ID
```
GET /api/lessons/{id}
```

#### Get Lessons by Language and Level
```
GET /api/lessons/language/{language}?level=beginner
```

#### Get Lessons by Type
```
GET /api/lessons/type/{type}
```

#### Create Lesson (Admin)
```
POST /api/lessons
Content-Type: application/json

{
  "title": "Spanish Basics",
  "description": "Learn basic Spanish",
  "language": "es",
  "type": "vocabulary",
  "level": "beginner",
  "content": "Lesson content..."
}
```

#### Update Lesson
```
PUT /api/lessons/{id}
Content-Type: application/json
```

#### Delete Lesson
```
DELETE /api/lessons/{id}
```

### Vocabulary

#### Search Vocabulary
```
GET /api/vocabulary/search?q=hello
```

#### Get Random Vocabulary
```
GET /api/vocabulary/random?limit=10&difficulty=beginner
```

#### Get Vocabulary by Difficulty
```
GET /api/vocabulary/difficulty/{difficulty}
```

#### Get Vocabulary by Lesson
```
GET /api/lessons/{lessonId}/vocabulary
```

#### Add Vocabulary Word
```
POST /api/vocabulary
Content-Type: application/json

{
  "lesson_id": 1,
  "word": "hello",
  "translation": "hola",
  "pronunciation": "oh-lah",
  "example_sentence": "Hello, how are you?",
  "part_of_speech": "noun",
  "difficulty": "beginner"
}
```

### Quizzes

#### Get Quiz with Questions
```
GET /api/quizzes/{id}
```

#### Get Quizzes by Lesson
```
GET /api/lessons/{lessonId}/quizzes
```

#### Create Quiz
```
POST /api/quizzes
Content-Type: application/json

{
  "lesson_id": 1,
  "title": "Spanish Basics Quiz",
  "description": "Test your knowledge",
  "total_questions": 10,
  "passing_score": 70
}
```

#### Submit Quiz
```
POST /api/quizzes/{id}/submit
Content-Type: application/json

{
  "answers": {
    "1": "2",
    "2": "3",
    "3": "1"
  }
}
```

#### Add Question to Quiz
```
POST /api/quizzes/{id}/questions
Content-Type: application/json

{
  "question_text": "What is hello in Spanish?",
  "question_type": "multiple_choice",
  "order": 1
}
```

#### Add Answer to Question
```
POST /api/questions/{id}/answers
Content-Type: application/json

{
  "answer_text": "Hola",
  "is_correct": true,
  "order": 1
}
```

### Progress

#### Get User Dashboard
```
GET /api/progress/dashboard
```

#### Get User Progress Overview
```
GET /api/progress/overview
```

#### Get Lesson History
```
GET /api/progress/lessons?limit=20
```

#### Get Quiz Results
```
GET /api/progress/quizzes?limit=20
```

#### Get Level Progress
```
GET /api/progress/level/{language}/{level}
```

#### Record Lesson Completion
```
POST /api/progress/lesson/{lessonId}
Content-Type: application/json

{
  "time_spent": 1800
}
```

## Directory Structure

```
php-language-app/
├── api/
│   └── routes.php           # API route definitions
├── config/
│   ├── config.php           # Configuration settings
│   └── Database.php         # Database connection class
├── database/
│   └── schema.sql           # Database schema
├── logs/
│   └── activity.log         # Activity logs
├── public/
│   ├── index.php            # Application entry point
│   ├── css/
│   │   └── style.css        # Main styles
│   ├── js/
│   │   └── app.js           # Main JavaScript
│   └── index.html           # Main HTML
├── src/
│   ├── Controllers/         # Controller classes
│   ├── Models/              # Model classes
│   ├── Router.php           # Routing class
│   └── helpers.php          # Helper functions
├── .gitignore               # Git ignore file
├── README.md                # This file
└── composer.json            # Composer dependencies (optional)
```

## Usage Examples

### Register a New User

```bash
curl -X POST http://localhost/php-language-app/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "john_doe",
    "email": "john@example.com",
    "password": "SecurePassword123",
    "first_name": "John",
    "last_name": "Doe",
    "learning_language": "es"
  }'
```

### Login

```bash
curl -X POST http://localhost/php-language-app/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePassword123"
  }'
```

### Get All Lessons

```bash
curl http://localhost/php-language-app/api/lessons
```

### Search Vocabulary

```bash
curl "http://localhost/php-language-app/api/vocabulary/search?q=hello"
```

## Security Features

- Password hashing with bcrypt (PASSWORD_BCRYPT)
- Input sanitization and validation
- SQL injection prevention with prepared statements
- Session-based authentication
- CSRF protection ready (implement tokens as needed)
- Secure database connection with PDO
- Error logging for debugging

## Performance Optimization

- Database indexing on frequently queried columns
- Efficient pagination for large datasets
- Lazy loading of related data
- Caching-ready architecture
- Optimized database queries

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Roadmap

- [ ] Implement caching system (Redis)
- [ ] Add social features (friend connections, leaderboards)
- [ ] Mobile app development
- [ ] AI-powered content recommendations
- [ ] Spaced repetition algorithm
- [ ] Speech recognition for pronunciation
- [ ] Integration with external APIs (Google Translate, etc.)
- [ ] Admin dashboard
- [ ] Advanced analytics

## Troubleshooting

### Database Connection Error

Make sure your database credentials in `config/config.php` are correct and the MySQL server is running.

### 404 Not Found Errors

Ensure `.htaccess` is in the `public` directory and mod_rewrite is enabled on your Apache server.

### Permission Denied

Make sure `public/` and `logs/` directories have proper permissions:
```bash
chmod -R 755 public/
chmod -R 755 logs/
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please open an issue on GitHub or contact the development team.

## Acknowledgments

- Built with PHP and MySQL
- RESTful API architecture
- Object-oriented design patterns
- Community-driven development
