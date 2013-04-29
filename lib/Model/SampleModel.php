<?php
namespace romaninsh\queue;

class Model_SampleModel extends \Model {

    function init(){
        parent::init();

        $this->addField('name');

        $this->setSource('Array',array(
            array('id'=>1, 'name'=>'Sample Record'),
            array('id'=>2, 'name'=>'Sample Record'),
            array('id'=>3, 'name'=>'Sample Record'),
            array('id'=>4, 'name'=>'Sample Record'),
            array('id'=>5, 'name'=>'Sample Record'),
            array('id'=>6, 'name'=>'Sample Record'),
            array('id'=>7, 'name'=>'Sample Record'),
            array('id'=>8, 'name'=>'Sample Record'),
            array('id'=>9, 'name'=>'Sample Record'),

            array('id'=>10, 'name'=>'Sample Record'),
            array('id'=>11, 'name'=>'Sample Record'),
            array('id'=>12, 'name'=>'Sample Record'),
            array('id'=>13, 'name'=>'Sample Record'),
            array('id'=>14, 'name'=>'Sample Record'),
            array('id'=>15, 'name'=>'Sample Record'),
            array('id'=>16, 'name'=>'Sample Record'),
            array('id'=>17, 'name'=>'Sample Record'),
            array('id'=>18, 'name'=>'Sample Record'),
            array('id'=>19, 'name'=>'Sample Record'),

            array('id'=>20, 'name'=>'Sample Record'),
            array('id'=>21, 'name'=>'Sample Record'),
            array('id'=>22, 'name'=>'Sample Record'),
            array('id'=>23, 'name'=>'Sample Record'),
            array('id'=>24, 'name'=>'Sample Record'),
            array('id'=>25, 'name'=>'Sample Record'),
            array('id'=>26, 'name'=>'Sample Record'),
            array('id'=>27, 'name'=>'Sample Record'),
            array('id'=>28, 'name'=>'Sample Record'),
            array('id'=>29, 'name'=>'Sample Record'),

            array('id'=>30, 'name'=>'Sample Record'),
            array('id'=>31, 'name'=>'Sample Record'),
            array('id'=>32, 'name'=>'Sample Record'),
            array('id'=>33, 'name'=>'Sample Record'),
            array('id'=>34, 'name'=>'Sample Record'),
            array('id'=>35, 'name'=>'Sample Record'),
            array('id'=>36, 'name'=>'Sample Record'),
            array('id'=>37, 'name'=>'Sample Record'),
            array('id'=>38, 'name'=>'Sample Record'),
            array('id'=>39, 'name'=>'Sample Record'),

            array('id'=>40, 'name'=>'Sample Record'),
            array('id'=>41, 'name'=>'Sample Record'),
            array('id'=>42, 'name'=>'Sample Record'),
            array('id'=>43, 'name'=>'Sample Record'),
            array('id'=>44, 'name'=>'Sample Record'),
            array('id'=>45, 'name'=>'Sample Record'),
            array('id'=>46, 'name'=>'Sample Record'),
            array('id'=>47, 'name'=>'Sample Record'),
            array('id'=>48, 'name'=>'Sample Record'),
            array('id'=>49, 'name'=>'Sample Record'),

            array('id'=>50, 'name'=>'Sample Record'),
            array('id'=>51, 'name'=>'Sample Record'),
            array('id'=>52, 'name'=>'Sample Record'),
            array('id'=>53, 'name'=>'Sample Record'),
            array('id'=>54, 'name'=>'Sample Record'),
            array('id'=>55, 'name'=>'Sample Record'),
            array('id'=>56, 'name'=>'Sample Record'),
            array('id'=>57, 'name'=>'Sample Record'),
            array('id'=>58, 'name'=>'Sample Record'),
            array('id'=>59, 'name'=>'Sample Record'),
        ));

    }

    function process(){
        if(rand(1,5)<3)throw $this->exception('Ouch!');
        sleep(5);
        return rand(1,5);
    }
}
