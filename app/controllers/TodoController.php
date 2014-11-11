<?php

class TodoController extends Controller {

     public static function initEvents(){
        /*
         * New user event
         * Logged in event
         * Token expired event
         * New task added event
         * Task removed event
         * Task marked done event
         * Priority increase / decrease event
         * New member added to task event
         * Member removed from task event
         * New comment added event
         * Notification viewed event
         * 
         */
        Event::listen('user.new-added', function($user) {
            //Email me who was added.
            $message = "Hi " . self::userMarkup($user->id) . ", Welcome to todo.";
            Notification::notify($user->id, $message, 'user.new-added');
        });
        Event::listen('user.logged-in', function($user) {
            GAuth::user($user->toArray());
        });
        Event::listen('user.token-expired', function($userId) {
            
        });
        Event::listen('task.new-added', function($taskId) {
            
        });
        Event::listen('task.deleted', function($taskId) {
            //Notify every task member
            //Also email if required
            $task = Task::findOrFail($taskId);
            $message = "The task: '" . self::taskMarkup($task->id) . "' was removed by " . self::userMarkup(GAuth::user()['id']);
            $taskMembers = $task->members();
            foreach ($taskMembers as $memId) {
                if (intval($memId) != intval(GAuth::user()['id']))
                    Notification::notify($memId, $message, 'task.deleted', 'User ' . $task->created_by_user_id);
            }
        });
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

}
