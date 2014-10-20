<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});
Route::get('api/tasks',function(){
    $headers = [
    'Access-Control-Allow-Origin'      => '*',
];
    $tasks = Task::all(array('id','text','updated_at','created_by_user_id as authorId','status'));
    return Response::json($tasks,200,$headers);
});

Route::get('api/{username}/tasks',function($username){
        $user = User::where('username', '=', $username)->firstOrFail();
        return Response::json($user->tasks);
});

Route::get('api/task/{id}/users',function($id){
    $task = Task::findOrFail($id);
    return Response::json($task->users);
});