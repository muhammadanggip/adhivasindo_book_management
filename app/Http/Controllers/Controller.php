<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Book Management API",
 *     version="1.0.0",
 *     description="A comprehensive API for managing books and book loans",
 *     @OA\Contact(
 *         email="support@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Development server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Schema(
 *     schema="Book",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Laravel: Up & Running"),
 *     @OA\Property(property="author", type="string", example="Matt Stauffer"),
 *     @OA\Property(property="published_year", type="integer", example=2023),
 *     @OA\Property(property="isbn", type="string", example="978-1-492-04068-8"),
 *     @OA\Property(property="stock", type="integer", example=5),
 *     @OA\Property(property="available_stock", type="integer", example=4),
 *     @OA\Property(property="is_available", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="BookLoan",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="book_id", type="integer", example=1),
 *     @OA\Property(property="user_name", type="string", example="John Doe"),
 *     @OA\Property(property="user_email", type="string", example="john@example.com"),
 *     @OA\Property(property="book_title", type="string", example="Laravel: Up & Running"),
 *     @OA\Property(property="book_author", type="string", example="Matt Stauffer"),
 *     @OA\Property(property="loaned_at", type="string", format="date-time"),
 *     @OA\Property(property="returned_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="is_returned", type="boolean", example=false),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
abstract class Controller
{
    //
}
