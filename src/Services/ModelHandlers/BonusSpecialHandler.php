<?php

namespace App\Services\ModelHandlers;

use App\Entity\BonusSpecial;
use App\Entity\CollectionBonusSpecial;
use App\Form\BonusSpecialType;
use App\Form\CollectionBonusSpecialType;
use App\Services\FileUploader;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BonusSpecialHandler extends ModelSingleEntityAbstract implements ModelInterface
{
    use TraitHandlers;

    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        EntityManagerInterface $manager,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        Environment $twig,
        FlashBagInterface $session,
        ParameterBagInterface $parameterBag,
        FileUploader $fileUploader
    )
    {
        parent::__construct($manager, $formFactory, $router, $twig, $session);
        $this->fileUploader = $fileUploader;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param Request $request
     * @param bool|null $mode
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function save(Request $request, ?bool $mode = false)
    {
        if ($this->entity->isNew()) {
            return $this->submit(
                $request,
                'bonus_special_list',
                'back/webcontroller/bonus_special/new.html.twig',
                'success',
                'Bonus spécial sauvegardé avec succès'
            );
        } else {
            return $this->submit(
                $request,
                'bonus_special_list',
                'back/webcontroller/bonus_special/edit.html.twig',
                'success',
                'Bonus spécial sauvegardé avec succès'
            );
        }
    }


    /**
     * @param Request $request
     * @param bool|null $mode
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function saveCollection(Request $request, ?bool $mode = false)
    {
        return $this->submitFormCollection(
            $request,
            'bonus_special_list',
            'back/webcontroller/bonus_special/new.html.twig',
            'success',
            'Bonus spécial sauvegardé avec succès'
        );
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_bonus_special_token', 'jtwc_bonus_special-delete')) {
            return $this->processRemovEntity('bonus_special_list', 'info', 'Bonus spécial désactivé avec succès');
        } else {
            return $this->redirectAfterSubmit('bonus_special_list', 'danger', 'A problem occured when processing the request!!');
        }
    }

    /**
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function show()
    {
        return new Response(
            $this->twig->render(
                'back/webcontroller/bonus_special/show.html.twig',
                [
                    'entity' => $this->entity
                ]
            )
        );
    }

    public function list()
    {
        $bonusSpecials = $this->getEntities();
        $bonusSpecialsView = $this
            ->twig
            ->render(
                'back/webcontroller/bonus_special/list.html.twig',
                [
                    'bonusSpecials' => $bonusSpecials
                ]
            );

        return new Response($bonusSpecialsView);
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(BonusSpecialType::class, $this->entity);
    }

    protected function createCollectionForm(): FormInterface
    {
        return $this->formFactory->create(
            CollectionBonusSpecialType::class,
            new CollectionBonusSpecial()
        );
    }

    /**
     * @param FormInterface $formCollection
     * @param string $url_name
     * @param string $type
     * @param string $message
     * @return RedirectResponse
     */
    protected function saveCollectionEntity(FormInterface $formCollection, string $url_name, string $type, string $message)
    {
        /**
         * @var Collection|BonusSpecial[]
         */
        $collectionBonusSpecials = $formCollection
                                                ->get('bonusSpecials')
                                                ->getData();

        if (!$collectionBonusSpecials->isEmpty()) {
            while ($collectionBonusSpecials->current()) {
                $bonusSpecial = $collectionBonusSpecials->current();

                $newFilename = $this->getFileName($bonusSpecial);

                if ($newFilename) {
                    $bonusSpecial->setImageFile($newFilename);
                }
                $newVideoFilename = $this->getFileName($bonusSpecial, 'video');

                if ($newVideoFilename) {
                    $bonusSpecial->setVideoFile($newVideoFilename);
                }

                $this->manager->persist($bonusSpecial);

                $collectionBonusSpecials->next();
            }
            $this->manager->flush();
        }

        return $this->redirectAfterSubmit($url_name, $type, $message);
    }

    /**
     * @param Request $request
     * @param string $url_name
     * @param string $template
     * @param string $type
     * @param string $message
     * @param bool|null $mode
     *
     * @return Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function submitFormCollection(Request $request, string $url_name, string $template, string $type, string $message, ?bool $mode = false): Response
    {
        $form = $this->createCollectionForm();
        $form->handleRequest($request);

        if ($this->validate($form)) {
            return $this->saveCollectionEntity($form, $url_name, $type, $message);
        } else {
            return $this->renderFormView($template, $form);
        }
    }

    /**
     * @return BonusSpecial
     */
    protected function getBonusSpecial()
    {
        /**
         * @var BonusSpecial $entity
         */
        $entity = &$this->entity;

        return $entity;
    }

    protected function saveEntity(string $url_name, string $type, string $message, array $params = null)
    {
        $bonusSpecial = $this->getBonusSpecial();

        $newFilename = $this->getFileName($bonusSpecial);

        if ($newFilename) {
            $bonusSpecial->setImageFile($newFilename);
        }

        $newVideoFilename = $this->getFileName($bonusSpecial, 'video');

        if ($newVideoFilename) {
            $bonusSpecial->setVideoFile($newVideoFilename);
        }

        return parent::saveEntity($url_name, $type, $message); // TODO: Change the autogenerated stub
    }
}
