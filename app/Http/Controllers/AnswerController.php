<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerCommentRequest;
use App\Http\Requests\AnswerRequest;
use App\Models\Answer;
use App\Models\Assay;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $answers = match ($request->user()->role) {
            "STDNT" => $this->getAnswersLikeStudent($request),
            "TCHR" => $this->getAnswersLikeTeacher($request),
            default => collect([])
        };

        return response()->json([
            'message' => 'answers getted sucessfully',
            'answers' => $answers,
        ]);
    }


    public function show(Answer $answer): JsonResponse
    {
        return response()->json([
            'message' => 'answer getted sucessfully',
            'assay' => $answer->assay()->with('questions.alternatives')->get(),
            'answer' => [
                'answer_header' => [
                    'id' => $answer->id,
                    'student' => [
                        'id' => $answer->student()->first()->id,
                        'name' => $answer->student()->first()->user()->first()->name,
                    ],
                    'assay_id' => $answer->assay_id,
                ],
                'answer_body' => $answer->answer_template()->get(),
            ]
        ]);
    }

    public function store(AnswerRequest $request): JsonResponse
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        $assay = Assay::find($request->assay_id);
        if ($assay->is_answerable == false) {
            return response()->json([
                'message' => 'Assay is not answerable',
            ], 422);
        }


        // Check if the student already has an answer for the given assay
        $answer_exists = Answer::where('student_id', $student->id)
            ->where('assay_id', $request->assay_id)->exists();
        
        if ($answer_exists) {
            return response()->json([
                'message' => 'Answer already exists',
            ], 422);

        }

        $answer_req = $request->validated();
        
        $answer = Answer::create([
            'student_id' => Student::where('user_id', Auth::id())->first()->id,
            'assay_id' => $answer_req['assay_id'],
        ]);

        $answers_templates = array_map(function ($answer_template_req) use ($answer) {
            return [
                'answer_id' => $answer->id,
                'question_id' => $answer_template_req['question_id'],
                'alternative_id' => $answer_template_req['alternative_id'],
            ];
        }, $answer_req['answers']);
        $answer->answer_template()->createMany($answers_templates);

        return response()->json([
            'message' => 'Answer stored successfully',
            'answer_header' => $answer,
            'answer_body' => $answer->answer_template()->get(),
        ]);

    }

    public function update(AnswerCommentRequest $request, Answer $answer): JsonResponse
    {   
        //TODO: comentários da resposta volta nulo
        $request_comment = $request->validated();
        $answer->update([
            'comment' => $request_comment['comment'] ?? $answer->comment
        ]);

        return response()->json([
            'message' => 'comment added succesfully',
            'answer' => $answer,
        ]);
    }


    private function getAnswersLikeStudent(Request $request)
    {
        $student = Student::where('user_id', $request->user()->id)->first();
        $answers = $student->answers();
        if ($assay_query = $request->input('assay')) {
            
            $answers->where('assay_id', $assay_query);
        }
        
        if ($subject_query = $request->input('subject')) {
            
            $subject_query_id = Subject::where('subject_name', $subject_query);
            $answers->whereHas('assay', function($query) use ($subject_query_id) {
                $query->where('subject_id', $subject_query_id);
            });
        } 
        return $answers->get()->map(function ($answer) {
            
            return [
                'id' => $answer->id,
                'student' => [
                    'name' => $answer->student()->first()->user()->first()->name,
                    'id' => $answer->student()->first()->id,
                ],
                'assay' => $answer->assay()->first()->title,
                'comment' => $answer->comment ?? 'No comments yet'
            ];
        });
    }



    private function getAnswersLikeTeacher(Request $request)
    {
        $teacher = Teacher::where('user_id', $request->user()->id)->first();
        
        $answers = Answer::whereHas('assay', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        });

        if ($class_query = $request->input('class')) {
            
            $answers->whereHas('assay', function($query) use ($class_query) {
                $query->where('class_tag_id', $class_query);
            });
        }

        if ($assay_query = $request->input('assay')) {

            $answers->where('assay_id', $assay_query);
        }

        return $answers->get()->map(function ($answer) {
            return [
                'id' => $answer->id,
                'student' => [
                    'name' => $answer->student()->first()->user()->first()->name,
                    'id' => $answer->student()->first()->id,
                ],
                'assay' => $answer->assay()->first()->title,
                'comment' => $answer->comment ?? 'No comments yet'
            ];
        });
    }
}

