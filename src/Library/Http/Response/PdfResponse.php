<?php

namespace App\Library\Http\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * PDF file response.
 */
class PdfResponse extends Response
{
    public function __construct(string $fileName, ?string $content = '', bool $isInline = true, array $headers = [])
    {
        parent::__construct($content, 200, $headers);
        $dispositionType = ResponseHeaderBag::DISPOSITION_INLINE;

        if (!$isInline)
        {
            $dispositionType = ResponseHeaderBag::DISPOSITION_ATTACHMENT;
        }

        $disposition = $this->headers->makeDisposition($dispositionType, sprintf('%s.pdf', $fileName));
        $this->headers->set('Content-Type', 'application/pdf');
        $this->headers->set('Content-Disposition', $disposition);
    }
}