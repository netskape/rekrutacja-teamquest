<?php

namespace Application\Forms;

use Laminas\Form\Form;
use Laminas\Form\Element\File;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Callback;
use Laminas\Validator\File\Extension;
use Laminas\Validator\NotEmpty;


class CsvForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->setAttribute('method', 'POST');
        $this->setAttribute('action','/');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add([
            'type' => File::class,
            'name' => 'csvFile',
            'attributes' => [
                'id' => 'csv-file',
                'class' => 'form-control',
                'accept' => '.csv'
            ],
            'options' => [
                'label' => 'Plik CSV',
            ],
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'id' => 'upload-btn',
                'value' => 'Upload',
                'class' => 'btn btn-sm btn-primary',
//                'disabled' => true
            ],
        ]);
        return $this;
    }

}