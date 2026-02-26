<?php

namespace App\Services\ModelHandlers;

use App\Entity\PackPromo;
use App\Entity\UserCommands;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;

abstract class ModelCollectionEntityAbstract extends ModelAbstract
{
    abstract protected function createForm(): FormInterface;

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
     * @throws Exception
     */
    protected function submit(Request $request, string $url_name, string $template, string $type, string $message, ?bool $mode = false): Response
    {
        $originalCollection = $this->getOriginalCollection();
        $form = $this->createForm();
        $form->handleRequest($request);

        if ($this->validate($form)) {
            if (!$this->entity->isNew()) {
                if ($this->entity instanceof UserCommands) {
                    foreach ($originalCollection as $product) {
                        if (false === $this->entity->getProducts()->contains($product)) {
                            $product->setCommand(null);
                            $this->manager->remove($product);
                        }
                    }
                } elseif ($this->entity instanceof PackPromo) {
                    foreach ($originalCollection as $product) {
                        if (false === $this->entity->getProducts()->contains($product)) {
                            $product->setPromo(null);
                            $this->manager->remove($product);
                        }
                    }
                }
            } else {
                if ($this->entity instanceof UserCommands) {
                    $motif =  true === $form->get('inscription')->getData() ? 'inscription' : 'achat';
                    $this->entity->setMotif($motif);
                }
            }

            $this->manager->persist($this->entity);
            $this->manager->flush();
            return $this->redirectAfterSubmit($url_name, $type, $message);
        } else {
            return new Response(
                $this->twig->render($template, [
                    'form' => $form->createView(),
                ])
            );
        }
    }

    private function getOriginalCollection()
    {
        $originalCollection = new ArrayCollection();

        if (!$this->entity->isNew()) {
            /**
             * @var $entity UserCommands|PackPromo
             */
            $entity = $this->entity;

            foreach ($entity->getProducts() as $product) {
                $originalCollection->add($product);
            }
        }

        return $originalCollection;
    }
}
