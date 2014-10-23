<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Task extends Eloquent {

    protected $table = 'tasks';
    public $timestamps = true;

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
    protected $hidden = array('softDeletes');

    public function addcomment($commentText, $userId) {
        $comment = New Comment;
        $comment->text = $commentText;
        $comment->user_id = $userId;
        $comment->task_id = $this->id;
        $comment->save();
        return $comment;
    }

    public function comments() {
        return Comment::where('task_id', $this->id)->get(array('id', 'text', 'task_id', 'user_id'));
    }

    public function users() {
        return $this->belongsToMany('User');
    }

    public function creator() {
        return $this->belongsTo('User', 'created_by_user_id');
    }

    //  public function priority() {
    //      return $this->hasOne('Task_priority', 'task_id');
    //  }

    public static function all($columns = array('*')) {
        //$allTasks = parent::all($columns);
        $allTasks = DB::table('tasks')
                ->leftjoin('task_priority', "tasks.id", '=', 'task_priority.task_id')
                ->whereNull('deleted_at')
                ->select(DB::raw(implode(' , ', $columns)))
                //->select($columns)
                ->get();
        foreach ($allTasks as &$task) {
            $task->priority = intval($task->priority);
            $task->status = strval($task->status);
        }
        return $allTasks;
    }

    public static function lastPriority() {
        return DB::table('task_priority')
                        ->max('priority');
    }

    public static function savePriority($taskId) {
        $lastPriority = Task::lastPriority();
        DB::table('task_priority')->insert(array('priority' => $lastPriority + 1, 'task_id' => $taskId));
        return $lastPriority + 1;
    }

    public function save(array $options = array()) {
        parent::save($options);
        $savedPriority = Task::savePriority($this->id);
        $this->priority = $savedPriority;
    }

    public function delete() {
        parent::delete();
        Task::deletePriority($this->id);
    }

    static function deletePriority($taskId) {
        DB::table('task_priority')->where('task_id', '=', $taskId)->delete();
    }

    static function increasePriority($taskId) {
        //set priorityVal where taskid = -1
        //where priorityVal -1, inc by 1
        //if current priority = 1, do nothing, send error back
        DB::beginTransaction();
        $currPriority = intVal(DB::table('task_priority')
                        ->select('priority')
                        ->where('task_id', $taskId)
                        ->get()[0]->priority);
        if ($currPriority == 1)
            return FALSE;

        DB::table('task_priority')
                ->where('task_id', $taskId)
                ->where('priority', '!=', 1)
                ->update(array('priority' => $currPriority - 1));

        DB::table('task_priority')
                ->where('priority', $currPriority - 1)
                ->where('task_id', '!=', $taskId)
                ->update(array('priority' => $currPriority));
        DB::commit();
        return true;
    }

    static function decreasePriority($taskId) {
        DB::beginTransaction();
        $currPriority = intVal(DB::table('task_priority')
                        ->select('priority')
                        ->where('task_id', $taskId)
                        ->get()[0]->priority);
        if ($currPriority == Task::lastPriority())
            return FALSE;

        DB::table('task_priority')
                ->where('task_id', $taskId)
                ->where('priority', '!=', 1)
                ->update(array('priority' => $currPriority + 1));

        DB::table('task_priority')
                ->where('priority', $currPriority - 1)
                ->where('task_id', '!=', $taskId)
                ->update(array('priority' => $currPriority));
        DB::commit();
        return true;
    }

}
