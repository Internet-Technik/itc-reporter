<?php
namespace Dlab\ITCReporter\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ResponseProcessor
{
    public function __construct(ResponseInterface $Response);

    public function process();
}
