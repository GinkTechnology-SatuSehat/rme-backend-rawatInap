<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data_login = $request->validated();

        $user = User::where('email', $data_login['email'])->first();
        if (!isset($user)) {
            throw new HttpResponseException(
                response([
                    'errors' => [
                        'message' => [
                            'User not found'
                        ]
                    ]
                ], 404)
            );
        }

        if (!Hash::check($data_login['password'], $user->password)) {
            throw new HttpResponseException(
                response([
                    'errors' => [
                        'message' => [
                            'Wrong Email or Password'
                        ]
                    ]
                ], 401)
            );
        }

        return response([
            'success' => true,
            'message' => 'Login Success',
            'data' => [
                'auth_token' => $user->createToken('auth_token')->plainTextToken
            ]
        ], 200);
    }

    public function generate_satusehat_token()
    {
        //SETUP VARIABLE
        $satusehat_auth_url = env('SATUSEHAT_AUTH_URL');
        $satusehat_client_id = env('SATUSEHAT_CLIENT_ID');
        $satusehat_client_secret = env('SATUSEHAT_CLIENT_SECRET');

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        $data = [
            'form_params' => [
                'client_id' => $satusehat_client_id,
                'client_secret' => $satusehat_client_secret,
            ],
        ];

        //SETUP REQUEST
        $url = $satusehat_auth_url . '/accesstoken?grant_type=client_credentials';
        $request = new Request('POST', $url, $headers);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request, $data)->wait();
            $contents = json_decode($res->getBody()->getContents());

            if (isset($contents->access_token)) {
                SatuSehatToken::create([
                    'token' => $contents->access_token,
                    'token_type' => $contents->token_type,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Generate Satu Sehat Token Success',
                    'data' => [
                        'ss_token' => $contents->access_token,
                        'ss_token_type' => $contents->token_type
                    ]
                ], 200);
            } else {
                return null;
            }
        } catch (ClientException $e) {
            $res = json_decode($e->getResponse()->getBody()->getContents());
            $issue_information = $res;

            throw new HttpResponseException(response([
                'success' => false,
                'message' => 'Generate Satu Sehat Token Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
