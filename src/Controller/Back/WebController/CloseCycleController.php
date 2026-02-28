<?php

namespace App\Controller\Back\WebController;

use App\Entity\Cycle;
use App\Entity\FiltreCycle;
use App\Form\FiltreCycleType;
use App\Services\CloseCycle;
use App\Repository\CycleRepository;
use App\Services\BonusSummaryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CloseCycleController extends AbstractController
{
    /**
     * @var CloseCycle
     */
    private $closeCycle;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(
        CloseCycle $closeCycle,
        Environment $twig,
        ParameterBagInterface $params,
        EntityManagerInterface $manager
    )
    {
        $this->closeCycle = $closeCycle;
        $this->twig = $twig;
        $this->manager = $manager;
        $this->params = $params;
    }

    #[Route('cycle/list', name: 'close_cycle_list')]
    public function index(CycleRepository $cycleRepository)
    {
        $cycles = $cycleRepository->getAllCycle();
        return new Response(
            $this->twig->render('back/webcontroller/close_cycle/index.html.twig', [
                'cycles' => $cycles
            ])
        );
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('close/cycle/{id}', name: 'close_cycle', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function closeCycle(Cycle $cycle, RouterInterface $router)
    {
        $this->closeCycle->closeCycle($cycle);

        $cycle->setClosed(true);
        $cycle->setBinarySaved(true);
	  
	  	$cycle->setActive(false);
	  
        $this->manager->flush();

        return new RedirectResponse($router->generate('view_recap'));
    }

    #[IsGranted('ROLE_JTWC_ADMIN')]
    #[Route('view/recap', name: 'view_recap', methods: ['GET', 'POST'])]
    public function viewRecap(Request $request)
    {
        $cycle = null;
        $filtreCycle = new FiltreCycle();
        $form = $this->createForm(FiltreCycleType::class, $filtreCycle);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {
            /** @var CycleRepository $repositoryCycle */
            $repositoryCycle = $this->manager->getRepository(Cycle::class);
            $cycle = $repositoryCycle->getLastCycle();
        }

        $reports = $this->closeCycle->viewReport($cycle);

        $actif = $reports['actif'];
        $passif = $reports['passif'];
        $total = $reports['total'];

        array_pop($reports);
        array_pop($reports);
        array_pop($reports);

        return $this->render('back/webcontroller/bonus/report.html.twig', array(
            'reports' => $reports,
            'form' => $form->createView(),
            'total' => $total,
            'passif' => $passif,
            'actif' => $actif,
        ));
    }

    #[Route('view/own/recap', name: 'view_own_recap', methods: ['GET', 'POST'])]
    public function viewOwnRecap(Request $request, BonusSummaryInterface $viewReport)
    {
        $cycle = null;
        $filtreCycle = new FiltreCycle();
        $form = $this->createForm(FiltreCycleType::class, $filtreCycle);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /**
             * @var Cycle $cycle
             */
            $cycle = $form->get('period')->getData();
        }

        if (!$cycle) {
            /** @var CycleRepository $repositoryCycle */
            $repositoryCycle = $this->manager->getRepository(Cycle::class);
            $cycle = $repositoryCycle->getLastCycle();
        }

        $report = $viewReport->processAllBonuses($cycle);

//        dump($report);

        return $this->render('back/webcontroller/bonus/view_own_report.html.twig', array(
            'report' => $report,
            'form' => $form->createView(),
        ));
    }    
}
