<?php
/**
 * Lesson Controller
 */

namespace App\Controllers;

use App\Models\Lesson;
use App\Models\Vocabulary;
use Exception;

class LessonController {
    private $lessonModel;
    private $vocabularyModel;

    public function __construct() {
        $this->lessonModel = new Lesson();
        $this->vocabularyModel = new Vocabulary();
    }

    /**
     * Get all lessons
     */
    public function getAllLessons($limit = 50, $offset = 0) {
        try {
            $lessons = $this->lessonModel->getAll($limit, $offset);
            return [
                'success' => true,
                'data' => $lessons,
                'count' => count($lessons)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get lesson by ID with content
     */
    public function getLesson($lessonId) {
        try {
            $lesson = $this->lessonModel->getById($lessonId);
            
            if (!$lesson) {
                throw new Exception('Lesson not found');
            }

            $vocabulary = $this->vocabularyModel->getByLessonId($lessonId);

            return [
                'success' => true,
                'lesson' => $lesson,
                'vocabulary' => $vocabulary,
                'vocabulary_count' => count($vocabulary)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get lessons by language and level
     */
    public function getLessonsByLanguageAndLevel($language, $level) {
        try {
            $lessons = $this->lessonModel->getByLanguageAndLevel($language, $level);
            return [
                'success' => true,
                'data' => $lessons,
                'count' => count($lessons)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get lessons by type
     */
    public function getLessonsByType($type) {
        try {
            $lessons = $this->lessonModel->getByType($type);
            return [
                'success' => true,
                'data' => $lessons,
                'count' => count($lessons)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create new lesson
     */
    public function createLesson($data) {
        try {
            if (empty($data['title']) || empty($data['language']) || empty($data['type'])) {
                throw new Exception('Title, language, and type are required');
            }

            $lessonId = $this->lessonModel->create($data);

            return [
                'success' => true,
                'message' => 'Lesson created successfully',
                'lesson_id' => $lessonId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update lesson
     */
    public function updateLesson($lessonId, $data) {
        try {
            $lesson = $this->lessonModel->getById($lessonId);
            if (!$lesson) {
                throw new Exception('Lesson not found');
            }

            $this->lessonModel->update($lessonId, $data);

            return [
                'success' => true,
                'message' => 'Lesson updated successfully'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete lesson
     */
    public function deleteLesson($lessonId) {
        try {
            $lesson = $this->lessonModel->getById($lessonId);
            if (!$lesson) {
                throw new Exception('Lesson not found');
            }

            $this->lessonModel->delete($lessonId);

            return [
                'success' => true,
                'message' => 'Lesson deleted successfully'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
