<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerCommentRequest;
use App\Http\Requests\AnswerRequest;
use App\Models\Answer;
use App\Models\Assay;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
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

        $assay = $answer->assay()->with('questions.alternatives')->first();
        $teacher = $assay->teacher()->first();
        $answers_templates = $answer->answer_template()->get();


        $template_with_corrections = $assay->questions()->get(
        )->map(function ($question) use ($answers_templates) {
            
            $answer_template = $answers_templates->firstWhere('question_id', $question->id);

            $alternatives = $question->alternatives()->get(
            )->map(function ($alternative) {
                
                return [

                    'description' => $alternative->description,
                    'id' => $alternative->id,
                ];
            });

            return [
                'asking' => $question->asking,
                'correct' => $question->correct_alternative,
                'answered' => $answer_template->answered_alternative_id,
                'alternatives' => $alternatives,
            ];
        }, $answers_templates);

        


        return response()->json([
            'message' => 'answer getted sucessfully',
            'answer_template' => [
                'assay' => [
                    'title' => $assay->title,
                    'final_date' => $assay->final_date,
                    'class' => $assay->class()->first()->tag,
                    'subject' => $assay->subject()->first()->subject_name,
                    'teacher' => [
                        'name' => $teacher->user()->first()->name,
                        'id' => $teacher->id,
                    ],
                ],
                'template' => [
                    'header' => [
                        'id' => $answer->id,
                        'grade' => $answer->grade()->first()->grade ?? 'in process',
                        'student' => [
                            'id' => $answer->student()->first()->id,
                            'name' => $answer->student()->first()->user()->first()->name,
                        ],
                        'assay_id' => $answer->assay_id,
                        'comment' => $answer->comment ?? 'No comments yet',
                    ],
                ],
                'body' => $template_with_corrections,
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
                'answered_alternative_id' => $answer_template_req['alternative_id'],
            ];
        }, $answer_req['answers']);
        $answer->answer_template()->createMany($answers_templates);

        $assay = $answer->assay()->first();
        
        $answers_template = $answer->answer_template()->get();


        $result = $assay->questions()->get()->map(function ($question) use ($answers_template) {
            $answer_template = $answers_template->firstWhere('question_id', $question->id);
            
            return $answer_template->answered_alternative_id == $question->correct_alternative;
        })->reduce(function ($carry, $item) {
            return $carry + ($item ? 1 : 0);
        }, 0);

        Grade::create([
            'answer_id' => $answer->id,
            'student_id' => $answer->student()->first()->id,
            'grade' => $result
        ]);


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
                'grade' => $answer->grade()->first()->score ?? 'in process',
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
                'grade' => $answer->grade()->first()->score ?? 'in process',
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

