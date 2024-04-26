<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

/**
 * Define uma controller base comum para todas as controllers.
 */
#[OA\Info(
    version: "1.0.0",
    title:"API de Gestão Bancária",
)]
abstract class Controller
{
    //
}
