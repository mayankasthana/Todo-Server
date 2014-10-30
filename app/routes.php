<?php

require_once app_path() . '/../vendor/google/apiclient/autoload.php'; // or wherever autoload.php is located
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
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, access-token');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE');
Route::get('/', function() {

    return View::make('hello');
});

Route::get('setup/database/migrate', function() {
    Artisan::call('migrate', array('--force' => true));
    return 'done';
});
Route::get('setup/database/seed', function() {
    Artisan::call('db:seed', array('--force' => true));
    return 'done';
});

Route::post('api/login', function() {
    $authCode = Input::get('code');
    $access_token = Input::get('access_token');
    $client = new Google_Client();
    $client->setClientId("444337330755-p48vqremkchmm6veish2k4rb6bgugf1u.apps.googleusercontent.com");
    $client->setClientSecret("PdwKmpApHALa4dIVFdgauFmd");
    $client->setRedirectUri("postmessage");

    $client->authenticate($authCode);
    $token = json_decode($client->getAccessToken());
    //Match access_token as received from the client and from the google server
    if ($access_token == $token->access_token) {
        //Get name, email, details
        $plus = new Google_Service_Plus($client);
        $me = $plus->people->get('me');
        //Check new user
        $isNew = !User::isUserWithOuidExists($me->id);
        if ($isNew) {
            //If new, save details
            User::saveGPlusUser($me);
        }
        //store access Token
        User::where('ouid', '=', $me->id)->update(array('access_token' => $access_token, 'access_token_time' => $token->created));
        $user = User::where('ouid', $me->id)->get()->first();
        Event::fire('user.logged-in', $user);
        if ($isNew) {
            Event::fire('user.new-added', $user);
        }
        return Response::json(array(
                    'id' => $user->id,
                    'displayName' => $user->displayName
                        ), 200);
    } else {
        $message = [
            "error" => [
                "code" => 401,
                "message" => "Invalid Credentials",
                "Client accessToken" => $access_token,
                "Google accessToken" => $token->access_token
            ]
        ];
        return Response::json($message, 401);
    }
});
Route::group(array('before' => 'auth.basic'), function() {
    TodoController::initEvents();
    
    Route::get('api/me', function() {
        return GAuth::user();
    });
    
    Route::put('api/task/{taskId}/priority/{action}', function($taskId, $action) {
        //Input is increase priority of id;
        //Or decrease priority id;
        //{action:'inc',task_id: 'id'}
        if ($action == 'inc') {
            Task::increasePriority($taskId);
            //return Resposnse::json([], 409);
        } else if ($action == 'dec') {
            Task::decreasePriority($taskId);
        }
        Event::fire('task.change-priority', array(array('taskId' => $taskId, 'action' => $action)));
        return Response::json(Task::getAllPriorityList(), 200);
    }
    );

    Route::get('api/tasks', function() {
        $userId = GAuth::user()['id'];
        $tasks = User::associatedTasks($userId);
        return $tasks;
    });

    Route::get('api/users', function() {
        $users = User::all();
        return Response::json($users);
    });
    Route::get('api/{username}/tasks', function($username) {
        $user = User::where('username', '=', $username)->firstOrFail();
        return Response::json($user->tasks);
    });
    Route::get('api/task/{taskId}/comments', function($taskId) {
        $task = Task::findOrFail($taskId);
        return $task->comments();
    });
    Route::put('api/task/{taskId}/comment', function($taskId) {
        $commentText = Input::get('comment');
        $userId = Input::get('userId');
        $task = Task::findOrFail($taskId);
        $user = User::findOrFail($userId);
        $comment = $task->addcomment($commentText, $userId);
        Event::fire('task.new-comment', array(array('task' => $task, 'user' => $user, 'comment' => $comment)));
        return $comment;
    });

    Route::post('api/task', function() {
        $userId = GAuth::user()['id'];
        $newTaskText = Input::get('newTaskText');
        $task = new Task;
        $task->text = $newTaskText;
        $task->creator()->associate(User::findOrFail($userId));
        $task->status = 0;
        $task->save();

        //$priority = Task::savePriority($task->id);
        //$task->priority = $priority;
        return Response::json($task);
    });

    Route::get('api/task/{id}/users', function($id) {
        $task = Task::findOrFail($id);
        $membersId = array();
        foreach ($task->users as $user) {
            array_push($membersId, $user->id);
        }
        return Response::json($membersId);
    });

    Route::delete('api/task/{taskId}', function($taskId) {
        Event::fire('task.deleted', $taskId);
        $task = Task::findOrFail($taskId);
        $task->delete();
        //Todo remove the priority
        return 'done';
    });

    Route::put('api/task/{taskId}/status/{status}', function($taskId, $status) {
        //$task = Task::findOrFail($taskId);
        Task::setStatus($taskId, $status);
        Event::fire('task.status-changed', array(array('taskId' => $taskId, 'status' => $status)));
        return Task::getAllPriorityList();
    });

    Route::post('api/task/{taskId}/users', function($taskId) {
        $task = Task::findOrFail($taskId);
        //[1,2,3]
        $members = Input::get('ids');
        $memberIds = $members;
        $res = $task->addMembers($memberIds);
        Event::fire('task.members-added', array(array('taskId' => $taskId, 'memberIds' => $memberIds)));
        return Response::json($res);
    });

    Route::post('api/task/{taskId}/users/del', function($taskId) {
        $task = Task::findOrFail($taskId);
        //[1,2,3]
        $members = Input::get('ids');
        $memberIds = $members;
        $res = $task->removeMembers($memberIds);
        Event::fire('task.members-removed', array(array('taskId' => $taskId, 'memberIds' => $memberIds)));
        return Response::json($res);
    });
});
