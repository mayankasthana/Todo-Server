<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Task extends Eloquent {

    protected $table = 'tasks';
    public $timestamps = true;

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
    protected $hidden = array('softDeletes');

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
        return DB::table('tasks')
                        ->leftjoin('task_priority', "tasks.id", '=', 'task_priority.task_id')
                ->whereNull('deleted_at')        
                ->select($columns)
                        ->get();
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
        public function delete(){
            parent::delete();
            Task::deletePriority($this->id);
        }

        static function deletePriority($taskId){
            DB::table('task_priority')->where('task_id', '=', $taskId)->delete();
        }
}
