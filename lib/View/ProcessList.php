<?php
namespace romaninsh\queue;
class View_ProcessList extends \Grid {
    function init(){
        parent::init();
        $this->addColumn('text','model_id');
        $this->addColumn('text','status');
    }
}
