<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

/**
 * @OA\Info(
 *     title="Documentation API MSPR",
 *     version="1.0",
 *     description="API de démonstration pour Swagger et Laravel"
 * )
 */
class ProductsController extends Controller
{
    /**
  * @OA\Get(
 *     path="/api/products",
 *     security={{"bearerAuth":{}}},
 *     summary="Récupère la liste des produits",
 *     tags={"products"},
 *     @OA\Response(
 *         response="200",
 *         description="Liste des produits",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="price", type="number"),
 *                 @OA\Property(property="description", type="string"),
 *                 @OA\Property(property="color", type="string"),
 *                 @OA\Property(property="stock", type="integer"),
 *                 @OA\Property(property="images", type="string"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time"),
 *             ),
 *         ),
 *     ),
 * )
 * 
 *  * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="bearerAuth"
 * )
 */
    public  function index(){
        $products=Product::all();
        return response()->json($products);
    }
}
