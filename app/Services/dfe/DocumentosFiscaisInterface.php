<?php

namespace App\Services\dfe;

use App\Models\Emitente;
use Illuminate\Http\Request;

/**
 * Interface DocumentosFiscaisInterface
 */
interface DocumentosFiscaisInterface
{
    public function __construct(Emitente $emitente, string $modelo);

    public function buildNFeXml(Request $request): string;

    public function assignXml(string $xml):string;

    public function sendBatch(string $signedXml):string;

    public function getStatus(string $receipt):string;

    public function addProtocolIntoXml(string $signedXml, string $protocol):string;

    public function cancelNFe(array $data):string;

    public function sendAndAuthorizeNfe(array $param): string;

    public function getErrors();

    public function getChave();

}
