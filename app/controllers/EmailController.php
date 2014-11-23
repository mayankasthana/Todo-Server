<?php

class EmailController extends Controller {

    public function __construct() {
        EmailController::initEmailListeners();
    }

    public function sendDeferredEmails() {
        $deferredEmails = Email::where('send_status', false)
                ->where('time_to_send', '<=', DB::raw('CURRENT_TIMESTAMP'))
                ->take(3)
                ->get();
        foreach ($deferredEmails as $defEmail) {
            Mail::send('emails.blank', array('msg' => $defEmail->body), function($message) use($defEmail) {
                $message->to($defEmail->to_email_id, 'Todo')->subject($defEmail->subject);
            });
            $defEmail->send_status = true;
            $defEmail->sent_time = DB::raw('CURRENT_TIMESTAMP');
            $defEmail->save();
        }
    }

    public static function initEmailListeners() {
        Event::listen('user.new-added', function($user = null) {
            //$user = User::findOrFail(1);
            if ($user == null) {
                $user = GAuth::user();
            }

//Email me who was added.
            /*            Mail::send('emails.welcome', ['user' => $user], function($message) use($user) {
              $message->to($user->email, 'Todo')->subject('Welcome to Todo!');
              }); */
            $from = "System";
            $toEmailId = $user->email;
            $type = 'user.new-added';
            $subject = 'Welcome to Todo!';
            $view = View::make('emails.welcome', ['user' => $user]);
            $body = $view->render();
            $timeToSend = null;
            Email::deferSend($from, $toEmailId, $type, $subject, $body, $timeToSend);
        });
        Event::listen('user.logged-in', function($user) {
            GAuth::user($user->toArray());
        });
        Event::listen('user.token-expired', function($userId) {
            
        });
        Event::listen('task.new-added', function($taskId) {
            
        });
        Event::listen('task.deleted', function($taskId) {
            //return;
//email if required
            Log::debug('task.delete Event');
            Log::debug('Trying to send mail');
            $task = Task::findOrFail($taskId);
            //$message = "The task: '" . self::taskMarkup($task->id) . "' was removed by " . self::userMarkup(GAuth::user()['id']);
            $taskMembers = $task->members();
            $taskMembers = User::whereIn('id', $taskMembers)->get();
            //from person
            $fromUser = GAuth::user();
            //TODO replace hardcoded id with auth me
            //$content = $fromUser['displayName'] . " deleted the task '" . $task->title . "'.";
            $content = "The task: '" . $task->title . "' was removed by " . GAuth::user()['displayName'];
            foreach ($taskMembers as $mem) {
                if (intval($mem->id) != intval(GAuth::user()['id'])) {
                    /*        Mail::send('emails.common', ['task' => $task, 'user' => $mem, 'content' => $content], function($message) use($mem) {
                      $message->to($mem->email, 'Todo')->subject('Task Removed');
                      }); */
                    $view = View::make('emails.common', ['task' => $task, 'user' => $mem, 'content' => $content]);
                    $body = $view->render();
                    Email::deferSend('sys', $mem->email, 'task.deleted', 'Task Removed', $body, null);
                }
            }
        }, 3);
        Event::listen('task.status-changed', function($payload) {
//Notify every task member
//Log::info(print_r($payload,true));
            $taskId = $payload['taskId'];
            $oldStatus = $payload['oldStatus'];
            $newStatus = $payload['newStatus'];
            $message = '';
            $task = Task::findOrFail($taskId);
            if ($newStatus == '1') {
                $message = "The task: '" . self::taskMarkup($task->id) . "' was marked done by " . self::userMarkup(GAuth::user()['id']);
            } else if ($newStatus == '0') {
                $message = "The task: '" . self::taskMarkup($task->id) . "' was marked not done by " . self::userMarkup(GAuth::user()['id']);
            }
            $taskMembers = $task->members();
            foreach ($taskMembers as $memId) {
                if (intval($memId) != intval(GAuth::user()['id'])) {
                    Notification::notify($memId, $message, 'task.status-changed', 'User ' . GAuth::user()['id']);
                }
            }
        });
        Event::listen('task.change-priority', function($payload) {
//Notify every task member
            $taskId = $payload['taskId'];
            $action = $payload['action'];
            $message = '';
            $task = Task::findOrFail($taskId);
            if ($action == 'inc') {
                $message = "The priority of task: '" . self::taskMarkup($task->id) . "' was increased by " . self::userMarkup(GAuth::user()['id']);
            } else if ($action == 'dec') {
                $message = "The priority of task: '" . self::taskMarkup($task->id) . "' was decreased by " . self::userMarkup(GAuth::user()['id']);
            }
            $taskMembers = $task->members();
            foreach ($taskMembers as $memId) {
                if (intval($memId) != intval(GAuth::user()['id']))
                    Notification::notify($memId, $message, 'task.change-priority', 'User ' . GAuth::user()['id']);
            }
        });
        Event::listen('task.members-added', function($payload) {
//Notify the newly added members                
            $taskId = $payload['taskId'];
            $members = $payload['memberIds'];

            $task = Task::findOrFail($taskId);
            $message = "You were added to the task '" . self::taskMarkup($task->id) . "' by " . self::userMarkup(GAuth::user()['id']);
            foreach ($members as $member) {
                if (intval($member) != intval(GAuth::user()['id']))
                    Notification::notify($member, $message, 'task.members-added', 'User ' . GAuth::user()['id']);
            }
        });
        Event::listen('task.assigned', function($payload) {
//Notify the newly added members                
            $taskId = $payload['taskId'];
            $members = $payload['memberIds'];

            $task = Task::findOrFail($taskId);
            $message = self::userMarkup(GAuth::user()['id']) . " assigned you to the task '" . self::taskMarkup($task->id);
            foreach ($members as $member) {
                if (intval($member) != intval(GAuth::user()['id']))
                    Notification::notify($member, $message, 'task.assigned', 'User ' . GAuth::user()['id']);
            }
        });

        Event::listen('task.members-removed', function($payload) {
//Notify the removed members
            $taskId = $payload['taskId'];
            $members = $payload['memberIds'];

            $task = Task::findOrFail($taskId);
            $message = "You were removed from the task '" . self::taskMarkup($task->id) . "' by " . self::userMarkup(GAuth::user()['id']);
            foreach ($members as $member) {
                if (intval($member) != intval(GAuth::user()['id']))
                    Notification::notify($member, $message, 'task.members-removed', 'User ' . GAuth::user()['id']);
            }
        });
        Event::listen('task.assignee-removed', function($payload) {
//Notify the removed members
            $taskId = $payload['taskId'];
            $members = $payload['assigneeIds'];

            $task = Task::findOrFail($taskId);
            $message = "You were unassigned the task '" . self::taskMarkup($task->id) . "' by " . self::userMarkup(GAuth::user()['id']);
            foreach ($members as $member) {
                if (intval($member) != intval(GAuth::user()['id']))
                    Notification::notify($member, $message, 'task.assignee-removed', 'User ' . GAuth::user()['id']);
            }
        });
        Event::listen('task.new-comment', function($payload) {
//Notify every task member
            $task = $payload['task'];
            $user = $payload['user'];
            $comment = $payload['comment'];
            $message = self::userMarkup(GAuth::user()['id']) . " commented on the task '" . self::taskMarkup($task->id) . "'.";
            $members = $task->members();
            foreach ($members as $member) {
                if (intval($member) != intval(GAuth::user()['id'])) {
                    Notification::notify($member, $message, 'task.new-comment', 'User ' . GAuth::user()['id']);
                }
            }
        });
        Event::listen('notification.seen', function() {
            
        });
    }

    private static function taskMarkup($text) {
        return '<task>' . $text . '</task>';
    }

    private static function userMarkup($userText) {
        return '<user>' . $userText . '</user>';
    }

    private static function commentMarkup($commentText) {
        return '<comment>' . $commentText . '</comment>';
    }

    public function welcome($user = null) {
        Event::fire('user.new-added');
        return View::make('emails.welcome', ['user' => User::findOrFail(1)]);
    }

}
