<?php

namespace Application\Service;

class ReadCsvFileService
{

    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;

    }

    public function  getData(){
        $csvData = [];
        if (($handle = fopen($this->filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $csvData[] = $data;
            }
            fclose($handle);
        }
        return $csvData;
    }

}