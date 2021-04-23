<?php
namespace Snscripts\ITCReporter\Responses;

use Snscripts\ITCReporter\Interfaces\ResponseProcessor;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\str;

class FinanceGetReport implements ResponseProcessor
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

        $rows = str_getcsv($reportCSV, "\n");

        $headerCount = 0;
        $header = null;
        $dataSource = null;

        $reportArray = [];

        foreach ($rows as $values) {
            if (empty($values)) {
                continue;
            }

            $data = explode("\t", $values);
            $dataCount = count($data);
            
            if (($dataCount - 1) === $headerCount) {
                $removedLastColumn = array_pop($data);
                $dataCount--;
            }

            if ($dataCount < 3) {
                $reportArray["header"][$data[0]] = $data[1];
            }

            if ($dataCount > 3 && $dataSource !== null && $headerCount === $dataCount) {
                $reportArray[$dataSource][] = array_combine(
                    $headers,
                    $data
                );
            }

            if ($dataCount > 3 && $headerCount !== $dataCount) {
                $headers = str_getcsv($values, "\t");
                $headerCount = count($headers);
                $dataSource = $dataSource === null ? "data" : "footer";
            }
        }

        return $reportArray;
    }
}
