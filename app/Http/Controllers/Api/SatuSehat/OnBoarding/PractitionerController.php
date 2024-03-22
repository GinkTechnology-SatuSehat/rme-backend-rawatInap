<?php

namespace App\Http\Controllers\Api\SatuSehat\OnBoarding;

use GuzzleHttp\Client;
use App\Models\Practitioner;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\SatuSehat\OnBoarding\PractitionerRequest;

class PractitionerController extends Controller
{
    public function get_practitioner(PractitionerRequest $request, $practitioner_id)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $nik = $request->query('nik');

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];

        //SETUP REQUEST
        if (isset($nik)) {
            $url = $satusehat_base_url . '/Practitioner?identifier=' . 'https://fhir.kemkes.go.id/id/nik|' . $nik;
        } else {
            $url = $satusehat_base_url . '/Practitioner' . '/' . $practitioner_id;
        }
        $request = new Request('GET', $url, $headers, $data = null);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Practitioner::create([
                    'practitioner_id' => $response->id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Get Practitioner Success',
                    'data' => [
                        'practitioner_id' => $response->id,
                        'practitioner_name' => $response->name[0]->text
                    ]
                ], $statusCode);
            } else if (isset($response->entry)) {
                $data_practitioner = [];
                for ($i = 0; $i < count($response->entry); $i++) {
                    $data_practitioner = $response->entry[$i]->resource;
                }
                return response([
                    'success' => true,
                    'message' => 'Get Practitioner Success',
                    'data' => [
                        $data_practitioner
                    ]
                ], $statusCode);
            } else {
                return null;
            }
        } catch (ClientException $e) {
            $res = json_decode($e->getResponse()->getBody()->getContents());
            $issue_information = $res;

            throw new HttpResponseException(response([
                'success' => false,
                'message' => 'Get Practitioner Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
