<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Task extends Eloquent {

    protected $table = 'tasks';
    public $timestamps = true;

    use SoftDeletingTrait;

    protected $dates = ['deleted_at'];
    protected $hidden = array('softDeletes', 'updated_at', 'deleted_at');

    public function addcomment($commentText, $userId) {
        $comment = New Comment;
        $comment->text = $commentText;
        $comment->user_id = $userId;
        $comment->task_id = $this->id;
        $comment->save();
        return $comment;
    }

    public function comments() {
        //    return Comment::where('task_id', $this->id)->get(array('id', 'text', 'task_id', 'user_id', 'created_at'));
        return $this->hasMany('comment');
    }

    public function attachments() {
        return $this->hasMany('attachment');
    }

    public function users() {
        return $this->belongsToMany('User');
    }

    public function creator() {
        return $this->belongsTo('User', 'created_by_user_id');
    }

    public function assignees() {
        return $this->belongsToMany('User', 'task_user_assign', 'task_id', 'user_id');
    }

    public static function priorityText($priorityNbr) {
        $priorityCode = intVal($priorityNbr);
        $priorityText = '';
        switch ($priorityCode) {
            case 0:
                $priorityText = 'Low';
                break;
            case 1:
                $priorityText = 'Normal';
                break;
            case 2:
                $priorityText = 'High';
                break;
            case 3:
                $priorityText = 'Urgent';
                break;
            default :
                $priorityText = 'Unknown';
        }
        return $priorityText;
    }

//  public function priority() {
//      return $this->hasOne('Task_priority', 'task_id');
//  }


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
        //$savedPriority = Task::savePriority($this->id);
        //$this->priority = $savedPriority;
    }

    public static function setStatus($taskId, $status) {
        DB::beginTransaction();
        DB::table('tasks')
                ->where('id', $taskId)
                ->update(array('status' => $status));
        DB::commit();
    }

    public function delete() {
        parent::delete();
        Task::deletePriority($this->id);
    }

    static function deletePriority($taskId) {
        DB::beginTransaction();
        DB::commit();
    }

    public function taskMemberExists($memId) {
        $exists = DB::table('task_user')
                        ->where('task_id', $this->id)
                        ->where('user_id', $memId)
                        ->count() > 0;
        return $exists;
    }

    public function addMembers($memberIds) {
        $data = array();
        for ($i = 0; $i < sizeof($memberIds); $i++) {
            if ($this->taskMemberExists($memberIds[$i])) {
                continue;
            }
            $dataItem = array();
            $dataItem['task_id'] = $this->id;
            $dataItem['user_id'] = $memberIds[$i];
            array_push($data, $dataItem);
        }
        return DB::table('task_user')
                        ->insert($data);
    }

    public function assignMembers($membersIds) {
        $data = array();
        for ($i = 0; $i < sizeof($membersIds); $i++) {
            $dataItem = array();
            $dataItem['task_id'] = $this->id;
            $dataItem['user_id'] = $membersIds[$i];
            array_push($data, $dataItem);
        }
        return DB::table('task_user_assign')
                        ->insert($data);
    }

    public function removeMembers($memberIds) {
        $res = DB::table('task_user')
                ->where('task_id', $this->id)
                ->whereIn('user_id', $memberIds)
                ->delete();
        return $res;
    }

    public function removeAssignees($memberIds) {
        return DB::table('task_user_assign')
                        ->where('task_id', $this->id)
                        ->whereIn('user_id', $memberIds)
                        ->delete();
    }

    public function members() {
        return array_map(function($user) {
            return $user->user_id;
        }, (array) DB::table('task_user')
                        ->where('task_id', $this->id)
                        ->select(array('user_id'))
                        ->get());
    }

}
