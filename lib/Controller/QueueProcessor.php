<?php
namespace romaninsh\queue;

class Controller_QueueProcessor extends \AbstractController {
    public $default_batch_length=5;        // How many records process in a single batch

    public $queue_class='romaninsh/queue/Model_Queue';
    public $queue;

    public $processor_id=null;

    function init(){
        parent::init();
        if(!$this->processor_id)$this->processor_id='Procesor #'.rand(100,999);

        $this->queue=$this->add($this->queue_class);
    }

    /**
     * All the records matching our model will be added into the queue,
     * then model's process() method will be executed by the scheduler.
     *
     * $processor->schedule($books, 'delete'); // will delete all books
     *
     * Model should have necessary conditions applied.
     */
    function schedule(\Model $model, $method='process'){

        if($model instanceof \Model_Table){
            $model->setActualFields(array('id',$this->model->title_field));
        }
        $class=get_class($model);
        $caption=$model->caption?:$class;


        $q=$this->queue;


        $q->lock();
        try {
            // TODO: this could be more efficient, but I went for simplicity
            foreach($model as $id=>$row){
                $q->set('model_id',$id);
                $q->set('model_class',$class);
                $q->set('model_method',$method);

                $q->set('name',$method.' '.' ('.$caption.') #'.$id);
                $q->set('status','draft');
                $q->saveAndUnload();
            }

            // Schedule. TODO: use some sort of unique identifier to mark only
            // records we have added ourselves
            $q->addCondition('status','draft')
                ->dsql()->set('status','scheduled')
                ->update();

            $q->unlock();   // finally

        }catch(\Exception $e){
            // Remove everything we have scheduled so far and re-throw error
            $q->addCondition('status','scheduled')
                ->dsql()->delete();
            $q->unlock();

            throw $e;       // finally
        }
    }

    /**
     * Only the currently loaded record will be processed by scheduler.
     *
     * $processor->scheduleOne($books, 'sendEmail'); // will delete all books
     *
     * @return Queue model (so that you can track it by ID)
     */
    function scheduleOne(\Model $model, $method='process'){

        if(!$model->loaded())throw $this->exception('Model must be loaded');

        if($model instanceof \Model_Table){
            $model->setActualFields(array('id',$this->model->title_field));
        }
        $class=get_class($model);
        $caption=$model->caption?:$class;


        $q=$this->queue;


        $id=$model->id;
        $q->set('model_id',$id);
        $q->set('model_class',$class);
        $q->set('model_method',$method);

        $q->set('name',$method.' '.' ('.$caption.') #'.$id);
        $q->set('status','scheduled'); // skip draft
        $q->saveAndUnload();

    }


    /**
     * Process will load a batch of records from the queue, will initialize
     * appropriate models and will execute action on those models.
     */
    function process($batch_length=null){

        $m=$this->queue->newInstance();
        $m->addCondition('status','scheduled');
        $m->setLimit($batch_length?:$this->default_batch_length);

        $batch=array();
        $batch_ids=array();

        $m->lock();
        foreach($m as $id=>$rec){
            $batch[]=$rec;
            $batch_ids[]=$id;
        }

        if(!$batch_ids)return;

        if ($m instanceof Model_Table) {
            $m->addCondition('id',$batch_ids)->dsql()
                ->set('processor_id',$this->processor_id)
                ->set('status','batch')
                ->update()
                ;
        }else{
            foreach($batch_ids as $id){
                $m->load($id);
                $m['processor_id']=$this->processor_id;
                $m['status']='batch';
                $m->save();
            }
        }
        $m->unlock();


        $models=array();

        foreach($batch as $rec){

            if(!$this->api instanceof \ApiWeb)
                echo "Processing ".$rec['name'].".. ";

            // cache model objects and re-use them
            if(isset($models[$rec['model_class']])){
                $mm=$models[$rec['model_class']];
            }else{
                $mm=$models[$rec['model_class']]=
                    $this->add($rec['model_class']);
            }

            $this->queue->load($rec[$this->queue->id_field]);
            $this->queue['status']='processing';
            $this->queue->save();

            try{
                $mm->unload();
                $mm->load($rec['model_id']);
                $res=$mm->{$rec['model_method']}();

                // Update queue
                $this->queue['outcome']=json_encode($res);
                $this->queue['status']='completed';
                echo "OK\n";
            }catch(\Exception $e){
                $this->queue['error']=$e->getMessage();
                $this->queue['status']='failed';
                echo "FAIL\n";
            }

            $this->queue->save();

        }

        $m=$this->queue->newInstance();

        if ($m instanceof Model_Table) {

            $m
                ->addCondition('status','completed')
                ->addCondition('processor_id',$this->processor_id)->dsql()
                ->set('status','finished')
                ->update()
                ;
        }else{

            $m
                ->addCondition('status','completed')
                ->addCondition('processor_id',$this->processor_id);

            foreach($m as $rec){
                $m['status']='finished';
                $m->save();
            }
        }
    }
}
