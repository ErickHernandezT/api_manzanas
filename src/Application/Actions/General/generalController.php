<?php
namespace App\Application\Actions\General;

use Psr\Http\Message\ResponseInterface as Response;
use App\Application\Actions\ActionPayload;





class generalController
{

    

    function response(int $statusCode, array $payload, Response $response)
    {
        $payload = new ActionPayload($statusCode, $payload);

        $response->getBody()->write(
            json_encode(
                $payload,
                JSON_PRETTY_PRINT
            )
        );

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus($payload->getStatusCode());
    }
}
