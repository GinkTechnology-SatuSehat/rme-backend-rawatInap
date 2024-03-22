<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\CarePlan;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\CarePlanRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class CarePlanController extends Controller
{
    public $care_plan = [
        'resourceType' => 'CarePlan'
    ];

    public function add_title($data_care_plan)
    {
        if (isset($data_care_plan['title'])) {
            $this->care_plan['title'] = $data_care_plan['title'];
        }
    }

    public function add_status($data_care_plan)
    {
        if (isset($data_care_plan['status_care_plan'])) {
            $this->care_plan['status'] = $data_care_plan['status_care_plan'];
        }
    }

    public function add_category($data_care_plan)
    {
        if (isset($data_care_plan['category'])) {
            for ($i = 0; $i < count($data_care_plan['category']); $i++) {
                $this->care_plan['category'][] = [
                    'coding' => [
                        [
                            "system" => "http://snomed.info/sct",
                            'code' => $data_care_plan['category'][$i]['code'],
                        ]
                    ]
                ];

                if (isset($data_care_plan['category'][$i]['display'])) {
                    $this->care_plan['category'][$i]['coding'][0]['display'] = $data_care_plan['category'][$i]['display'];
                }
            }
        }
    }

    public function add_intent($data_care_plan)
    {
        if (isset($data_care_plan['intent'])) {
            $this->care_plan['intent'] = $data_care_plan['intent'];
        }
    }

    public function add_description($data_care_plan)
    {
        if (isset($data_care_plan['description'])) {
            $this->care_plan['description'] = $data_care_plan['description'];
        }
    }

    public function add_subject($data_care_plan)
    {
        if (isset($data_care_plan['subject'])) {
            $this->care_plan['subject'] = [
                'reference' => 'Patient/' . $data_care_plan['subject']['reference_id_patient']
            ];

            if (isset($data_care_plan['subject']['display'])) {
                $this->care_plan['subject']['display'] = $data_care_plan['subject']['display'];
            }
        }
    }

    public function add_encounter($data_care_plan)
    {
        if (isset($data_care_plan['encounter'])) {
            $this->care_plan['encounter'] = [
                'reference' => 'Encounter/' . $data_care_plan['encounter']['reference_id_encounter']
            ];
        }
    }

    public function add_created($data_care_plan)
    {
        if (isset($data_care_plan['created'])) {
            $this->care_plan['created'] = $data_care_plan['created'];
        }
    }

    public function add_author($data_care_plan)
    {
        if (isset($data_care_plan['author'])) {
            $this->care_plan['author'] = [
                'reference' => 'Practitioner/' . $data_care_plan['author']['reference_id_practitioner']
            ];

            if (isset($data_care_plan['author']['display'])) {
                $this->care_plan['author']['display'] = $data_care_plan['author']['display'];
            }
        }
    }

    public function json($data_care_plan)
    {
        $this->add_title($data_care_plan);
        $this->add_status($data_care_plan);
        $this->add_category($data_care_plan);
        $this->add_intent($data_care_plan);
        $this->add_description($data_care_plan);
        $this->add_subject($data_care_plan);
        $this->add_encounter($data_care_plan);
        $this->add_created($data_care_plan);
        $this->add_author($data_care_plan);

        return json_encode($this->care_plan, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_care_plan(CarePlanRequest $request)
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
        $data = $this->json($data_allergie);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/CarePlan';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                CarePlan::create([
                    'care_plan_id' => $response->id,
                    'care_plan_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create CarePlan Success',
                    'data' => [
                        'care_plan_id' => $response->id,
                        'care_plan_status' => $response->status
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
                'message' => 'Create CarePlan Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
