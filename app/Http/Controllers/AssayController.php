<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssayRequest;
use App\Http\Requests\AssayRewriteRequest;
use App\Models\Alternative;
use App\Models\Answer;
use App\Models\Assay;
use App\Models\Question;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AssayController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Assay::class, 'assay');
    }

    /**
     * Display the specified resource.
     */
    public function show(Assay $assay): JsonResponse
    {
        return response()->json([
            'assay_header' => $assay->makeHidden('questions'),
            'assay_body' => $this->assayBodyHandler($assay),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $assays = match ($request->user()->role) {
            'STDNT' => $this->getStudentAssays($request),
            'TCHR' => $this->getTeacherAssays($request),
            default => collect([])
        };

        return response()->json([
            'message' => 'Assays retrieved successfully',
            'assays' => $assays
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(AssayRequest $request): JsonResponse
    {
        $assay_req = $request->validated();
        $teacher = Teacher::where('user_id', Auth::id())->first();
        
        $assay = Assay::create([
            'title' => $assay_req['title'],
            'deadline' => $assay_req['deadline'],
            'subject_id' => $assay_req['subject_id'],
            'teacher_id' => $teacher->id,
            'class_tag_id' => $assay_req['class_tag_id'] ?? null
        ]);

        foreach ($request['questions'] as $request_question) {

            $question = Question::create([
                'assay_id' => $assay->id,
                'asking' => $request_question['asking']
            ]);
            foreach ($request_question['alternatives'] as $request_alternative) {
            
                $alternative = Alternative::create([
                    'question_id' => $question->id,
                    'description' => $request_alternative['description']
                ]);
                if (isset($request_alternative['is_correct'])) {
                    $question->update([
                        'correct_alternative' => $alternative->id
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Assay created successfully',
            'assay_header' => $assay,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssayRewriteRequest $request, Assay $assay): JsonResponse
    {
        //TODO: controller do update: pegar a entidade
        
        $assay_req = $request->validated();

        $assay->update([
            'title' => $assay_req['title'] ?? $assay->title,
            'deadline' => $assay_req['deadline'] ?? $assay->deadline,
            'class_tag_id' => $assay_req['class_tag_id'] ?? $assay->class_tag_id,
            'subject_id' => $assay_req['subject_id'] ?? $assay->subject_id,
        ]);

        
        
        return response()->json([
            'message' => 'Assay updated successfully',
            'assay' => $assay
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assay $assay): JsonResponse
    {
        $assay->delete();

        return response()->json([
            'message' => 'Assay deleted successfully'
        ]);
    }

    private function getStudentAssays(Request $request): mixed
    {
        $student = Student::where('user_id', $request->user()->id)->first();
        $assays = Assay::where('class_tag_id', $student->class_tag_id)
            ->where('is_answerable', true);        

        if ($request->boolean('pendant')) {
            
            $assays->whereDoesntHave('answers', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            });
        }
        
        if ($subject_query = $request->input('subject')) {
            
            $assays->where(function($query) use ($subject_query) {
                $query->where('subject_id', $subject_query);
            });
        }

        return $assays->get();
    }

    private function getTeacherAssays(Request $assay_req): mixed
    {
        $teacher = Teacher::where('user_id', $assay_req->user()->id)->first();
        $assays = Assay::where('teacher_id', $teacher->id);

        if ($assay_req->has('visible')) {
            $assays->where('is_visible', $assay_req->boolean('visible'));
        }

        if ($assay_req->has('answerable')) {
            $assays->where('is_answerable', $assay_req->boolean('answerable'));
        }

        return $assays->get();
    }

    private function assayBodyHandler(Assay $assay)
    {
        $user = Auth::user();

        return match ($user->role) {
            'STDNT' => $this->assayBodyForStudent($assay),
            'TCHR' => $this->assayBodyForTeacher($assay),
            default => null
        };
    }

    private function assayBodyForTeacher(Assay $assay)
    {
        $questions = $assay->questions()->with('alternatives')->get();
        return $questions->map(function ($question){
            return [
                'id' => $question->id,
                'asking' => $question->asking,
                'correct_alternative' => $question->correct_alternative,
                'alternatives' => $question->alternatives->map(function ($alternative) {
                    return [
                        'id' => $alternative->id,
                        'description' => $alternative->description
                    ];
                })
            ];
        });
    }


    private function assayBodyForStudent(Assay $assay)
    {
        $questions = $assay->questions()->with('alternatives')->get();
        return $questions->map(function ($question){
            return [
                'id' => $question->id,
                'asking' => $question->asking,
                'alternatives' => $question->alternatives->map(function ($alternative) {
                    return [
                        'id' => $alternative->id,
                        'description' => $alternative->description
                    ];
                })
            ];
        });
    }
}

