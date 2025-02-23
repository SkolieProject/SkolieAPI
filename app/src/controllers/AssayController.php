<?php

namespace Minuz\SkolieAPI\controllers;

use Minuz\SkolieAPI\attributes\Route;
use Minuz\SkolieAPI\http\Request;
use Minuz\SkolieAPI\http\Response;
use Minuz\SkolieAPI\model\Role\Role;
use Minuz\SkolieAPI\repo\AssayRepository;
use Minuz\SkolieAPI\services\Sessioner;
use Minuz\SkolieAPI\tools\Parser;

class AssayController
{
  private AssayRepository $assay_repository;


  public function __construct()
  {
    $this->assay_repository = new AssayRepository();
  }



  #[Route('/assay/', 'POST')]
  public function newAssay(Request $request, Response $response): void
  {
    if ($this->isExpired($request, $response, $credentials)) {
      return;
    }

    $repository = $this->assay_repository->checkRole($credentials->id);
    if ($repository->user_role != Role::Teacher) {
      $response::Response(400, 'Error', 'Error : You are not allowed to make assays');
      return;
    }

    $assay_info = $request::body();
    $have_basics = Parser::HaveValues($assay_info, ['class', 'title', 'deadline']);
    if ($have_basics == false) {
      $response::Response(400, 'Error', 'Error on assay info: Too few information');
      return;
    }


    $repository->newAssay($credentials->id, $assay_info);

    if ($repository->exit_code == true) {
      $response::Response(400, 'Error', 'Error on assay info: Verify and try again');
      return;
    }

    $assay = $repository->assay;
    $response::Response(200, 'OK', 'Assay registred sucessfully', ['Assay' => $assay->overview()]);
  }








  #[Route('/assay/{id}', 'POST')]
  public function rewriteAssay(Request $request, Response $response, int $assay_id)
  {
    if ($this->isExpired($request, $response, $credentials)) {
      return;
    }

    $repository = $this->assay_repository->checkRole($credentials->id);
    if ($repository->user_role != Role::Teacher) {
      $response::Response(400, 'Error', 'Error : You are not allowed to make assays');
      return;
    }

    $questions = $request::body();

    $repository = $this->assay_repository->writeQuestions($credentials->id, $assay_id, $questions);
    if ($repository->exit_code == true) {
      $response::Response(400, 'Error', 'Error on rewriting questions, please try again');
      return;
    }

    $repository->getAssay($credentials->id, $assay_id);
    $assay = $repository->assay;
    $response::Response(200, 'OK', 'Your assay was rewrited sucessfully', $assay->overview());
  }







  #[Route('/assay/{id}', 'GET')]
  public function getAssay(Request $request, Response $response, int $assay_id)
  {

    if ($this->isExpired($request, $response, $credentials)) {
      return;
    }

    $repository = $this->assay_repository->getAssay($credentials->id, $assay_id);

    if ($repository->exit_code == true) {
      $response::Response(400, 'Error', 'Error trying to acess assay, verify and try again');
      return;
    }

    $assay = $repository->assay;
    $response::Response(200, 'OK', 'Assay acessed sucessfully', $assay->overview());
  }



  #[Route('/assayGroup?{query}')]
  public function getAssayGroup(Request $request, Response $response, array $query)
  {
    if ($this->isExpired($request, $response, $credentials)) {
      return;
    }

    $query_marked = Parser::HaveValues($query, ['classes']);
    if ($query_marked['classes'] == false) {
      $response::Response(400, 'Error', 'Invalid query structure');
    }

    $repository = $this->assay_repository->checkRole($credentials->id);
    if ($repository->user_role == Role::Teacher) {
      $repository->getTeacherAssays($credentials->id);

      $assays = array_map(
        function ($assay) {
          return $assay->overview();
        },
        $repository->assay_group
      );


      $response::Response(200, 'OK', 'Assays from teacher getted sucessfully', $assays);
      return;
    }
  }





  private function isExpired(Request $request, Response $response, &$credentials = null): bool
  {
    $credentials = Sessioner::assertSession($request::session());
    if ($credentials === false) {
      $response::Response(400, 'Error', 'Your login has expired');
      return true;
    }

    return false;
  }
}
