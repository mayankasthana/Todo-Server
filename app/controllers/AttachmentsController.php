<?php

require_once app_path() . '/../vendor/flowjs/flow-php-server/src/Flow/Autoloader.php';

class AttachmentsController extends Controller {

    public function uploadHandler($taskId, $userId) {
        $fileIn = Input::all();
        //Log::debug($fileIn);
        //Log::debug('Task id ' . $taskId);

        $request = new \Flow\Request();
        $folderPath = storage_path()
                . '/files/flow/uploads/'
                . $taskId . '/'
                . $userId . '/';
        //Log::debug($destination);
        $config = new \Flow\Config(array(
            'tempDir' => storage_path() . '/files/flow/chunks'
        ));
        $file = new \Flow\File($config, $request);
        $response = Response::make('', 200);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!$file->checkChunk()) {
                return Response::make('', 404);
            }
        } else {
            if ($file->validateChunk()) {
                $file->saveChunk();
            } else {
                // error, invalid chunk upload request, retry
                return Response::make('', 400);
            }
        }

        if ($file->validateFile()) {
            /* phew, the file upload is complete.
             * Now save to db, give some random name, save original name. 
             */
            $saveFileName = uniqid();
            $destination = $folderPath . $saveFileName;

            $att = new Attachment;
            $att->origFileName = $fileIn['flowRelativePath'];
            $att->savedFileName = $saveFileName;
            $att->task_id = $taskId;
            $att->user_id = $userId;
            $att->fileSize = $fileIn['flowTotalSize'];
            $att->save();

            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0755, true);
            }
            if ($file->save($destination)) {
                $response = Response::make('fileSaved', 200);
            }
        }
        return $response;
    }

}
