<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\Specimen;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\SatuSehat\Interoperability\SpecimenRequest;

class SpecimenController extends Controller
{
    public $specimen = [
        'resourceType' => 'Specimen',
    ];

    public function add_identifier($data_specimen)
    {
        if (isset($data_specimen['identifier'])) {
            for ($i = 0; $i < count($data_specimen['identifier']); $i++) {
                $this->specimen['identifier'][] = [
                    'system' => $data_specimen['identifier'][$i]['system'],
                    'value' => $data_specimen['identifier'][$i]['value'],
                ];

                if (isset($data_specimen['identifier'][$i]['assigner'])) {
                    $this->specimen['identifier'][$i]['assigner']['reference'] = 'Organization/' . $data_specimen['identifier'][$i]['assigner']['reference_id_organization'];
                }
            }
        }
    }

    public function add_status($data_specimen)
    {
        if (isset($data_specimen['status_specimen'])) {
            $this->specimen['status'] = $data_specimen['status_specimen'];
        }
    }

    public function add_type($data_specimen)
    {
        if (isset($data_specimen['type'])) {
            $this->specimen['type'] = [
                'coding' => [
                    [
                        'system' => "http://snomed.info/sct",
                        'code' => $data_specimen['type']['code'],
                    ]
                ]
            ];

            if (isset($data_specimen['type']['display'])) {
                $this->specimen['type']['coding'][0]['display'] = $data_specimen['type']['display'];
            }
        }
    }

    public function add_collection($data_specimen)
    {
        if (isset($data_specimen['collection'])) {
            $this->specimen['collection'] = [
                'method' => [
                    'coding' => [
                        [
                            'system' => "http://snomed.info/sct",
                            'code' => $data_specimen['collection']['method']['code'],
                        ]
                    ]
                ],
            ];

            if (isset($data_specimen['collection']['method']['display'])) {
                $this->specimen['collection']['method']['coding'][0]['display'] = $data_specimen['collection']['method']['display'];
            }

            if (isset($data_specimen['collection']['collectedDateTime'])) {
                $this->specimen['collection']['collectedDateTime'] = $data_specimen['collection']['collectedDateTime'];
            }

            if (isset($data_specimen['collection']['quantity'])) {
                $this->specimen['collection']['quantity'] = [
                    'value' => $data_specimen['collection']['quantity']['value'],
                    'unit' => $data_specimen['collection']['quantity']['unit'],
                ];
            }

            if (isset($data_specimen['collection']['collector'])) {
                $this->specimen['collection']['collector'] = [
                    'reference' => 'Practitioner/' . $data_specimen['collection']['collector']['reference_id_practitioner']
                ];

                if (isset($data_specimen['collection']['collector']['display'])) {
                    $this->specimen['collection']['collector']['display'] = $data_specimen['collection']['collector']['display'];
                }
            }

            if (isset($data_specimen['collection']['fastingStatusCodeableConcept'])) {
                $this->specimen['collection']['fastingStatusCodeableConcept'] = [
                    'coding' => [
                        [
                            'system' => "http://terminology.hl7.org/CodeSystem/v2-0916",
                            'code' => $data_specimen['collection']['fastingStatusCodeableConcept']['code'],
                        ]
                    ]
                ];

                if (isset($data_specimen['collection']['fastingStatusCodeableConcept']['display'])) {
                    $this->specimen['collection']['fastingStatusCodeableConcept']['coding'][0]['display'] = $data_specimen['collection']['fastingStatusCodeableConcept']['display'];
                }
            }
        }
    }

    public function add_subject($data_specimen)
    {
        if (isset($data_specimen['subject'])) {
            $this->specimen['subject'] = [
                'reference' => 'Patient/' . $data_specimen['subject']['reference_id_patient']
            ];

            if (isset($data_specimen['subject']['display'])) {
                $this->specimen['subject']['display'] = $data_specimen['subject']['display'];
            }
        }
    }

