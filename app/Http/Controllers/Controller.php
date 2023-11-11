<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
 * @OA\Info(
 *     title="Documentación para la API de tareas (INGCO)s",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="meifer.elitepvpers@gmail.com"
 *    )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
