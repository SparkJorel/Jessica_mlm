<?php

namespace App\Controller\Back\WebController;

use App\Entity\PackPromo;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\ModelHandlers\PackPromoHandler;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PackPromoController
{
    /**
     * @var PackPromoHandler
     */
    private $packPromoHandler;

    public function __construct(PackPromoHandler $packPromoHandler)
    {
        $this->packPromoHandler = $packPromoHandler;
    }

    #[Route('/pack-promos', name: 'pack_promo_list', methods: ['GET'])]
    public function list()
    {
        return
            $this
                ->packPromoHandler
                ->setEntity((new PackPromo()))
                ->list();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/pack-promos/new', name: 'pack_promo_new', methods: ['GET', 'POST'])]
    public function create(Request $request)
    {
        return
            $this
                ->packPromoHandler
                ->setEntity((new PackPromo()))
                ->save($request)
            ;
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/pack-promos/{id}', name: 'pack_promo_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(PackPromo $packPromo)
    {
        return $this->packPromoHandler->setEntity($packPromo)->show();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/pack-promos/{id}/edit', name: 'pack_promo_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, PackPromo $packPromo)
    {
        return
            $this
                ->packPromoHandler
                ->setEntity($packPromo)
                ->save($request);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/pack-promos/{id}/delete', name: 'pack_promo_delete', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function remove(Request $request, CsrfTokenManagerInterface $csrf, PackPromo $packPromo)
    {
        return  $this
            ->packPromoHandler
            ->setEntity($packPromo)
            ->remove($request, $csrf);
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/pack-promos/{id}/end', name: 'pack_promo_ended', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function endPromo(PackPromo $packPromo)
    {
        return $this->packPromoHandler->setEntity($packPromo)->endPromo();
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('/pack-promos/{id}/start', name: 'pack_promo_started', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function startPromo(PackPromo $packPromo)
    {
        return $this->packPromoHandler->setEntity($packPromo)->startPromo();
    }
}
