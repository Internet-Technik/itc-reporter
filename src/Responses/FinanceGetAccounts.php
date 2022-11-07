<?php
namespace Dlab\ITCReporter\Responses;

use Dlab\ITCReporter\Interfaces\ResponseProcessor;
use Psr\Http\Message\ResponseInterface;

class FinanceGetAccounts implements ResponseProcessor
{
    public function __construct(ResponseInterface $Response)
    {
        $this->Response = $Response;
    }

    public function process()
    {
        try {
            $XML = new \SimpleXMLElement(
                $this->Response->getBody()
            );

            if (empty($XML->Account)) {
                throw new \Exception('No account data');
            }
        } catch (\Exception $e) {
            return [];
        }

        $accounts = [];
        foreach ($XML->Account as $AccountXML) {
            $id = (int) $AccountXML->Number;
            $name = (string) $AccountXML->Name;

            $accounts[$id] = [
                'Name' => $name,
                'Number' => $id
            ];
        }

        return $accounts;
    }
}
