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
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE');
Route::get('/', function() {
    return View::make('hello');
});
Route::get('api/tasks', function() {
    $headers = [
            // 'Access-Control-Allow-Origin'      => '*',
    ];
    $tasks = Task::all(array('id', 'text', 'updated_at', 'created_by_user_id as authorId', 'status', 'priority'));
    //$tasks = Task::all();
    return Response::json($tasks, 200, $headers);
});

Route::get('api/users', function() {
    $headers = [
            //'Access-Control-Allow-Origin'      => '*',
    ];
    $users = User::all();
    return Response::json($users, 200, $headers);
});
Route::get('api/{username}/tasks', function($username) {
    $user = User::where('username', '=', $username)->firstOrFail();
    return Response::json($user->tasks);
});

Route::any('api/task', function() {
    $headers = [
            //'Access-Control-Allow-Origin'      => '*',
    ];
    $userId = Input::get('userId');
    $newTaskText = Input::get('newTaskText');
    $task = new Task;
    $task->text = $newTaskText;
    $task->creator()->associate(User::findOrFail($userId));
    $task->save();
    
    //$priority = Task::savePriority($task->id);
    //$task->priority = $priority;
    return Response::json($task);
});

Route::get('api/task/{id}/users', function($id) {
    $task = Task::findOrFail($id);
    return Response::json($task->users);
});

Route::delete('api/task/{taskId}',function($taskId){
    $task = Task::findOrFail($taskId);
    $task->delete();
    //Todo remove the priority
    return 'done';
});

