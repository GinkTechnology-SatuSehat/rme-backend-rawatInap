<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\SatuSehat\Interoperability\ServiceRequestRequest;

class ServiceRequestController extends Controller
{
    public $service_request = [
        'resourceType' => 'ServiceRequest',
    ];

    public function add_identifier($data_service_request)
    {
        if (isset($data_service_request['identifier'])) {
            for ($i = 0; $i < count($data_service_request['identifier']); $i++) {
                $this->service_request['identifier'][] = [
                    'system' => $data_service_request['identifier'][$i]['system'],
                    'value' => $data_service_request['identifier'][$i]['value'],
                ];

                if (isset($data_service_request['identifier'][$i]['use'])) {
                    $this->service_request['identifier'][$i]['use'] = $data_service_request['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_service_request)
    {
        if (isset($data_service_request['status_service_request'])) {
            $this->service_request['status'] = $data_service_request['status_service_request'];
        }
    }

    public function add_intent($data_service_request)
    {
        if (isset($data_service_request['intent'])) {
            $this->service_request['intent'] = $data_service_request['intent'];
        }
    }

    public function add_priority($data_service_request)
    {
        if (isset($data_service_request['priority'])) {
            $this->service_request['priority'] = $data_service_request['priority'];
        }
    }

    public function add_category($data_service_request)
    {
        if (isset($data_service_request['category'])) {
            for ($i = 0; $i < count($data_service_request['category']); $i++) {
                $this->service_request['category'][] = [
                    'coding' => [
                        [
                            "system" => "http://snomed.info/sct",
                            'code' => $data_service_request['category'][$i]['code'],
                        ]
                    ]
                ];

                if (isset($data_service_request['category'][$i]['display'])) {
                    $this->service_request['category'][$i]['coding'][0]['display'] = $data_service_request['category'][$i]['display'];
                }
            }
        }
    }

    public function add_code($data_service_request)
    {
        if (isset($data_service_request['code'])) {
            $this->service_request['code'] = [
                'coding' => [
                    [
                        'system' => $data_service_request['code']['system'],
                        'code' => $data_service_request['code']['code'],
                    ]
                ]
            ];

            if (isset($data_service_request['code']['display'])) {
                $this->service_request['code']['coding'][0]['display'] = $data_service_request['code']['display'];
            }
        }
    }

    public function add_subject($data_service_request)
    {
        if (isset($data_service_request['subject'])) {
            $this->service_request['subject'] = [
                'reference' => 'Patient/' . $data_service_request['subject']['reference_id_patient']
            ];

            if (isset($data_service_request['subject']['display'])) {
                $this->service_request['subject']['display'] = $data_service_request['subject']['display'];
            }
        }
    }

    public function add_encounter($data_service_request)
    {
        if (isset($data_service_request['encounter'])) {
            $this->service_request['encounter'] = [
                'reference' => 'Encounter/' . $data_service_request['encounter']['reference_id_encounter']
            ];

            if (isset($data_service_request['encounter']['display'])) {
                $this->service_request['encounter']['display'] = $data_service_request['encounter']['display'];
            }
        }
    }

    public function add_occurrenceDateTime($data_service_request)
    {
        if (isset($data_service_request['occurrenceDateTime'])) {
            $this->service_request['occurrenceDateTime'] = $data_service_request['occurrenceDateTime'];
        }
    }

    public function add_authoredOn($data_service_request)
    {
        if (isset($data_service_request['authoredOn'])) {
            $this->service_request['authoredOn'] = $data_service_request['authoredOn'];
        }
    }

    public function add_requester($data_service_request)
    {
        if (isset($data_service_request['requester'])) {
            $this->service_request['requester'] = [
                'reference' => 'Practitioner/' . $data_service_request['requester']['reference_id_practitioner']
            ];

            if (isset($data_service_request['requester']['display'])) {
                $this->service_request['requester']['display'] = $data_service_request['requester']['display'];
            }
        }
    }

    public function add_performer($data_observation)
    {
        if (isset($data_observation['performer'])) {
            for ($i = 0; $i < count($data_observation['performer']); $i++) {
                $this->service_request['performer'][] = [
                    'reference' => 'Practitioner/' . $data_observation['performer'][$i]['reference_id_practitioner']
                ];

                if (isset($data_observation['performer'][$i]['display'])) {
                    $this->service_request['performer'][$i]['display'] = $data_observation['performer'][$i]['display'];
                }
            }
        }
    }

    public function add_reasonCode($data_service_request)
    {
        if (isset($data_service_request['reasonCode'])) {
            for ($i = 0; $i < count($data_service_request['reasonCode']); $i++) {
                $this->service_request['reasonCode'][] = [
                    'text' => $data_service_request['reasonCode'][$i]['text']
                ];
            }
        }
    }

    public function add_reasonReference($data_service_request)
    {
        if (isset($data_service_request['reasonReference'])) {
            for ($i = 0; $i < count($data_service_request['reasonReference']); $i++) {
                $this->service_request['reasonReference'][] = [
                    'Reference' => 'Condition/' . $data_service_request['reasonReference'][$i]['Reference_id_condition']
                ];
            }
        }
    }

    public function add_supportingInfo($data_service_request)
    {
        if (isset($data_service_request['supportingInfo'])) {
            for ($i = 0; $i < count($data_service_request['supportingInfo']); $i++) {
                $this->service_request['supportingInfo'][] = [
                    'reference' => 'Procedure/' . $data_service_request['supportingInfo'][$i]['reference_id_procedure']
                ];
            }
        }
    }

    public function json($data_service_request)
    {
        $this->add_identifier($data_service_request);
        $this->add_status($data_service_request);
        $this->add_intent($data_service_request);
        $this->add_priority($data_service_request);
        $this->add_category($data_service_request);
        $this->add_code($data_service_request);
        $this->add_subject($data_service_request);
        $this->add_encounter($data_service_request);
        $this->add_occurrenceDateTime($data_service_request);
        $this->add_authoredOn($data_service_request);
        $this->add_requester($data_service_request);
        $this->add_performer($data_service_request);
        $this->add_reasonCode($data_service_request);
        $this->add_reasonReference($data_service_request);
        $this->add_supportingInfo($data_service_request);

        return json_encode($this->service_request, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_service_request(ServiceRequestRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_service_request = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_service_request));
        $data = $this->json($data_service_request);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/ServiceRequest';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                ServiceRequest::create([
                    'service_request_id' => $response->id,
                    'service_request_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Service Request Success',
                    'data' => [
                        'service_request_id' => $response->id,
                        'service_request_status' => $response->status
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
                'message' => 'Create Service Request Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
