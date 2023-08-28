<?php

declare(strict_types=1);

namespace Application\Controller;



use Application\Entity\PasswordHistory;
use Application\Entity\Users;
use Application\Forms\ChangePasswordForm;
use Application\Forms\LoginForm;
use Application\Service\MailService;
use Doctrine\Persistence\ObjectManager;
use Laminas\Authentication\AuthenticationService;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\SessionManager;
use Laminas\View\Model\ViewModel;
use ZendTwig\Renderer\TwigRenderer;
use ZendTwig\View\TwigModel;
use Laminas\Mvc\Controller\Plugin\Redirect;

class AuthController extends AbstractActionController {

    private AuthenticationService $authenticationService;
    private FormElementManager $formElementManager;
    private ObjectManager $objectManager;
    private SessionManager $sessionManager;
    private TwigRenderer $twigRenderer;
    private MailService $mailService;

    public function __construct(
        AuthenticationService $authenticationService,
        FormElementManager $formElementManager,
        ObjectManager $objectManager,
        SessionManager $sessionManager,
        TwigRenderer $twigRenderer,
        MailService $mailService,
    ){
        $this->authenticationService = $authenticationService;
        $this->formElementManager = $formElementManager;
        $this->objectManager = $objectManager;
        $this->sessionManager = $sessionManager;
        $this->twigRenderer = $twigRenderer;
        $this->mailService = $mailService;
    }

    public static function verifyCredential(Users $user, $inputPassword)
    {
         return $user->getPassword() == hash('sha512',$inputPassword);
    }

    public function loginAction()
    {
        $view = new TwigModel();
        $view->setTerminal(true);
        $view->setTemplate('application/auth/login');
        $result = $this->objectManager->getRepository(PasswordHistory::class)->findBy(
            [
                'user' => 1,
            ]
        );

        $form = $this->formElementManager->get(LoginForm::class);
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);

            if ($form->isValid()) {
                $adapter = $this->authenticationService->getAdapter();
                $adapter->setIdentity($data['email']);
                $adapter->setCredential($data['password']);
                $authResult = $this->authenticationService->authenticate();
                $isValid = $authResult->isValid();

                if (!$isValid) {
                    $form->setMessages(['password' => [
                        'other' => 'Nieprawidłowy login lub hasło'
                    ]]);

                    $form->get('email')->setAttribute('class', $form->get('email')->getAttribute('class') . ' is-invalid');
                    $form->get('password')->setAttribute('class', $form->get('password')->getAttribute('class') . ' is-invalid');
                } else {
                    if ($data['rememberMe'] ?? false) {
                        $this->sessionManager->rememberMe();
                    } else {
                        $ttl = 60 * 60 * 12;
                        $this->sessionManager->rememberMe($ttl);
                    }
                    return $this->redirect()->toRoute('home');
                }
            }

        }
        $view->setVariables([
            'loginForm' => $form
        ]);
        return $view;
    }

    public function logoutAction()
    {

        $this->authenticationService->clearIdentity();
        $this->redirect()->toRoute('auth/login');
    }

    public function changePasswordAction()
    {
        $view = new TwigModel();
        $view->setTerminal(true);
        $view->setTemplate('application/auth/change-password');

        /** @var ChangePasswordForm $form */
        $form = $this->formElementManager->get(ChangePasswordForm::class);
        $form->get('userId')->setValue($this->identity()->getId());




        $isSendMail = false;
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var Users $user */
                $user = $this->objectManager->getRepository(Users::class)->find($data['userId']);
                $user->setPassword(hash('sha512',$data['password']));
                $user->setChangePasswordRequired(false);
                $this->objectManager->persist($user);
                $this->objectManager->flush();

                $mail = new TwigModel([
                    'email' => $user->getEmail(),
                    'password' => $data['password'],
                ]);
                $mail->setTemplate('application/mail/change-password-mail.twig');
                $content = $this->twigRenderer->render($mail);
                $isSendMail = $this->mailService->send($content,'kzubkowicz@gmail.com');
                if($isSendMail){
                    $isSendMail = true;
                }

            }
        }


        $view->setVariables([
            'form' => $form,
            'isSendMail' => $isSendMail,
            'user' => $this->identity()
        ]);
        return $view;
    }
}