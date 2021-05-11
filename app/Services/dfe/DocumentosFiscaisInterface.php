<?php

namespace App\Services\dfe;

use App\Models\Emitente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Interface DocumentosFiscaisInterface
 */
interface DocumentosFiscaisInterface
{
    public function __construct(Emitente $emitente, string $modelo);

    public function buildNFeXml(Request $request);

    public function assignXml(string $xml);

    public function sendBatch(string $signedXml);

    public function getStatus(string $receipt);

    public function addProtocolIntoXml(string $signedXml, string $protocol);

    public function cancelNFe(array $data);

    public function sendAndAuthorizeNfe(Request $request);

    public function getErrors();

    public function getChave();

}
