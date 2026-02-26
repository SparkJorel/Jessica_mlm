<?php

namespace App\Controller\Back\WebController;

use App\Services\BonusBinary;
use App\Services\GenerationalBonus;
use App\Services\GetBonus;
use App\Services\GetRecapBonusSponsoringAndPersonalPurchase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BonusController
{
    /**
     * @var GetBonus
     */
    private $bonus;
    /**
     * @var BonusBinary
     */
    private $binary;
    /**
     * @var GetRecapBonusSponsoringAndPersonalPurchase
     */
    private $recapBonus;
    /**
     * @var GenerationalBonus
     */
    private $generationalBonus;

    public function __construct(
        GetBonus $bonus,
        BonusBinary $binary,
        GenerationalBonus $generationalBonus,
        GetRecapBonusSponsoringAndPersonalPurchase $recapBonus
    )
    {
        $this->bonus = $bonus;
        $this->binary = $binary;
        $this->recapBonus = $recapBonus;
        $this->generationalBonus = $generationalBonus;
    }

    /**
     * @Route("bonus/sponsorship", name="bonus_parrainage_user")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getBonusSponsorship(Request $request)
    {
        return $this->bonus->getBonusSponsor($request);
    }


    /**
     * @Route("bonus/personal-purchase", name="bonus_achat_personnel")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getBonusPersonalPurchase(Request $request)
    {
        return $this->bonus->getBonusPersonalPurchase($request);
    }

    /**
     * @Route("bonus/binary", name="bonus_binaire")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getBonusBinaire(Request $request)
    {
        return $this->binary->getBonusBinaire($request);
    }

    /**
     * @Route("bonus/generational", name="bonus_generational")
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getBonusGenerational(Request $request)
    {
        return $this->generationalBonus->bonusGenerational($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("bonus/sponsoring/cycle", name="bonus_sponsoring_cycle", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getRecapBonusSponsoring(Request $request)
    {
        return $this->recapBonus->getRecapSponsoring($request);
    }

    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("bonus/personal-purchase/cycle", name="bonus_personal_purchase_cycle", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getBonusPersonalPurchaseCycle(Request $request)
    {
        return $this->recapBonus->getRecapBonusAchatPersonal($request);
    }
    /**
     * @Security("is_granted('ROLE_JTWC_ADMIN')")
     * @Route("bonus/binary/cycle", name="bonus_binaire_cycle", options={"expose"=true})
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getBonusBinaireCycle(Request $request)
    {
        return $this->binary->getRecapBonusBinaire($request);
    }
}
