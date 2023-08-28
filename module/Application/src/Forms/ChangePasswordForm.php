<?php

namespace Application\Forms;

use Application\Validators\PasswordHistoryExistValidator;
use Application\Validators\PasswordStrengthValidator;
use Doctrine\Persistence\ObjectManager;
use Laminas\Form\Element\Hidden;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Identical;
use Laminas\Validator\NotEmpty;


class ChangePasswordForm  extends Form implements InputFilterProviderInterface
{
    protected ObjectManager $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        parent::__construct('change-password',[]);
        $this->setAttribute('action', '/auth/change-password');

        $this->add([
            'name' => 'password',
            'type' => Password::class,

            'attributes' => [
                'autocomplete' => 'new-password',
                'data-original-password' => '1',
                'class' => 'form-control',
                'id' => 'password',
            ],
            'options' => [
                'label' => 'Hasło',
            ]
        ]);
        $this->add([
            'name' => 'passwordRepeat',
            'type' => Password::class,
            'attributes' => [
                'autocomplete' => 'new-password',
                'class' => 'form-control',
                'id' => 'password-repeat',

            ],
            'options' => [
                'label' => 'Powtórzenie hasła',
                'label_attributes' => [
                    'class' => 'form-label'
                ]
            ]
        ]);
        $this->add([
            'name' => 'userId',
            'type' => Hidden::class,
            'attributes' => [
                'class' => 'form-control',
                'id' => 'user-id',

            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => Submit::class,
            'attributes' => [
                'class' => 'btn btn-sm btn-primary',
                'value' => 'Zmień hasło'
            ]
        ]);
        return $this;

    }

    public function getInputFilterSpecification()
    {
        return [
            'password' => [
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
                    [
                        'name' => PasswordStrengthValidator::class,
                        'break_chain_on_failure' => true,
                    ],

                    new PasswordHistoryExistValidator($this->objectManager,$this->get('userId')->getValue())



                ]

            ],
            'passwordRepeat' => [
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
                    [
                        'name' => Identical::class,
                        'break_chain_on_failure' => true,
                        'options' => [
                            'allow_empty' => false,
                            'token' => 'password',
                            'messages' => [
                                Identical::NOT_SAME => 'Hasła nie pasują do siebie'
                            ]
                        ]
                    ]

                ]

            ]
        ];
    }
}
