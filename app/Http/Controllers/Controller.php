<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="API Documentation",
 *      description="API Documentation for User API",
 *      @OA\Contact(
 *          email="your-email@example.com"
 *      )
 * ),
 * @OA\Server(
 *      url="http://localhost:8000/",
 *      description="Local Server"
 * ),
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * ),
 * @OA\SecurityRequirement(
 *      security={{"bearerAuth":{}}}
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
