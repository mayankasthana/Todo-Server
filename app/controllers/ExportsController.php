<?php

class ExportsController extends Controller {

    public static function test() {
        $user = User::findOrFail(1);
        $tasks = $user->myTasks()
                ->with('creator')
                ->with('users')
                ->with('assignees')
                ->with(array('comments'), function($query) {
                    
                })
                ->with(array('attachments' => function($query) {
                        $query->select(array(
                            'id',
                            DB::raw('origFileName as filename'),
                            'filesize',
                            'upload_time',
                            'task_id'
                        ));
                    }))
                        ->get()
                        ->toArray();
                $xmlArr = array();
                for ($i = 0; $i < sizeOf($tasks); $i++) {
                    $status = $tasks[$i]['status'];
                    $tasks[$i]['status'] = $status == 0 ? 'Not done' : 'Completed';

                    $priority = $tasks[$i]['priority'];
                    $tasks[$i]['priority'] = Task::priorityText($priority);

                    array_push($xmlArr, array('task' => ($tasks[$i])));
                    foreach ($xmlArr[count($xmlArr) - 1]['task']['comments'] as &$comment) {
                        $comment = array('comment' => $comment);
                    }
                    foreach ($xmlArr[count($xmlArr) - 1]['task']['users'] as &$member) {
                        $member = array('member' => $member);
                    }
                    foreach ($xmlArr[count($xmlArr) - 1]['task']['assignees'] as &$assignee) {
                        $assignee = array('assigned' => $assignee);
                    }
                    foreach ($xmlArr[count($xmlArr) - 1]['task']['attachments'] as &$attachment) {
                        $attachment['link'] = URL::to('api/att/'.$attachment['id']);
                        $attachment = array('attachment' => $attachment);
                    }
                }

                $xml = new SimpleXMLElement("<Tasks></Tasks>");
                //array_walk_recursive($xmlArr, array ($xml, 'addChild'));
                ExportsController::array_to_xml($xmlArr, $xml);
                return Response::make($xml->asXML(), '200')->header('Content-Type', 'text/xml');
            }

            static function array_to_xml($array, &$xml) {
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        if (!is_numeric($key)) {
                            $subnode = $xml->addChild("$key");
                            ExportsController::array_to_xml($value, $subnode);
                        } else {
                            ExportsController::array_to_xml($value, $xml);
                        }
                    } else {
                        $xml->addChild("$key", "$value");
                    }
                }
            }

        }
        