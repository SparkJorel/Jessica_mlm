<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Entity\PurchaseSummary;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PayInWithDohone implements PayInInterface
{
    /**
     * @var array
     */
    protected $body = [];

    /**
     * @var int
     */
    protected $telephone;

    /** @var PurchaseSummary $summary */
    protected $summary;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /** @var RouterInterface */
    private $router;

    private $urlApiDohonePayIn;
    private $apiKeyDohone;

    public function __construct(
        string $apiKeyDohone,
        string $urlApiDohonePayIn,
        RouterInterface $router,
        HttpClientInterface $httpClient
    )
    {
        $this->router = $router;

        $this->apiKeyDohone = $apiKeyDohone;
        $this->urlApiDohonePayIn = $urlApiDohonePayIn;
        $this->httpClient = $httpClient;
    }

    public function sendCfrmSMSCmd($rcs, int $telephone)
    {
        $this->body = [];

        $this->body['cmd'] = 'cfrmsms';
        $this->body['rCS'] = $rcs;
        $this->body['rT'] = $telephone;

        /** @var ResponseInterface $response */
        $response =  $this
                        ->httpClient
                        ->request('GET', $this->urlApiDohonePayIn, [
                            'query' => $this->body,
                        ]);

        return $response->getContent();
    }

    //public function

    /**
     * @return RedirectResponse|string
     */
    public function payIn(string $rcs = null)
    {
        $this->body = [];

        if (!$rcs) {
            $this->body['cmd'] = 'start';
            $this->body['rDvs'] = 'XAF';
            $this->body['rH'] = $this->apiKeyDohone;
            $this->body['source'] = 'JessicaTWC';
            $this->body['rMt'] = $this->summary->getMontant();
            $this->body['rT'] = $this->summary->getAddressUser()->getTelephone();
            $this->body['rN'] = $this->summary->getAddressUser()->getName();
            $this->body['motif'] = $this->summary->getMotif();
            $this->body['rMo'] = $this->summary->getOperateur();
            $this->body['rOTP'] = $this->summary->getOtpCode();
            $this->body['rI'] = $this->summary->getTransaction();
            
            $this->body['endPage'] = $this->router->generate($this->summary->getSuccess(), [], $this->router::ABSOLUTE_URL);
            $this->body['notifyPage'] = $this->router->generate($this->summary->getNotifyPage(), [], $this->router::ABSOLUTE_URL);
            $this->body['cancelPage'] = $this->router->generate($this->summary->getFail(), [], $this->router::ABSOLUTE_URL);
    
        } else {
            $this->body['cmd'] = 'cfrmsms';
            $this->body['rCS'] = $rcs;
            $this->body['rT'] = $this->getTelephone();
        }

        try {

            /** @var ResponseInterface $response */
            $response =  $this
                            ->httpClient
                            ->request('GET', $this->urlApiDohonePayIn, [
                                'query' => $this->body,
                            ]);

            return $response->getContent();
        } catch (TransportExceptionInterface $ex) {
        } catch (RedirectionExceptionInterface $ex) {
        } catch (ClientExceptionInterface $ex) {
        } catch (ServerExceptionInterface $ex) {
        }

        return "KO start :";
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return 'dohone';
    }

    /**
     * @param PurchaseSummary $purchaseSummary
     * @return PayInInterface
     */
    public function purchaseSummary(PurchaseSummary $purchaseSummary): PayInInterface
    {
        $this->summary = $purchaseSummary;

        return $this;
    }

    /**
     * @param int $telephone
     * @return PayInInterface
     */
    public function setTelephone(int $telephone): PayInInterface
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }
}
