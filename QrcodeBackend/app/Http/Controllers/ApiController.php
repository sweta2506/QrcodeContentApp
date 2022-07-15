<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Validator;
/*!
This controller is responsible for managing api calls from frontend like login, register, generate qr code, logout etc.
 */
class ApiController extends Controller
{
    /**
     * @var App\Repositories\UserRepository
     */
    private $userRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\UserRepository $userRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(UserRepository $userRepository, ResponseHelper $responseHelper) 
    {
        $this->userRepository = $userRepository;
        $this->responseHelper = $responseHelper;
    }
    
    /**
     * Login user
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function doLogin(Request $request): JsonResponse
    {
        $rules = array(
            'email' => 'required|email|max:255',
            'password' => 'required|min:8'
        );
        $validator = Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                400,
                $validator->errors()->first()
            );            
        }
        $userData = array(
            'email' => $request->email ,
            'password' => $request->password
        );
        if (\Auth::attempt($userData))
        {
            // Set response data
            $apiData = ['token' => auth()->user()->createToken('API Token')->plainTextToken];
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.custom_error_message.LOGIN_SUCCESSFULL');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);            
        }
        else
        {
            return $this->responseHelper->error(
                Response::HTTP_UNAUTHORIZED,
                Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
                401,
                trans('messages.custom_error_message.ERROR_UNAUTHORIZED_ACCESS')
            );            
        }
    }
    
    /**
     * Register user
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser(Request $request): JsonResponse
    {
        $userDetails = array();
        $rules = array(
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8|same:cpassword',
            'cpassword' => 'required|min:8'
        );
        $validator = \Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                400,
                $validator->errors()->first()
            );
        }
        $userDetails = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password)
        ];
        
        $user = $this->userRepository->createUser($userDetails);

        if(!empty($user))
        {
            $apiData = [];
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.custom_error_message.REGISTRATION_SUCCESS');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        }
        else
        {
            return $this->responseHelper->error(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR],
                500,
                trans('messages.custom_error_message.ERROR_INTERNAL')
            );            
        }
    }
    
    /**
     * Generate QR code
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateQr(Request $request): JsonResponse
    {
        $rules = array(
            'content' => 'required',
            'size' => 'required|int'
        );
        $validator = \Validator::make($request->all() , $rules);
        if ($validator->fails())
        {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                400,
                $validator->errors()->first()
            );
        }
        $r = $g = $b = 0;
        $r1 = $g1 = $b1 = 255;

        if(!empty($request->color))
        {
            list($r, $g, $b) = sscanf($request->color, "#%02x%02x%02x");
        }
        if(!empty($request->bgcolor))
        {
            list($r1, $g1, $b1) = sscanf($request->bgcolor, "#%02x%02x%02x");
        }
        $time = time();
        // create a folder
        if(!\File::exists(public_path('images')))
        {
            \File::makeDirectory(public_path('images'), $mode = 0777, true, true);
        }
        \QrCode::size($request->size)->color($r, $g, $b)->backgroundColor($r1, $g1, $b1)
        ->generate($request->content, 'images/'.$time.'.svg');

        $imgUrl = asset('images/'.$time.'.svg');

        // Set response data
        $apiData = ['qrcode' => $imgUrl];
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.custom_error_message.QR_CODE_SUCCESS');

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
    
    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();
        
        // Set response data
        $apiData = [];
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.custom_error_message.LOGOUT_SUCCESS');

        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
