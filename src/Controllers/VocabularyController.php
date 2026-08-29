<?php
/**
 * Vocabulary Controller
 */

namespace App\Controllers;

use App\Models\Vocabulary;
use Exception;

class VocabularyController {
    private $vocabularyModel;

    public function __construct() {
        $this->vocabularyModel = new Vocabulary();
    }

    /**
     * Add vocabulary word
     */
    public function addWord($data) {
        try {
            if (empty($data['word']) || empty($data['translation'])) {
                throw new Exception('Word and translation are required');
            }

            $wordId = $this->vocabularyModel->addWord($data);

            return [
                'success' => true,
                'message' => 'Word added successfully',
                'word_id' => $wordId
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get word by ID
     */
    public function getWord($wordId) {
        try {
            $word = $this->vocabularyModel->getById($wordId);
            
            if (!$word) {
                throw new Exception('Word not found');
            }

            return [
                'success' => true,
                'data' => $word
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get vocabulary by lesson
     */
    public function getVocabularyByLesson($lessonId) {
        try {
            $vocabulary = $this->vocabularyModel->getByLessonId($lessonId);
            return [
                'success' => true,
                'data' => $vocabulary,
                'count' => count($vocabulary)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Search vocabulary
     */
    public function search($query, $limit = 20) {
        try {
            if (empty($query)) {
                throw new Exception('Search query is required');
            }

            $results = $this->vocabularyModel->search($query, $limit);
            return [
                'success' => true,
                'data' => $results,
                'count' => count($results)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get random vocabulary
     */
    public function getRandomVocabulary($limit = 10, $difficulty = null) {
        try {
            $vocabulary = $this->vocabularyModel->getRandom($limit, $difficulty);
            return [
                'success' => true,
                'data' => $vocabulary,
                'count' => count($vocabulary)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get vocabulary by difficulty
     */
    public function getByDifficulty($difficulty, $limit = 50) {
        try {
            $vocabulary = $this->vocabularyModel->getByDifficulty($difficulty, $limit);
            return [
                'success' => true,
                'data' => $vocabulary,
                'count' => count($vocabulary)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update word
     */
    public function updateWord($wordId, $data) {
        try {
            $word = $this->vocabularyModel->getById($wordId);
            if (!$word) {
                throw new Exception('Word not found');
            }

            $this->vocabularyModel->update($wordId, $data);

            return [
                'success' => true,
                'message' => 'Word updated successfully'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete word
     */
    public function deleteWord($wordId) {
        try {
            $word = $this->vocabularyModel->getById($wordId);
            if (!$word) {
                throw new Exception('Word not found');
            }

            $this->vocabularyModel->delete($wordId);

            return [
                'success' => true,
                'message' => 'Word deleted successfully'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
