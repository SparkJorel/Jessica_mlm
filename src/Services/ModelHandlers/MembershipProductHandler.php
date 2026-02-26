<?php

namespace App\Services\ModelHandlers;

use App\Entity\Membership;
use App\Entity\MembershipProduct;
use App\Form\MembershipProductType;
use App\Repository\MembershipProductRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class MembershipProductHandler extends ModelSingleEntityAbstract implements ModelInterface, ModelCollectionInterface
{
    public function list()
    {
        $cycleView = $this
            ->twig
            ->render(
                'back/webcontroller/membership_product/list.html.twig',
                [
                    'membership_products' => $this->getMembershiProducts($this->getMembershiProduct()->getMembership()),
                    'membership' => $this->getMembershiProduct()->getMembership()
                ]
            );

        return new Response($cycleView);
    }

    public function show()
    {
    }

    public function save(Request $request, ?bool $mode = false)
    {
        if ($this->entity->isNew()) {
            return $this->submit(
                $request,
                'membership_product_list',
                'back/webcontroller/membership_product/new.html.twig',
                'success',
                'Valeur créée',
                $mode,
                ['code' => $this->getMembershiProduct()->getMembership()->getCode()]
            );
        } else {
            return $this->submit(
                $request,
                'membership_product_list',
                'back/webcontroller/membership_product/new.html.twig',
                'success',
                'Valeur mis à jour',
                $mode,
                ['code' => $this->getMembershiProduct()->getMembership()->getCode()]
            );
        }
    }

    public function remove(Request $request, CsrfTokenManagerInterface $csrf, ?bool $mode = false)
    {
        if ($this->isTokenValid($csrf, $request, '_jtwc_membership_product_token', 'jtwc_membership_product-delete')) {
            return $this->processRemovEntity('membership_product_list', 'info', 'valeur supprimée');
        } else {
            return $this->redirectAfterSubmit('membership_product_list', 'danger', 'A problem occured when processing the request!!');
        }
    }

    public function saveCollection(Request $request): Response
    {
        return new Response("en cours de construction");
    }

    protected function createForm(): FormInterface
    {
        return $this->formFactory->create(MembershipProductType::class, $this->entity);
    }

    /**
     * @return MembershipProduct
     */
    private function getMembershiProduct()
    {
        /** @var MembershipProduct $entity */
        $entity = &$this->entity;
        return $entity;
    }

    protected function getMembershiProducts(Membership $membership)
    {
        $summary = [];

        /** @var MembershipProductRepository $repository */
        $repository = $this->manager->getRepository(get_class($this->entity));

        /** @var MembershipProduct[]|null */
        $membershipProducts = $repository->findBy(['membership' => $membership]);

        if (!$membershipProducts) {
            return null;
        }

        foreach ($membershipProducts as $membershipProduct) {
            $summary[$membershipProduct->getName()][] = ['id' => $membershipProduct->getId(), 'product' => $membershipProduct->getProduct()->getCode(), 'quantity' => $membershipProduct->getQuantity()];
        }

        return $summary;
    }
}
