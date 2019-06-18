<?php
/**
 * User: simon
 * Date: 16.03.2018
 */
namespace ShortPixel\notify;

abstract class ProgressNotifier {
    protected $path;

    public function __construct($path) {
        $this->path = $path;
    }

    public function recordProgress($info, $replace = false) {
        $data = $this->getData();
        $data = $data ? $data : new \stdClass();
        if(isset($info->status)) {
            $data->status = $info->status;
        }
        if(isset($info->total)) {
            $data->total = 0 + $info->total;
        }
        if($replace) {
            $data->succeeded = $data->failed = $data->same = 0;
        }
        if(isset($info->failed)) {
            $data->failed = (isset($data->failed) ? $data->failed : 0) + (is_array($info->failed) ? count($info->failed) : 0 + $info->failed);
        }
        if(isset($info->same)) {
            $data->same = (isset($data->same) ? $data->same : 0) + (is_array($info->same) ? count($info->same) : 0 + $info->same);
        }
        $succeeded = [];
        if(isset($info->succeeded)) {
            $data->succeeded = (isset($data->succeeded) ? $data->succeeded : 0) + (is_array($info->succeeded) ? count($info->succeeded) : 0 + $info->succeeded);
            if(is_array($info->succeeded)) {
                $succeeded = $info->succeeded;
            }
        }
        $data->succeededList = array_slice(array_merge($succeeded, (isset($data->succeededList) ? $data->succeededList : [])), 0, 20);
        if(!count($data->succeededList)) unset($data->succeededList);

        $same = [];
        if(isset($info->same)) {
            $data->same = (isset($data->same) ? $data->same : 0) + (is_array($info->same) ? count($info->same) : 0 + $info->same);
            if(is_array($info->same)) {
                $same = $info->same;
            }
        }
        $data->sameList = array_slice(array_merge($same, (isset($data->sameList) ? $data->sameList : [])), 0, 20);
        if(!count($data->sameList)) unset($data->sameList);

        $failed = [];
        if(isset($info->failed)) {
            $data->failed = (isset($data->failed) ? $data->failed : 0) + (is_array($info->failed) ? count($info->failed) : 0 + $info->failed);
            if(is_array($info->failed)) {
                for($i = 0; $i < count($info->failed); $i++) {
                    $info->failed[$i]->TimeStamp = date("Y-m-d H:i:s");
                }
                $failed = $info->failed;
            }
        }
        $data->failedList = array_slice(array_merge($failed, (isset($data->failedList) ? $data->failedList : [])), 0, 100);
        if(!count($data->failedList)) unset($data->failedList);

        $this->setData($data);
    }

    public abstract function getData();
    public abstract function setData($data);
    public abstract function set($type, $data);
    public abstract function get($type);

    public function enqueueFailedImages(){

    }
    public function getFailedImages(){

    }

    /**
     * Add to a queue info about the last optimized images. The queue is limited to maximum 20 images - the newest
     * @return mixed
     */
    public function enqueueDoneImages() {

    }

    /**
     * @return array - list of the last maximum 20 images optimized.
     */
    public function getDoneImages() {

    }

    public static function constructNotifier($path) {
        $mc = \ShortPixel\getMemcache();
        if($mc) {
            $notifier = new ProgressNotifierMemcache($path);
            $notifier->setMemcache($mc);
            return $notifier;
        }
        return new ProgressNotifierFileQ($path);
    }
}
