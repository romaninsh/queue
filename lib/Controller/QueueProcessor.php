<?php
namespace romaninsh\queue;

class Controller_QueueProcessor extends \AbstractController {
    public $batch_length=20;        // How many records process in a single batch

    public $queue_class='romaninsh/queue/Model_Queue';

    public $model_method='process'; // Change to call a different method


    /**
     * All the records matching our model will be added into the queue
     */
    function schedule(){
        $this->model->setActualFields(array('id',$this->model->title_field));

        $q=$this->add($this->queue_class);

        try {
            foreach($this->model as $row){
                $q->set('model_id',$this->model->id);
                $q->set('name',$this->model[$this->model->title_field]);
                $q->set('status','draft');
                $q->saveAndUnload();
            }

            // Convert all of them into 
            $q->lock();
            $q->addCondition('status','draft')
                ->dsql()->set('status','scheduled')
                ->update();
            $q->unlock();
        }catch(Exception $e){
            // Remove everything we have scheduled so far
            $q->addCondition('status','scheduled')
                ->dsql()->delete();

            throw $e;
        }
    }


    function process(){
        if(!$this->model)throw $this->exception('Use setModel() first');


    }
}
