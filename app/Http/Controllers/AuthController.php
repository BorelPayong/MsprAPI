<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
class AuthController extends Controller

{
    /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register a new user",
 *     tags={"Register"},
 *     @OA\RequestBody(
 *         description="User object that needs to be added",
 *         required=true,
 *         @OA\JsonContent(
 *              required={"name","firstname","username","email","password","phoneNumber","postalCode","city"},
 *              @OA\Property(property="name", type="string"),
 *              @OA\Property(property="firstname", type="string", example="KANA JATSA"),
 *              @OA\Property(property="username", type="string", example="rkdev"),
 *              @OA\Property(property="email", type="string", example="ronaldokana12@gmail.com"),
 *              @OA\Property(property="password", type="string", format="password"),
 *              @OA\Property(property="phoneNumber", type="string", example="0758571979"),
 *              @OA\Property(property="postalCode", type="string", example="93300"),
 *              @OA\Property(property="city", type="string", example="Aubervilliers"),
 *              @OA\Property(property="company", type="string", example="Kwalys")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created",
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
 *         )
 *     )
 * )
 */
    public function register(Request $request){

        // Validate the incoming data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

         // Create a new user in the database
         $user = User::create([
            'name'=>$request->name,
            'firstname'=>$request->firstname,
            'username'=>$request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'postalCode'=>$request->postalCode,
            'phoneNumber'=>$request->phoneNumber,
            'city'=>$request->city,
            'company'=>$request->company
        ]);

     
          if($user){
             
            // Send confirmation to the user's email address
            Mail::send('emails.sentInfos', [
                'title' => 'Confirmation inscription',
                'body' => "Bienvenue dans notre platefome votre compte vient d'etre créer et vos identifiants sont : \n email: ".$user->email." \n password: ".$request->password,
            ], function ($m) use ($user) {
                $m->from('ronaldokana12@gmail.com', 'coffee App');
                $m->to($user->email, $user->name)->subject('Confirmation inscription');
            });

            return response()->json([
                'message' => 'user created',
            ], 200);
          }else{
            return response()->json([
                'message' => 'all fields are required',
            ], 203);
          }
      
    }


    /**
 * @OA\Post(
 *     path="/api/login",
 *     tags={"Login"},
 *     summary="Se connecter à l'application",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Informations de connexion",
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password")
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="QR code sent to email",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
 *             @OA\Property(property="token_type", type="string", example="Bearer"),
 *             @OA\Property(property="expires_in", type="integer", example=3600)
 *         )
 *     ),
 *     @OA\Response(
 *         response="401",
 *         description="Identifiants invalides",
 *     ),
 *     @OA\Response(
 *         response="422",
 *         description="Erreur de validation des champs",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Les champs email et password sont obligatoires.")
 *         )
 *     )
 * )
 */
    public function login(Request $request)
    {
        // Validate the email and password
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Retrieve the user associated with the email
        $user = User::where('email', $request->email)->first();

        // Check if the email and password match
        if ($user && \Hash::check($request->password, $user->password)) {
            // Generate a unique token or session ID
            $token = $user->createToken('coffeeApp')->plainTextToken;;

            // Create a new QR code image
            $qrCode = QrCode::format('png')->size(200)->generate($token);
            // dd($qrCode);
            // die();
            // Save the token to the user's session
            Session::put('qr_token', $token);

            // Send the QR code to the user's email address
            Mail::send('emails.qrcode', [
                'qrCode' => $qrCode,
                'token' => $token,
            ], function ($m) use ($user) {
                $m->from('ronaldokana12@gmail.com', 'Coffee App');
                $m->to($user->email, $user->name)->subject('Your QR code for login');
            });

            return response()->json([
                'message' => 'QR code sent to email',
                'token'=>$token
            ], 200);
        } else {
            return response()->json([
                'message' => 'Email or password is incorrect',
            ], 401);
        }
    }

      /**
 * @OA\Post(
 *     path="/api/qr-login",
 *     tags={"LoginQRCODE"},
 *     summary="Se connecter à l'application",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Informations de connexion",
 *         @OA\JsonContent(
 *             required={"token"}
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."),
 *             @OA\Property(property="token_type", type="string", example="Bearer"),
 *             @OA\Property(property="expires_in", type="integer", example=3600)
 *         )
 *     ),
 *     @OA\Response(
 *         response="401",
 *         description="Identifiants invalides",
 *     ),
 *     @OA\Response(
 *         response="422",
 *         description="Erreur de validation des champs",
 *     )
 * )
 */
    public function qrLogin(Request $request)
    {
        // Validate the token
        $validatedData = $request->validate([
            'token' => 'required',
        ]);

        // Retrieve the token from the user's session
        $sessionToken = Session::get('qr_token');

        // Check if the token matches the one in the user's session
        if ($request->token === $sessionToken) {
            // Log the user in and perform other necessary actions
            // ...

            // Clear the token from the user's session
            Session::forget('qr_token');

            return response()->json([
                'message' => 'Login successful',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid token',
            ], 401);
        }
    }

    public function testQrCode(){
        $qrCode = QrCode::size(200)->generate('Hello, world!');
        return view('emails.qrcode',['qrCode'=>$qrCode]);
    }
}
