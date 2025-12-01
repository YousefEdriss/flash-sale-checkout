<?php
namespace App\Exceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    public function register(){}
    public function render($request, Throwable $e){
        return response()->json(['error'=> $e->getMessage()], 500);
    }
}
