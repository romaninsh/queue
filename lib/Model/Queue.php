<?php
namespace romaninsh\queue;

class Model_Queue extends \Model_Table {
    public $table='queue';

    function init(){
        parent::init();

        // Information about the model
        $this->addField('model_class');     // Class of model we should process
        $this->addField('model_id');        // ID of the main model
        $this->addField('model_method');

        $this->addField('name');  // To be displayed in admin. Set to user email or something
        $this->addField('status')->enum(array('draft','scheduled','batch','processing','completed','finished','failed'))->defaultValue('draft');

        $this->addField('ts')->type('datetime');
        $this->addField('processor_id');       // identifier of processor

        $this->addField('outcome')->enum(array('success','fail'));
        $this->addField('error')->type('text');  // text of exception

        // Update timestamp on every save
        $this->addHook('beforeSave',function($m){ $m['ts']=date('Y:m:d H:i:s');});
    }

    function lock(){
        $this->dsql()->expr('lock table [table] write',array('table'=>$this->table))->execute();
    }
    function unlock(){
        $this->dsql()->expr('unlock tables',array('table'=>$this->table))->execute();
    }


}
