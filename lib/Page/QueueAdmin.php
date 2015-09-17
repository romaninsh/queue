<?php
namespace romaninsh\queue;

class Page_QueueAdmin extends \Page {
    public $queue_class='romaninsh/queue/Model_Queue';
    public $processor;
    function init(){
        parent::init();


        // Initialize processor
        if(!$this->processor){
            $this->processor=$this->add('romaninsh/queue/Controller_QueueProcessor');
        }

        // Manually process a record. It might be time consuming and your UI
        // will be waiting for a moment....
        $bs=$this->add('ButtonSet');


        $button=$bs->add('Button')->set('Process One');


        /** Use jQuery UI to display a progress-bar */
        $dialog=$this->add('View');
        $progress=$dialog->add('View');
        $dialog->add('Text')->set('Executing action on the server...');

        $progress->js(true)->progressbar(array('value'=>false));
        $dialog->js(true)->dialog(array('autoOpen'=>false,'modal'=>true,
        'title'=>'Processing single record'));

        $button->js('click',array(
            $dialog->js()->dialog('open'),
            /*
            $progress->js()->find('.ui-progressbar-value')->animate(
                array('width'=>'100%'),
                5000
            )
             */
        ));

        $u=$this->api->url();

        if($button->isClicked()){
            $this->processor->process(1);
            /*
            $this->add('romaninsh/queue/Model_SampleModel')
                ->tryLoadAny()->process();
             */

            $this->js(null, $dialog->js()->dialog('close'))
                ->univ()->successMessage('Successfully processed')
                ->execute();
        }


        if($bs->add('Button')->set('Cl: finished')->isClicked()){
            $this->processor->queue->addCondition('status','finished')->deleteAll();
            $this->js()->univ()->location($u)->execute();
        }
        if($bs->add('Button')->set('Cl: failed')->isClicked()){
            $this->processor->queue->addCondition('status','failed')->deleteAll();
            $this->js()->univ()->location($u)->execute();
        }


        if($bs->add('Button')->set('Empty Queue')->isClicked()){
            $this->processor->queue->deleteAll();
            $this->js()->univ()->page($this->api->url())->execute();
        }

        $bs->add('Button')->set('View Sample Model')
            ->add('VirtualPage')->set(function($p){
                $p->add('Grid')->setModel('romaninsh/queue/Model_SampleModel',array('id','name'));
            })->bindEvent('click');


        if($bs->add('Button')->set('Queue Records')->isClicked()){
            $m=$this->add('romaninsh/queue/Model_SampleModel');
            $this->processor->schedule($m);



            /*
            $q=$this->add($this->queue_class);
            for($x=0;$x<10;$x++){
                $q->set(array(
                    'model_id'=>1,
                    'name'=>'Job #'.rand(100,999),
                ));
                $q->saveAndUnload();
            }
             */

            $this->js(null,
                $this->js()->univ()->page($this->api->url())
            )->univ()->successMessage('Successfully processed')
                ->execute();
        }

        if($bs->add('Button')->set('Refresh')->isClicked()){
            $this->js()->univ()->page($this->api->url())->execute();
        }



        $this->add('H2')->set('Active Processors');
        $q=$this->add($this->queue_class);
        $q->addCondition('status',array('batch','processing','completed'));

        $q->setOrder('ts desc');
        $q->setLimit(100);


        $processors=array();
        foreach($q as $rec){
            if(!$q['processor_id'])continue;
            if(count($processors[$q['processor_id']])>10)continue;  // show last 10
            $processors[$q['processor_id']][]=$rec;
        }


        if(count($processors)){
            $col=floor(12/count($processors));
            if($col<2)$col=2;
            if($col>5)$col=6;

            $cols=$this->add('Columns');

            foreach($processors as $proc){
                $cols->addColumn($col)
                    ->add('Text')->set($proc[0]['processor_id'])->owner
                    ->add('romaninsh/queue/View_ProcessList')
                    ->setSource($proc);

            }
        }


        $cc=$this->add('Columns');

        $c=$cc->addColumn(6);


        $c->add('H3')->set('Pending Jobs');
        $g=$c->add('Grid');
        $g->setModel($this->add($this->queue_class)
            ->addCondition('status','scheduled'),array('name','status','ts'))
            ->setOrder('ts')
            ->setLimit(100);
        $g->add('VirtualPage')
            ->addColumn('details')
            ->set(function($p)use($g){
                $p->add('View_ModelDetails')
                    ->setModel($this->queue_class)
                    ->load($p->id);
            });

        $c->add('H3')->set('Stale Jobs');
        $c->add('Grid')
            ->setModel($this->add($this->queue_class)
            ->addCondition('status','processing'),array('name','status','ts'))
            ->setOrder('ts',true)
            ->setLimit(100);

        $c=$cc->addColumn(6);

        $c->add('H3')->set('Recently Completed');
        $g=$c->add('Grid');
        $g->setModel($this->add($this->queue_class)
            ->addCondition('status','finished'),array('name','status','ts'))
            ->setOrder('ts',true)
            ->setLimit(100);
        $g->add('VirtualPage')
            ->addColumn('details')
            ->set(function($p)use($g){
                $p->add('View_ModelDetails')
                    ->setModel($this->queue_class)
                    ->load($p->id);
            });

        $c->add('H3')->set('Recently Failed');
        $g=$c->add('Grid');
        $g->setModel($this->add($this->queue_class)
            ->addCondition('status','failed'),array('name','status','ts'))
            ->setOrder('ts desc')
            ->setLimit(100);
        $g->addColumn('button','restart');
        $g->add('VirtualPage')
            ->addColumn('details')
            ->set(function($p)use($g){
                $p->add('View_ModelDetails')
                    ->setModel($this->queue_class)
                    ->load($p->id);
            });

        if($_GET['restart']){
            $this->add($this->queue_class)
                ->load($_GET['restart'])
                ->set('status','scheduled')
                ->save();
            $this->js()->univ()->page($this->api->url())->execute();
        }

    }
}
