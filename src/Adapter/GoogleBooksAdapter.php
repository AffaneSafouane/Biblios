<?php

namespace App\Adapter;

use Pagerfanta\Adapter\AdapterInterface;
use Google_Service_Books;

class GoogleBooksAdapter implements AdapterInterface
{
    private Google_Service_Books $service;
    private string $query;
    private array $optParams;

    public function __construct(Google_Service_Books $service, string $query, array $optParams)
    {
        $this->service = $service;
        $this->query = $query;
        $this->optParams = $optParams;
    }

    public function getNbResults(): int
    {
        $this->optParams['maxResults'] = 1;
        $result = $this->service->volumes->listVolumes($this->query, $this->optParams);
        return $result->getTotalItems();
    }

    public function getSlice(int $offset, int $length): iterable
    {
        $this->optParams['startIndex'] = $offset;
        $this->optParams['maxResults'] = $length;
        $result = $this->service->volumes->listVolumes($this->query, $this->optParams);
        return $result->getItems() ?: [];
    }
}