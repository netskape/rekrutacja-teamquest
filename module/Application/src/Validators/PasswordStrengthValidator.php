<?php

namespace  Application\Validators;

use Laminas\Validator\AbstractValidator;

class PasswordStrengthValidator extends AbstractValidator
{
    const INVALID = 'invalid';

    protected $messageTemplates = [
        self::INVALID => "Hasło nie może być któtsze niż 8 znaków i  musi zwierać 2 małe litery 2 duże litery 2 cyfry i 2 znaki specjalne",
    ];

    public function isValid($value)
    {
        $this->setValue($value);

        if (strlen($value) < 8) {
            $this->error(self::INVALID);
            return false;
        }

        if (preg_match_all('/[a-z]/', $value) < 2) {
            $this->error(self::INVALID);
            return false;
        }

        if (preg_match_all('/[A-Z]/', $value) < 2) {
            $this->error(self::INVALID);
            return false;
        }

        if (preg_match_all('/\d/', $value) < 2) {
            $this->error(self::INVALID);
            return false;
        }

        if (preg_match_all('/[^a-zA-Z\d]/', $value) < 2) {
            $this->error(self::INVALID);
            return false;
        }

        return true;
    }
}
