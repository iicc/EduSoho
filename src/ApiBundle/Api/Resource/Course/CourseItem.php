<?php

namespace ApiBundle\Api\Resource\Course;

use ApiBundle\Api\Annotation\ApiConf;
use ApiBundle\Api\ApiRequest;
use ApiBundle\Api\Exception\ResourceNotFoundException;
use ApiBundle\Api\Resource\AbstractResource;
use Biz\Course\Service\CourseService;

class CourseItem extends AbstractResource
{
    /**
     * @ApiConf(isRequiredAuth=false)
     */
    public function search(ApiRequest $apiRequest, $courseId)
    {
        $course = $this->service('Course:CourseService')->getCourse($courseId);

        if (!$course) {
            throw new ResourceNotFoundException('教学计划不存在');
        }

        return $this->convertToLeadingItems($this->getCourseService()->findCourseItems($courseId));
    }

    private function convertToLeadingItems($originItems)
    {
        $newItems = array();
        foreach ($originItems as $originItem) {
            $item = array();
            $seq = 1;
            if ($originItem['itemType'] == 'task') {
                $item['type'] = 'task';
                $item['seq'] = $seq;
                $item['number'] = $originItem['number'];
                $item['title'] = $originItem['title'];
                $item['task'] = $originItem;
                $newItems[] = $item;
                $seq++;
                continue;
            }

            if ($originItem['itemType'] == 'chapter' && $originItem['type'] == 'lesson') {
                foreach ($originItem['tasks'] as $task) {
                    $item['type'] = 'task';
                    $item['seq'] = $seq;
                    $item['number'] = $originItem['number'];
                    $item['title'] = $task['title'];
                    $item['task'] = $task;
                    $newItems[] = $item;
                    $seq++;
                }
                continue;
            }

            $item['type'] = $originItem['type'];
            $item['seq'] = 1;
            $item['number'] = $originItem['number'];
            $item['title'] = $originItem['title'];
            $item['task'] =  null;
            $newItems[] = $item;
        }

        return $newItems;
    }

    /**
     * @return CourseService
     */
    private function getCourseService()
    {
        return $this->service('Course:CourseService');
    }
}