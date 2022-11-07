<?php
namespace Dlab\ITCReporter\Responses;

use Dlab\ITCReporter\Interfaces\ResponseProcessor;
use Psr\Http\Message\ResponseInterface;

class SalesGetReport implements ResponseProcessor
{
    public function __construct(ResponseInterface $Response)
    {
        $this->Response = $Response;
    }

    public function process()
    {
        $contents = $this->Response->getBody()->getContents();
        if (empty($contents)) {
            return [];
        }

        $reportCSV = @gzdecode($contents);
        if (! isset($reportCSV) || ! $reportCSV) {
            return [];
        }

        $rows = explode("\n", $reportCSV);
        $headers = explode("\t", array_shift($rows));

        $reportArray = [];

        foreach ($rows as $values) {
            if (empty($values)) {
                continue;
            }

            $data = explode("\t", $values);

            $reportArray[] = array_combine(
                $headers,
                $data
            );
        }

        return $reportArray;
    }
}