    public function add_request($data_specimen)
    {
        if (isset($data_specimen['request'])) {
            for ($i = 0; $i < count($data_specimen['request']); $i++) {
                $this->specimen['request'][] = [
                    'reference' => 'ServiceRequest/' . $data_specimen['request'][$i]['reference_id_service_request']
                ];
            }
        }
    }

    public function add_receivedTime($data_specimen)
    {
        if (isset($data_specimen['receivedTime'])) {
            $this->specimen['receivedTime'] = $data_specimen['receivedTime'];
        }
    }

    public function add_condition($data_specimen)
    {
        if (isset($data_specimen['condition'])) {
            for ($i = 0; $i < count($data_specimen['condition']); $i++) {
                if (isset($data_specimen['condition'][$i]['text'])) {
                    $this->specimen['condition'][$i]['text'] = $data_specimen['condition'][$i]['text'];
                }
            }
        }
    }

    public function add_extension($data_specimen)
    {
        if (isset($data_specimen['extension'])) {
            for ($i = 0; $i < count($data_specimen['extension']); $i++) {
                $this->specimen['extension'][] = [
                    'url' => $data_specimen['extension'][$i]['url'],
                ];

                if (isset($data_specimen['extension'][$i]['valueDateTime'])) {
                    $this->specimen['extension'][$i]['valueDateTime'] = $data_specimen['extension'][$i]['valueDateTime'];
                }

                if (isset($data_specimen['extension'][$i]['valueContactDetail'])) {
                    if (isset($data_specimen['extension'][$i]['valueContactDetail']['name'])) {
                        $this->specimen['extension'][$i]['valueContactDetail']['name'] = $data_specimen['extension'][$i]['valueContactDetail']['name'];
                    }

                    if (isset($data_specimen['extension'][$i]['valueContactDetail']['telecom'])) {
                        for ($j = 0; $j < count($data_specimen['extension'][$i]['valueContactDetail']['telecom']); $j++) {
                            $this->specimen['extension'][$i]['valueContactDetail']['telecom'][] = [
                                'system' => $data_specimen['extension'][$i]['valueContactDetail']['telecom'][$j]['system'],
                                'value' => $data_specimen['extension'][$i]['valueContactDetail']['telecom'][$j]['value'],
                            ];
                        }
                    }
                }

                if (isset($data_specimen['extension'][$i]['valueReference'])) {
                    $this->specimen['extension'][$i]['valueReference'] = [
                        'reference' => 'Practitioner/' . $data_specimen['extension'][$i]['valueReference']['reference_id_practitioner']
                    ];

                    if (isset($data_specimen['extension'][$i]['valueReference']['display'])) {
                        $this->specimen['extension'][$i]['valueReference']['display'] = $data_specimen['extension'][$i]['valueReference']['display'];
                    }
                }
            }
        }
    }

    public function add_processing($data_specimen)
    {
        if (isset($data_specimen['processing'])) {
            for ($i = 0; $i < count($data_specimen['processing']); $i++) {
                if (isset($data_specimen['processing'][$i]['timeDateTime'])) {
                    $this->specimen['processing'][$i]['timeDateTime'] = $data_specimen['processing'][$i]['timeDateTime'];
                }
            }
        }
    }

    public function json($data_specimen)
    {
        $this->add_identifier($data_specimen);
        $this->add_status($data_specimen);
        $this->add_type($data_specimen);
        $this->add_collection($data_specimen);
        $this->add_subject($data_specimen);
        $this->add_request($data_specimen);
        $this->add_receivedTime($data_specimen);
        $this->add_condition($data_specimen);
        $this->add_extension($data_specimen);
        $this->add_processing($data_specimen);

        return json_encode($this->specimen, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_specimen(specimenRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_specimen = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_specimen));
        $data = $this->json($data_specimen);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Specimen';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                specimen::create([
                    'specimen_id' => $response->id,
                    'specimen_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Specimen Success',
                    'data' => [
                        'specimen_id' => $response->id,
                        'specimen_status' => $response->status
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
                'message' => 'Create Specimen Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
