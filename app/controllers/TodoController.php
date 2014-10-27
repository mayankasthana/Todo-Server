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
        Event::listen('user.new-added', function($userId) {
            //Email me who was added.
            $message = "Hi! Welcome to todo.";
        });
        Event::listen('user.logged-in', function($userId) {
            
        });
        Event::listen('user.token-expired', function($userId) {
            
        });
        Event::listen('task.new-added', function($taskId) {
            
        });
        Event::listen('task.deleted', function($taskId) {
            //Notify every task member
            //Also email if required
            $task = Task::findOrFail($taskId);
            $message = "The task: '" . $task->text . "' was removed";
            $taskMembers = $task->members();
            foreach ($taskMembers as $memId) {
                Notification::notify($memId, $message, 'task.deleted', 'User ' . $task->created_by_user_id);
            }
        });
        Event::listen('task.status-changed', function($taskId) {
            //Notify every task member
        });
        Event::listen('task.change-priority', function($taskId) {
            //Notify every task member
        });
        Event::listen('task.member-added', function($taskId, $userId) {
            //Notify every task member
            //Notify the newly added member
        });
        Event::listen('task.member-removed', function($taskid, $userId) {
            //Notify every task member
            //Notify the removed member
        });
        Event::listen('task.commented', function($taskId, $commentId) {
            //Notify every task member
        });
        Event::listen('notification.seen', function() {
            
        });
    }

}
