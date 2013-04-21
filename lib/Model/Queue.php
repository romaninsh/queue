<?php
namespace romaninsh\queue;

class Model_Queue extensd \Model_Table {
    public $table='queue';

    function init(){
        parent::init();

        // must have ID
        $this->addField('model_id');   // ID of the main model
        $this->addField('name');  // To be displayed in admin. Set to user email or something
        $this->addField('status')->enum(array('draft','scheduled','processing','))->defaultValue('draft');
    }
}
