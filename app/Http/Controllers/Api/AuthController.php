<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Resources\Api\UserResource;
use App\Traits\ResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller
{
    use ResponseTrait;
    public function __construct(){
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
/**
    * @OA\get(
    *     path="/api/auth/users",
    *     summary="get users",
    *      tags={"auth"},

    *     @OA\Response(response="200", description="Users Data"),
    *     @OA\Response(response="404", description="Not Found"),
    *     security={{"bearerAuth":{}}}

    * )
*/
    
    public function getAllUsers(){
        $users = User::get();
        if($users->isEmpty()){
            return $this->returnError("No Users Found");
        }
        return $this->returnData("Users",UserResource::collection($users),"Users Data");
    }
/**
 * /Auth
    * @OA\Post(
     *     path="/api/auth/register",
    *     summary="Register a new user",
        *      tags={"auth"},

    *     @OA\Parameter(
    *         name="name",
    *         in="query",
    *         description="User's name",
    *         required=true,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="email",
    *         in="query",
    *         description="User's email",
    *         required=true,
    *         @OA\Schema(type="string")
    *     ),
    *     @OA\Parameter(
    *         name="password",
    *         in="query",
    *         description="User's password",
    *         required=true,
    *         @OA\Schema(type="string")
    *     ),
  
    *     @OA\Response(response="201", description="User registered successfully"),
    *     @OA\Response(response="422", description="Validation errors")
    
    * )
*/
    public function register(RegisterUserRequest $request){
        $user = User::create([
            'name' => $request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
            'role_id'=>$request->role_id
        ]);
        if(!$user){
            return $this->returnError("Not Found");
        }
        return $this->returnData("User",new UserResource($user),"User Created");

    }


    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login",
     *          tags={"auth"},

     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="404", description="Invalid credentials")
     * )
     */
    public function login(LoginUserRequest $request){
        $inputs = $request->validated();
        $token = JWTAuth::attempt($inputs);
        if(!$token){
            return $this->returnError("You Are Unauthenticated");

        }

        $data['user'] = auth()->user();
        $data['token'] = $token;
        $userResource = new UserResource($data['user']);
        // return $this->respondWithToken($token);
        return $this->returnData("data",["user"=>$userResource,"token"=>$data['token']],"User Data");
    }

  /**
     * @OA\Get(
     *     path="/api/auth/profile",
     *     summary="Get logged-in user details",
     *     tags={"auth"},

     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function profile(){
        $authUser = auth()->user();
        if(!$authUser){
            return $this->returnError("You Are Unauthenticated");
        }
    
        return $this->returnData("User",new UserResource($authUser),"User Data");

    }
      /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout the authenticated user",
     *     tags={"auth"},
     *       @OA\Header(
     *         header="Accept",
     *         description="Accept header",
     *         @OA\Schema(type="string", default="application/json")
     *     ),
     *     @OA\Response(response="200", description="You are logged out"),
     *     @OA\Response(response="401", description="You Are Unauthenticated"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function logout(){   
        if (!auth()->check()) {
            return $this->returnError("You Are Unauthenticated");
        }
        $user = auth()->user();
        auth()->logout();
        return $this->returnData("user", new UserResource($user), "You are logged out");
    }
    public function deleteUser($user_id){
        $user = User::findOrFail($user_id);
        if(!$user){
            return $this->returnError("Not Found");
        }
        $user->delete();
        return $this->returnData("user", new UserResource($user), "user deleted");

    }
}
