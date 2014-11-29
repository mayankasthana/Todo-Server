<?php

class Attachment extends Eloquent {

    protected $table = 'attachments';
    public $timestamps = true;
    protected $hidden = array('updated_at');

    public static function getAttachmentsByTask($task) {
        $attachments = Attachment::where('task_id', $task->id)
                ->select(
                        array(
                            'id',
                            'upload_time',
                            DB::raw('origFileName as fileName'),
                            DB::raw('task_id as taskId'),
                            DB::raw('user_id as uploader'),
                            'fileSize'
                        )
                )
                ->get()
                ->toArray();

        return $attachments;
    }

    public static function downloadAttachment($attId) {
        $att = Attachment::findOrFail($attId);
        $filePath = storage_path()
                . '/files/flow/uploads/'
                . $att->task_id . '/'
                . $att->user_id . '/'
                . $att->savedFileName;
        return Response::download($filePath, $att->origFileName, [
                    "Content-Description" => "File Transfer",
                    "Content-Disposition" => "attachment; "
                    . 'filename=' . $att->origFileName . ';'
                    . 'size=' . $att->fileSize
        ]);
    }

    public function deleteAttachedFile() {
        File::delete($this->filePath());
        return (!File::exists($this->filePath()));
    }

    public function filePath() {
        $filePath = storage_path()
                . '/files/flow/uploads/'
                . $this->task_id . '/'
                . $this->user_id . '/'
                . $this->savedFileName;
        return $filePath;
    }

}
