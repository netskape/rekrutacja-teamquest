<?php

declare(strict_types=1);

namespace Application\Controller;


use Application\Entity\Users;
use Application\Forms\CsvForm;
use Application\Service\ReadCsvFileService;
use Application\Validators\PasswordStrengthValidator;
use Laminas\Form\FormElementManager;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Controller\Plugin\Redirect;
use Laminas\View\Model\ViewModel;
use ZendTwig\View\TwigModel;
use Doctrine\Persistence\ObjectManager;

class IndexController extends AbstractActionController
{
    /**
     * @var FormElementManager
     */
    protected $formElementManager;
    private ObjectManager $objectManager;


    public function __construct(
        FormElementManager $formElementManager,
        ObjectManager $objectManager,
    )
    {
        $this->formElementManager = $formElementManager;
        $this->objectManager = $objectManager;

    }

    public function indexAction()
    {

        $tm = new TwigModel();
        $tm->setTemplate('application/index/index');
        $tm->setTerminal(true);

        /**
         * @var Users $user
         */
        $user = $this->identity();

        /**
         * @var CsvForm $form
         */
        $form = $this->formElementManager->get(CsvForm::class);
        $request = $this->getRequest();
        $importedUsers = [];

        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);

            if(!$post['csvFile']['tmp_name']){
                $form->setMessages(['csvFile' => [
                    'other' => 'Nie wybrano pliku'
                ]]);
            }

            if ($form->isValid() && $post['csvFile']['tmp_name']) {
                $data = $form->getData();
                $csvFileService = new ReadCsvFileService($data['csvFile']['tmp_name']);
                $users = $csvFileService->getData();

                foreach ($users as $u){

                    $importedUsers[$u[0]]['status'] = false;
                    $importedUsers[$u[0]]['message'] = null;

                    $findUser = $this->objectManager->getRepository(Users::class)->findBy(
                        [
                            'email' => $u[0]
                        ]
                    );
                    if($findUser){
                        $importedUsers[$u[0]]['message'] = 'W bazie danych istnieje już uzytkownik o takim adresie email';
                        continue;
                    }else{
                        $password = $u[1];
                        $passwordValidator = new PasswordStrengthValidator();

                        if($passwordValidator->isValid($password)) {
                            $newUser = new Users();
                            $newUser->setEmail($u[0]);
                            $newUser->setPassword(hash('sha512',$password));
                            $this->objectManager->persist($newUser);
                            $this->objectManager->flush();
                            $importedUsers[$u[0]]['status'] = true;
                        }
                        else {
                            $importedUsers[$u[0]]['message'] =  $passwordValidator->getMessages()["invalid"];
                        }
                    }
                }
            }
        }

        $tm->setVariables(
            [
                'user' => $this->identity(),
                'importedUsers' => $importedUsers,
                'csvForm' => $form
            ]
        );
        return $tm;
    }



}
