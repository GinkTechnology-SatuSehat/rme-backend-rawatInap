<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\Allergy;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\SatuSehat\Interoperability\AllergyRequest;

class AllergyController extends Controller
{
    public $allergie = [
        'resourceType' => 'AllergyIntolerance',
    ];

    public function add_identifier($data_allergie)
    {
        if (isset($data_allergie['identifier'])) {
            for ($i = 0; $i < count($data_allergie['identifier']); $i++) {
                $this->allergie['identifier'][] = [
                    'system' => $data_allergie['identifier'][$i]['system'],
                    'value' => $data_allergie['identifier'][$i]['value'],
                ];

                if (isset($data_allergie['identifier'][$i]['display'])) {
                    $this->allergie['identifier'][$i]['display'] = $data_allergie['identifier'][$i]['display'];
                }
            }
        }
    }

    public function add_clinicalStatus($data_allergie)
    {
        if (isset($data_allergie['clinicalStatus'])) {
            $this->allergie['clinicalStatus'] = [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/allergyintolerance-clinical',
                        'code' => $data_allergie['clinicalStatus']['code'],
                    ]
                ]
            ];

            if (isset($data_allergie['clinicalStatus']['display'])) {
                $this->allergie['clinicalStatus']['coding'][0]['display'] = $data_allergie['clinicalStatus']['display'];
            }
        }
    }

    public function add_verificationlStatus($data_allergie)
    {
        if (isset($data_allergie['verificationStatus'])) {
            $this->allergie['verificationStatus'] = [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/allergyintolerance-verification',
                        'code' => $data_allergie['verificationStatus']['code'],
                    ]
                ]
            ];

            if (isset($data_allergie['verificationStatus']['display'])) {
                $this->allergie['verificationStatus']['coding'][0]['display'] = $data_allergie['verificationStatus']['display'];
            }
        }
    }

    public function add_category($data_allergie)
    {
        if (isset($data_allergie['category'])) {
            for ($i = 0; $i < count($data_allergie['category']); $i++) {
                $this->allergie['category'][] = $data_allergie['category'][$i];
            }
        }
    }

    public function add_code($data_allergie)
    {
        if (isset($data_allergie['code'])) {
            $this->allergie['code'] = [
                'coding' => [
                    [
                        'system' => $data_allergie['code']['system'],
                        'code' => $data_allergie['code']['code'],
                    ]
                ]
            ];

            if (isset($data_allergie['code']['display'])) {
                $this->allergie['code']['coding'][0]['display'] = $data_allergie['code']['display'];
            }
        }
    }

    public function add_patient($data_allergie)
    {
        if (isset($data_allergie['patient'])) {
            $this->allergie['patient'] = [
                'reference' => 'Patient/' . $data_allergie['patient']['reference_id_patient']
            ];

            if (isset($data_allergie['patient']['display'])) {
                $this->allergie['patient']['display'] = $data_allergie['patient']['display'];
            }
        }
    }

    public function add_encounter($data_allergie)
    {
        if (isset($data_allergie['encounter'])) {
            $this->allergie['encounter'] = [
                'reference' => 'Encounter/' . $data_allergie['encounter']['reference_id_encounter']
            ];

            if (isset($data_allergie['encounter']['display'])) {
                $this->allergie['encounter']['display'] = $data_allergie['encounter']['display'];
            }
        }
    }

    public function add_recordedDate($data_allergie)
    {
        if (isset($data_allergie['recordedDate'])) {
            $this->allergie['recordedDate'] = $data_allergie['recordedDate'];
        }
    }

    public function add_recorder($data_allergie)
    {
        if (isset($data_allergie['recorder'])) {
            $this->allergie['recorder'] = [
                'reference' => 'Practitioner/' . $data_allergie['recorder']['reference_id_practitioner']
            ];

            if (isset($data_allergie['recorder']['display'])) {
                $this->allergie['recorder']['display'] = $data_allergie['recorder']['display'];
            }
        }
    }

    public function json($data_allergie)
    {
        $this->add_identifier($data_allergie);
        $this->add_clinicalStatus($data_allergie);
        $this->add_verificationlStatus($data_allergie);
        $this->add_category($data_allergie);
        $this->add_code($data_allergie);
        $this->add_patient($data_allergie);
        $this->add_encounter($data_allergie);
        $this->add_recordedDate($data_allergie);
        $this->add_recorder($data_allergie);

        return json_encode($this->allergie, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_allergy(AllergyRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_allergie = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_allergie));
        $data = $this->json($data_allergie);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/AllergyIntolerance';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Allergy::create([
                    'allergy_id' => $response->id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create AllergyIntolerance Success',
                    'data' => [
                        'allergy_id' => $response->id,
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
                'message' => 'Create AllergyIntolerance Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}