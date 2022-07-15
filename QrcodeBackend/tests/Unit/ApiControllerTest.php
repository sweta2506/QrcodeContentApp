<?php

namespace Tests\Unit;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\ApiController;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Mockery;
use Validator;

class ApiControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testDoLoginSuccess()
    {
        $data = [
            'email' => 'test@test.com',
            'password' => 'random@123'
        ];
        $rules = array(
            'email' => 'required|email|max:255',
            'password' => 'required|min:8'
        );
        $validator = $this->mock(Validator::class);
        $auth = $this->mock(Auth::class);
        $userRepository = $this->mock(UserRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);

        Validator::shouldReceive('make')
            ->andReturn($validator);

        $validator->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $auth->shouldReceive('attempt')
            ->with($data)
            ->once()
            ->andReturn(true);

        // Set response data
        $apiData = ['token' => 'random'];
        $apiStatus = Response::HTTP_OK;
        $apiMessage = 'Login successful';

        $methodResponse = [
            "status"=> $apiStatus,
            "data"=>$apiData,
            "message"=> $apiMessage
        ];

        $responseHelper->shouldReceive('success')
            ->with($apiStatus, $apiMessage, $apiData)
            ->once()
            ->andReturn(json_encode($methodResponse));

        $requestData = new Request($data);
        $controller = $this->getController($userRepository, $responseHelper);
        $response = $controller->doLogin($requestData);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($methodResponse, json_decode($response->getContent(), true));

    }

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\UserRepository $userRepository
     * @param App\Helpers\ResponseHelper $responseHelper
     * @return void
     */
    public function getController(UserRepository $userRepository, ResponseHelper $responseHelper) 
    {
        return new ApiController($userRepository, $responseHelper);
    }

    /**
    * Mock an object
    *
    * @param string name
    *
    * @return Mockery
    */
    private function mock($class)
    {
        return Mockery::mock($class);
    }
}
