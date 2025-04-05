<?php

namespace App\Http\Controllers;

use App\Models\Assay;
use Illuminate\Http\JsonResponse;

class AssaySettingsController extends Controller
{
  public function toggleVisibility(Assay $assay): JsonResponse
  {
    if (! $assay->class_tag_id) {
      return response()->json([
        'message' => 'Assay is not assigned to any class',
      ], 400);
    }
    
    $assay->update(['is_visible' => !$assay->is_visible]);

    return response()->json([
      'message' => "Assay visibility is now " . ($assay->is_visible ? 'on' : 'off'),
    ]);
  }

  public function toggleAnswerability(Assay $assay): JsonResponse
  {
    if (! $assay->class_tag_id) {
      return response()->json([
        'message' => 'Assay is not assigned to any class',
      ], 400);
    }

    $assay->update(['is_answerable' => !$assay->is_answerable]);

    return response()->json([
      'message' => "Assay answerability is now " . ($assay->is_answerable ? 'on' : 'off'),
    ]);
  }
}
