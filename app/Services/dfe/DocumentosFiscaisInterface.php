<?php

namespace App\Services\dfe;

use App\Models\Documento;
use App\Models\Emitente;
use App\Models\Evento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Interface DocumentosFiscaisInterface
 */
interface DocumentosFiscaisInterface
{
    public function __construct(Emitente $emitente, string $modelo);

    public function buildNFeXml(Request $request);

    public function assignXml(array $data);

    public function sendBatch(Documento $documento);

    public function getStatus(Evento $evento);

    public function addProtocolIntoXml(Documento $documento, $protocolo);

    public function cancelDocument(Documento $documento);

    public function getErrors();

    public function getChave();

}
