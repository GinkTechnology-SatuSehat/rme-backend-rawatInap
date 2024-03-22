<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\Condition;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\SatuSehat\Interoperability\ConditionRequest;

class ConditionController extends Controller
{
    public $condition = [
        'resourceType' => 'Condition',
    ];

    public function add_clinicalStatus($data_condition)
    {
        if (isset($data_condition['clinicalStatus'])) {
            $this->condition['clinicalStatus'] = [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/condition-clinical',
                        'code' => $data_condition['clinicalStatus']['code'],
                    ]
                ]
            ];

            if (isset($data_condition['clinicalStatus']['display'])) {
                $this->condition['clinicalStatus']['coding'][0]['display'] = $data_condition['clinicalStatus']['display'];
            }
        }
    }

    public function add_category($data_condition)
    {
        if (isset($data_condition['category'])) {
            $this->condition['category'][] = [
                'coding' => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                        'code' => $data_condition['category']['code'],
                    ]
                ]
            ];

            if (isset($data_condition['category']['display'])) {
                $this->condition['category'][0]['coding'][0]['display'] = $data_condition['category']['display'];
            }
        }
    }

    public function add_code($data_condition)
    {
        if (isset($data_condition['code'])) {
            $this->condition['code'] = [
                'coding' => [
                    [
                        'system' => $data_condition['code']['system'],
                        'code' => $data_condition['code']['code'],
                    ]
                ]
            ];

            if (isset($data_condition['code']['display'])) {
                $this->condition['code']['coding'][0]['display'] = $data_condition['code']['display'];
            }
        }
    }

    public function add_subject($data_condition)
    {
        if (isset($data_condition['subject'])) {
            $this->condition['subject'] = [
                'reference' => 'Patient/' . $data_condition['subject']['reference_id_patient']
            ];

            if (isset($data_condition['subject']['display'])) {
                $this->condition['subject']['display'] = $data_condition['subject']['display'];
            }
        }
    }

    public function add_encounter($data_condition)
    {
        if (isset($data_condition['encounter'])) {
            $this->condition['encounter'] = [
                'reference' => 'Encounter/' . $data_condition['encounter']['reference_id_encounter']
            ];

            if (isset($data_condition['encounter']['display'])) {
                $this->condition['encounter']['display'] = $data_condition['encounter']['display'];
            }
        }
    }

    public function add_onsetDateTime($data_condition)
    {
        if (isset($data_condition['onsetDateTime'])) {
            $this->condition['onsetDateTime'] = $data_condition['onsetDateTime'];
        }
    }

    public function add_recordedDate($data_condition)
    {
        if (isset($data_condition['recordedDate'])) {
            $this->condition['recordedDate'] = $data_condition['recordedDate'];
        }
    }

    public function add_onsetRange($data_condition)
    {
        if (isset($data_condition['onsetRange'])) {
            if (isset($data_condition['onsetRange']['low'])) {
                $this->condition['onsetRange']['low'] = [
                    'system' => 'http://unitsofmeasure.org',
                    'value' => $data_condition['onsetRange']['low']['value'],
                    'code' => $data_condition['onsetRange']['low']['code'],
                ];

                if (isset($data_condition['onsetRange']['low']['unit'])) {
                    $this->condition['onsetRange']['low']['unit'] = $data_condition['onsetRange']['low']['unit'];
                }
            }

            if (isset($data_condition['onsetRange']['high'])) {
                $this->condition['onsetRange']['high'] = [
                    'system' => 'http://unitsofmeasure.org',
                    'value' => $data_condition['onsetRange']['high']['value'],
                    'code' => $data_condition['onsetRange']['high']['code'],
                ];

                if (isset($data_condition['onsetRange']['high']['unit'])) {
                    $this->condition['onsetRange']['high']['unit'] = $data_condition['onsetRange']['high']['unit'];
                }
            }
        }
    }

    public function json($data_condition)
    {
        $this->add_clinicalStatus($data_condition);
        $this->add_category($data_condition);
        $this->add_code($data_condition);
        $this->add_subject($data_condition);
        $this->add_encounter($data_condition);
        $this->add_onsetDateTime($data_condition);
        $this->add_recordedDate($data_condition);
        $this->add_onsetRange($data_condition);

        return json_encode($this->condition, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_condition(ConditionRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_condition = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_condition));
        $data = $this->json($data_condition);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Condition';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Condition::create([
                    'condition_id' => $response->id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Condition Success',
                    'data' => [
                        'condition_id' => $response->id,
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
                'message' => 'Create Condition Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}