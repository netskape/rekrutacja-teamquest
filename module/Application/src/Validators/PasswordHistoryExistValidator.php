<?php

namespace Application\Validators;

use Application\Entity\PasswordHistory;
use Doctrine\Persistence\ObjectManager;
use Laminas\Validator\AbstractValidator;

class PasswordHistoryExistValidator extends AbstractValidator
{
    const EXIST = 'exist';

    protected $messageTemplates = [
        self::EXIST => "Podane hasło było już użyte w przeszłości",
    ];

    private ObjectManager $objectManager;
    private $userId;


    /**
     * EmailLoginExistValidator constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager  $objectManager,$userId)
    {
        parent::__construct();
        $this->objectManager = $objectManager;
        $this->userId = $userId;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    function isValid($value)
    {
        $result = $this->objectManager->getRepository(PasswordHistory::class)->findBy(
            [
                'user' => $this->userId,
                'password' => hash('sha512',$value)
            ]
        );
        if(!empty($result)){
            $this->error(self::EXIST);
            return false;
        }
        return true;
    }
}