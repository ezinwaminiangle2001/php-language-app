<?php
/**
 * Quiz Controller
 */

namespace App\Controllers;

use App\Models\Quiz;
use App\Models\Progress;
use Exception;

class QuizController {
    private $quizModel;
    private $progressModel;

    public function __construct() {
        $this->quizModel = new Quiz();
        $this->progressModel = new Progress();
    }

    /**
     * Get quiz with questions
     */
    public function getQuiz($quizId) {
        try {
            $quiz = $this->quizModel->getById($quizId);
            
            if (!$quiz) {
                throw new Exception('Quiz not found');
            }

            $questions = $this->quizModel->getQuestions($quizId);

            // Get answers for each question
            foreach ($questions as &$question) {
                $question['answers'] = $this->quizModel->getAnswers($question['id']);
            }

            return [
                'success' => true,
                'quiz' => $quiz,
                'questions' => $questions,
                'total_questions' => count($questions)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get quizzes for a lesson
     */
    public function getQuizzesByLesson($lessonId) {
        try {
            $quizzes = $this->quizModel->getByLessonId($lessonId);
            return [
                'success' => true,
                'data' => $quizzes,
                'count' => count($quizzes)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create quiz
     */
    public function createQuiz($data) {
        try {
            if (empty($data['lesson_id']) || empty($data['title'])) {
                throw new Exception('Lesson ID and title are required');
            }

            $quizId = $this->quizModel->create($data);

            return [
                'success' => true,
                'message' => 'Quiz created successfully',
                'quiz_id' => $quizId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Add question to quiz
     */
    public function addQuestion($quizId, $data) {
        try {
            if (empty($data['question_text'])) {
                throw new Exception('Question text is required');
            }

            $questionId = $this->quizModel->addQuestion($quizId, $data);

            return [
                'success' => true,
                'message' => 'Question added successfully',
                'question_id' => $questionId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Add answer option
     */
    public function addAnswer($questionId, $data) {
        try {
            if (empty($data['answer_text'])) {
                throw new Exception('Answer text is required');
            }

            $answerId = $this->quizModel->addAnswer($questionId, $data);

            return [
                'success' => true,
                'message' => 'Answer added successfully',
                'answer_id' => $answerId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Submit quiz answers
     */
    public function submitQuiz($userId, $quizId, $answers) {
        try {
            $quiz = $this->quizModel->getById($quizId);
            if (!$quiz) {
                throw new Exception('Quiz not found');
            }

            $questions = $this->quizModel->getQuestions($quizId);
            $correctCount = 0;
            $startTime = isset($_SESSION['quiz_start_time']) ? $_SESSION['quiz_start_time'] : time();
            $timeSpent = time() - $startTime;

            // Grade the quiz
            foreach ($questions as $question) {
                $correctAnswer = $this->getCorrectAnswer($question['id']);
                $userAnswer = $answers[$question['id']] ?? null;

                if ($userAnswer == $correctAnswer) {
                    $correctCount++;
                }
            }

            $score = ($correctCount / count($questions)) * 100;

            // Record result
            $this->progressModel->recordQuizResult(
                $userId,
                $quizId,
                round($score, 2),
                count($questions),
                $timeSpent
            );

            $passed = $score >= $quiz['passing_score'];

            return [
                'success' => true,
                'score' => round($score, 2),
                'correct_answers' => $correctCount,
                'total_questions' => count($questions),
                'passed' => $passed,
                'message' => $passed ? 'Quiz passed!' : 'Quiz failed. Try again!'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get correct answer for a question
     */
    private function getCorrectAnswer($questionId) {
        $answers = $this->quizModel->getAnswers($questionId);
        foreach ($answers as $answer) {
            if ($answer['is_correct']) {
                return $answer['id'];
            }
        }
        return null;
    }

    /**
     * Update quiz
     */
    public function updateQuiz($quizId, $data) {
        try {
            $quiz = $this->quizModel->getById($quizId);
            if (!$quiz) {
                throw new Exception('Quiz not found');
            }

            $this->quizModel->update($quizId, $data);

            return [
                'success' => true,
                'message' => 'Quiz updated successfully'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete quiz
     */
    public function deleteQuiz($quizId) {
        try {
            $quiz = $this->quizModel->getById($quizId);
            if (!$quiz) {
                throw new Exception('Quiz not found');
            }

            $this->quizModel->delete($quizId);

            return [
                'success' => true,
                'message' => 'Quiz deleted successfully'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
