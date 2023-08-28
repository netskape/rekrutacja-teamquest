<?php
namespace Application\Forms;


use Application\Entity\Client;
use Application\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use DoctrineModule\Validator\NoObjectExists;
use DoctrineModule\Validator\ObjectExists;
use Laminas\Filter\StringTrim;
use Laminas\Filter\StripTags;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\NotEmpty;

class LoginForm extends Form implements InputFilterProviderInterface
{


    protected ObjectManager $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        parent::__construct('login', []);
        $this->setAttribute('method', 'POST');
        $this->setAttribute('action','/auth/login');

        $this->add([
            'name' => 'email',
            'type' => Text::class,
            'attributes' => [
                'class' => 'form-control'
            ],
            'options' => [
                'label' => 'Login'
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => Password::class,
            'attributes' => [
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Hasło'
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => Submit::class,
            'attributes' => [
                'class' => 'btn btn-sm btn-primary',
                'value' => 'Zaloguj'
            ]
        ]);
        return $this;
    }
    public function getInputFilterSpecification()
    {

        return [
            'email' => [
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class]
                ],
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'break_chain_on_failure' => true,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Pole nie może być puste'
                            ]
                        ],
                    ],
                ]

            ],
            'password' => [
                'filters' => [
                    ['name' => StripTags::class],
                    ['name' => StringTrim::class]
                ],
                'validators' => [
                    [
                        'name' => NotEmpty::class,
                        'break_chain_on_failure' => true,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Pole nie może być puste'
                            ]
                        ],
                    ],
                ]
            ]
        ];
    }

}