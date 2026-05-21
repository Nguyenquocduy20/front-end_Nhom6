<?php
use App\Http\Controllers\Api\ExamApiController;
use App\Http\Controllers\Api\AnswerApiController;
use Illuminate\Support\Facades\Route;

// 1. API lấy danh sách câu hỏi và đáp án của đề thi (Đã tối ưu Cache)
Route::get('/exam/{quiz_id}', [ExamApiController::class, 'getExam']);

// 2. API lưu đáp án từng câu khi sinh viên tích chọn (Auto-save qua AJAX)
Route::post('/save-answer', [AnswerApiController::class, 'saveStudentAnswer']);
Route::get('/quiz-questions/{id}', [App\Http\Controllers\Api\QuizApiController::class, 'getQuestions']);