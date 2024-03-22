<?php

namespace App\Library\Http\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * File download response.
 */
class FileDownloadResponse extends Response
{
    public function __construct(string $fileName, string $fileExtension, ?string $fileContents = '', array $headers = [])
    {
        parent::__construct($fileContents, 200, $headers);

        $fullFileName = sprintf('%s.%s', $fileName, $fileExtension);
        $fileNameFallback = sprintf('file.%s', $fileExtension);

        $disposition = $this->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fullFileName, $fileNameFallback);
        $this->headers->set('Content-Disposition', $disposition);
    }
}