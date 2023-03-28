<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;


class OrdersController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/orders/{id}",
 *     summary="Récupère la liste des commandes",
 *     tags={"orders"},
 *     description="Return a specific order by ID",
 *     @OA\Parameter(
 *          name="id",
 *          description="Order ID",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer",
 *              format="int64"
 *          )
 *      ),
 *     @OA\Response(
 *         response="200",
 *         description="Liste des commandes",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="product_id", type="integer"),
 *                 @OA\Property(property="user_id", type="integer"),
 *                 @OA\Property(property="quantity", type="integer"),
 *                 @OA\Property(property="total", type="number"),
 *                 @OA\Property(property="status", type="integer"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time"),
 *                 @OA\Property(
 *                     property="user",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="email", type="string"),
 *                     @OA\Property(property="phoneNumber", type="string"),
 *                     @OA\Property(property="city", type="string"),
 *                 ),
 *                 @OA\Property(
 *                     property="product",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="price", type="number"),
 *                 ),
 *             ),
 *         ),
 *     ),
 * )
 * @OA\Tag(
 *     name="orders",
 *     description="API pour les commandes"
 * )
 */
    public function index($user_id){
    
        $orders= Order::with(['user'=>function($query){
            $query->select('id','name','email','phoneNumber','city');
        },'product'=> function($query){
            $query->select('id', 'name', 'price');
        }])->where('user_id', $user_id)->get();

        return response()->json($orders);
    }
}
