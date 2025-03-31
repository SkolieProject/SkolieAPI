<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssayRequest;
use App\Http\Requests\AssayRewriteRequest;
use App\Models\Alternative;
use App\Models\Answer;
use App\Models\Assay;
use App\Models\Question;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssayController extends Controller
{

    public function getAssay(Assay $assay)
    {
        $questions = Question::where('assay_id', $assay->id)->get();
        $questionsinfo = $questions->map(function ($question) {
            $alternatives = Alternative::where('question_id', $question->id)->get();
            $questioninfo = [
                'question_text' => $question->question_text,
                'alternatives' => $alternatives
            ];
            if (Auth::user()->role === 'TCHR') {
                $questioninfo['correct_alternative'] = $question->correct_alternative;
            }
            return $questioninfo;
        });

        return response()->json([
            'title' => $assay->title,
            'deadline' => $assay->deadline,
            'questions' => $questionsinfo
        ]);
    }


    public function getAssays(Request $request)
    {
        if ($request->user()->role === 'STDNT') {

            $student = Student::where('user_id', $request->user()->id)->first();
            $assays = Assay::where('class_tag_id', $student->class_tag_id)->get();
            


            // Aluno quer provas pendentes
            if ($request->query('pendant') != null) {
                $assays->filter(function ($assay) use ($student) {
                    return Answer::where([
                        ['student_id', $student->id],
                        ['assay_id', $assay->id]
                        ])->first() == null;
                    }
                );
            }
            
            
        }
        else if ($request->user()->role === 'TCHR') {
            $teacher = Teacher::where('user_id', $request->user()->id)->first();
            $assays = Assay::where('teacher_id', $teacher->id)->get();
            
            if ($visibility = $request->query('visible') != null) {
                $assays->filter(function ($assay) use ($visibility) {
                    return $assay->is_visible === $visibility;
                });
            }
            
            if ($answerability = $request->query('answerable') != null) {
                $assays->filter(function ($assay) use ($answerability) {
                    return $assay->is_answerable === $answerability;
                });
            }        
        }

        return response()->json([
            'message' => 'assays getted sucessfully',
            'assays' => $assays
        ]);
    }


    public function newAssay(AssayRequest $request)
    {
        $assay_request = $request->validated();

        $user_id = Auth::id();
        $teacher = Teacher::where('user_id', $user_id)->first();        
        
        $assay = Assay::create([
            'title' => $assay_request['title'],
            'deadline' => $assay_request['deadline'],
            'subject_id' => $assay_request['subject_id'],
            'teacher_id' => $teacher->id,
            'class_tag_id' => $assay_request['class_tag_id'] ?? null
        ]);
        

        foreach ($assay_request['questions'] as $question_request) {
            $question = $assay->questions()->create([
                'assay_id' => $assay->id,
                'question_text' => $question_request['question_text'],
            ]);

            foreach ( $question_request['alternatives'] as $alternative_request) {
                $alternative = $question->alternatives()->create([
                    'question_id' => $question->id,
                    'alternative_text' => $alternative_request['alternative_text']
                ]);

                if (isset($alternative_request['is_correct'])) {
                    $question->update(['correct_alternative' => $alternative->id]);
                }
            }
        }

        return response()->json([
            'message' => 'Assay created successfully',
            'assay' => $assay
        ], 201);

    }


    public function rewriteAssay(AssayRewriteRequest $request)
    {
        $assay_request = $request->validated();
        $assay = Assay::findOrFail($request->id);
    
        $assay->update([
            'title' => $assay_request['title'] ?? $assay->title,
            'deadline' => $assay_request['deadline'] ?? $assay->deadline,
            'class_tag_id' => $assay_request['class_tag_id'] ?? $assay->class_tag_id,
            'subject_id' => $assay_request['subject_id'] ?? $assay->subject_id,
        ]);
    
        if (! isset($assay_request['questions'])) {
            return response()->json([
                'message' => 'Assay updated successfully',
                'assay' => $assay
            ], 200);
        }

        foreach ($assay_request['questions'] as $question_request) {
            $question = $assay->questions()->updateOrCreate(
                ['id' => $question_request['id']],
                [
                    'question_text' => $question_request['question_text'],
                    'is_correct' => $question_request['is_correct'],
                ]
            );
    
            foreach ($question_request['alternatives'] as $alternative_request) {
                $alternative = $question->alternatives()->updateOrCreate(
                    ['id' => $alternative_request['id']],
                    [
                        'alternative_text' => $alternative_request['alternative_text'],
                        'is_correct' => $alternative_request['is_correct'],
                    ]
                );

                if (isset($alternative_request['is_correct'])) {
                    $question->update(['correct_alternative' => $alternative->id]);
                }
            }
        }
    
        return response()->json([
            'message' => 'Assay updated successfully',
            'assay' => $assay
        ], 200);
    }


    public function eraseAssay(Assay $assay)
    {
        $assay->delete();

        return response()->json(['message' => 'Assay deleted sucessfully']);
    }



    public function toggleViewAssay(Assay $assay)
    {
        $toggle_state = 'on';
        if ($assay->is_visible) {
            $assay->udpate('is_visible', false);
            $toggle_state = 'off';
        }
        $assay->udpate('is_visible', true);
        
        
        return response()->json([
            'message' => "Assay $assay->title visibility is now $toggle_state",
        ]);
    }


    public function answerViewAssay(Assay $assay)
    {
        $toggle_state = 'on';
        if ($assay->is_visible) {
            $assay->udpate('is_answerable', false);
            $toggle_state = 'off';
        }
        $assay->udpate('is_answerable', true);


        return response()->json([
            'message' => "Assay $assay->title answerability is now $toggle_state",
        ]);
    }
}
