<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use App\Models\MedicationRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\MedicationRequestRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicationRequestController extends Controller
{
    public $medication_request = [
        "resourceType" => "MedicationRequest",
    ];

    public function add_identifier($data_medication_request)
    {
        if (isset($data_medication_request['identifier'])) {
            for ($i = 0; $i < count($data_medication_request['identifier']); $i++) {
                $this->medication_request['identifier'][] = [
                    'system' => $data_medication_request['identifier'][$i]['system'],
                    'value' => $data_medication_request['identifier'][$i]['value'],
                ];

                if (isset($data_medication_request['identifier'][$i]['use'])) {
                    $this->medication_request['identifier'][$i]['use'] = $data_medication_request['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_medication_request)
    {
        if (isset($data_medication_request['status_medication_request'])) {
            $this->medication_request['status'] = $data_medication_request['status_medication_request'];
        }
    }

    public function add_intent($data_medication_request)
    {
        if (isset($data_medication_request['intent'])) {
            $this->medication_request['intent'] = $data_medication_request['intent'];
        }
    }

    public function add_category($data_medication_request)
    {
        if (isset($data_medication_request['category'])) {
            for ($i = 0; $i < count($data_medication_request['category']); $i++) {
                $this->medication_request['category'][] = [
                    'coding' => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-category",
                            'code' => $data_medication_request['category'][$i]['code'],
                        ]
                    ]
                ];

                if (isset($data_medication_request['category'][$i]['display'])) {
                    $this->medication_request['category'][$i]['coding'][0]['display'] = $data_medication_request['category'][$i]['display'];
                }
            }
        }
    }

    public function add_priority($data_medication_request)
    {
        if (isset($data_medication_request['priority'])) {
            $this->medication_request['priority'] = $data_medication_request['priority'];
        }
    }

    public function add_medicationReference($data_medication_request)
    {
        if (isset($data_medication_request['medicationReference'])) {
            $this->medication_request['medicationReference'] = [
                'reference' => 'Medication/' . $data_medication_request['medicationReference']['reference_id_medication']
            ];

            if (isset($data_medication_request['medicationReference']['display'])) {
                $this->medication_request['medicationReference']['display'] = $data_medication_request['medicationReference']['display'];
            }
        }
    }

    public function add_subject($data_medication_request)
    {
        if (isset($data_medication_request['subject'])) {
            $this->medication_request['subject'] = [
                'reference' => 'Patient/' . $data_medication_request['subject']['reference_id_patient']
            ];

            if (isset($data_medication_request['subject']['display'])) {
                $this->medication_request['subject']['display'] = $data_medication_request['subject']['display'];
            }
        }
    }

    public function add_encounter($data_medication_request)
    {
        if (isset($data_medication_request['encounter'])) {
            $this->medication_request['encounter'] = [
                'reference' => 'Encounter/' . $data_medication_request['encounter']['reference_id_encounter']
            ];

            if (isset($data_medication_request['encounter']['display'])) {
                $this->medication_request['encounter']['display'] = $data_medication_request['encounter']['display'];
            }
        }
    }

    public function add_authoredOn($data_medication_request)
    {
        if (isset($data_medication_request['authoredOn'])) {
            $this->medication_request['authoredOn'] = $data_medication_request['authoredOn'];
        }
    }

    public function add_requester($data_medication_request)
    {
        if (isset($data_medication_request['requester'])) {
            $this->medication_request['requester'] = [
                'reference' => 'Practitioner/' . $data_medication_request['requester']['reference_id_practitioner']
            ];

            if (isset($data_medication_request['requester']['display'])) {
                $this->medication_request['requester']['display'] = $data_medication_request['requester']['display'];
            }
        }
    }

    public function add_reasonCode($data_medication_request)
    {
        if (isset($data_medication_request['reasonCode'])) {
            $this->medication_request['reasonCode'][] = [
                'coding' => [
                    [
                        'system' => "http://hl7.org/fhir/sid/icd-10",
                        'code' => $data_medication_request['reasonCode']['code'],
                    ]
                ]
            ];

            if (isset($data_medication_request['reasonCode']['display'])) {
                $this->medication_request['reasonCode'][0]['coding'][0]['display'] = $data_medication_request['reasonCode']['display'];
            }
        }
    }

    public function add_courseOfTherapyType($data_medication_request)
    {
        if (isset($data_medication_request['courseOfTherapyType'])) {
            $this->medication_request['courseOfTherapyType'] = [
                'coding' => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/medicationrequest-course-of-therapy",
                        'code' => $data_medication_request['courseOfTherapyType']['code'],
                    ]
                ]
            ];

            if (isset($data_medication_request['courseOfTherapyType']['display'])) {
                $this->medication_request['courseOfTherapyType']['coding'][0]['display'] = $data_medication_request['courseOfTherapyType']['display'];
            }
        }
    }

    public function add_dosageInstruction($data_medication_request)
    {
        if (isset($data_medication_request['ingredient'])) {
            for ($i = 0; $i < count($data_medication_request['ingredient']); $i++) {
                $this->medication_request['ingredient'][] = [
                    'timing' => [
                        'repeat' => [
                            "frequency" => $data_medication_request['ingredient'][$i]['timing']['repeat']['frequency'],
                            "period" => $data_medication_request['ingredient'][$i]['timing']['repeat']['period'],
                            "periodUnit" => $data_medication_request['ingredient'][$i]['timing']['repeat']['periodUnit'],
                        ]
                    ],
                    'route' => [
                        'coding' => [
                            [
                                'system' => "http://www.whocc.no/atc",
                                'code' => $data_medication_request['ingredient'][$i]['route']['code'],
                            ]
                        ]
                    ]
                ];

                if (isset($data_medication_request['ingredient'][$i]['sequence'])) {
                    $this->medication_request['ingredient'][$i]['sequence'] = $data_medication_request['ingredient'][$i]['sequence'];
                }

                if (isset($data_medication_request['ingredient'][$i]['text'])) {
                    $this->medication_request['ingredient'][$i]['text'] = $data_medication_request['ingredient'][$i]['text'];
                }

                if (isset($data_medication_request['ingredient'][$i]['additionalInstruction'])) {
                    $this->medication_request['ingredient'][$i]['additionalInstruction'][] = [
                        'text' => $data_medication_request['ingredient'][$i]['additionalInstruction'][0]['text']
                    ];
                }

                if (isset($data_medication_request['ingredient'][$i]['patientInstruction'])) {
                    $this->medication_request['ingredient'][$i]['patientInstruction'] = $data_medication_request['ingredient'][$i]['patientInstruction'];
                }

                if (isset($data_medication_request['ingredient'][$i]['doseAndRate'])) {
                    for ($j = 0; $j < count($data_medication_request['ingredient'][$i]['doseAndRate']); $j++) {
                        $this->medication_request['ingredient'][$i]['doseAndRate'][$j] = [
                            'type' => [
                                'coding' => [
                                    [
                                        'system' => "http://terminology.hl7.org/CodeSystem/dose-rate-type",
                                        'code' => $data_medication_request['ingredient'][$i]['doseAndRate'][$j]['type']['code'],
                                    ]
                                ]
                            ],
                            'doseQuantity' => [
                                "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                'value' => $data_medication_request['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['value'],
                                'code' => $data_medication_request['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['code'],
                            ]
                        ];

                        if (isset($data_medication_request['ingredient'][$i]['doseAndRate'][$j]['type']['display'])) {
                            $this->medication_request['ingredient'][$i]['doseAndRate'][$j]['type']['coding'][0]['display'] = $data_medication_request['ingredient'][$i]['doseAndRate'][$j]['type']['display'];
                        }

                        if (isset($data_medication_request['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['unit'])) {
                            $this->medication_request['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['unit'] = $data_medication_request['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['unit'];
                        }
                    }
                }
            }
        }
    }

    public function add_dispenseRequest($data_medication_request)
    {
        if (isset($data_medication_request['dispenseRequest'])) {
            if (isset($data_medication_request['dispenseRequest']['dispenseInterval'])) {
                $this->medication_request['dispenseRequest']['dispenseInterval'] = [
                    'system' => 'http://unitsofmeasure.org',
                    'value' => $data_medication_request['dispenseRequest']['dispenseInterval']['value'],
                    'code' => $data_medication_request['dispenseRequest']['dispenseInterval']['code'],
                ];

                if (isset($data_medication_request['dispenseRequest']['dispenseInterval']['unit'])) {
                    $this->medication_request['dispenseRequest']['dispenseInterval']['unit'] = $data_medication_request['dispenseRequest']['dispenseInterval']['unit'];
                }
            }

            if (isset($data_medication_request['dispenseRequest']['validityPeriod'])) {
                $this->medication_request['dispenseRequest']['validityPeriod'] = [
                    'start' => $data_medication_request['dispenseRequest']['validityPeriod']['start'],
                    'end' => $data_medication_request['dispenseRequest']['validityPeriod']['end'],
                ];
            }

            if (isset($data_medication_request['dispenseRequest']['numberOfRepeatsAllowed'])) {
                $this->medication_request['dispenseRequest']['numberOfRepeatsAllowed'] = $data_medication_request['dispenseRequest']['numberOfRepeatsAllowed'];
            }

            if (isset($data_medication_request['dispenseRequest']['quantity'])) {
                $this->medication_request['dispenseRequest']['quantity'] = [
                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm',
                    'value' => $data_medication_request['dispenseRequest']['quantity']['value'],
                    'code' => $data_medication_request['dispenseRequest']['quantity']['code'],
                ];

                if (isset($data_medication_request['dispenseRequest']['quantity']['unit'])) {
                    $this->medication_request['dispenseRequest']['quantity']['unit'] = $data_medication_request['dispenseRequest']['dispenseInterval']['unit'];
                }
            }

            if (isset($data_medication_request['dispenseRequest']['expectedSupplyDuration'])) {
                $this->medication_request['dispenseRequest']['expectedSupplyDuration'] = [
                    'system' => 'http://unitsofmeasure.org',
                    'value' => $data_medication_request['dispenseRequest']['expectedSupplyDuration']['value'],
                    'code' => $data_medication_request['dispenseRequest']['expectedSupplyDuration']['code'],
                ];

                if (isset($data_medication_request['dispenseRequest']['expectedSupplyDuration']['unit'])) {
                    $this->medication_request['dispenseRequest']['expectedSupplyDuration']['unit'] = $data_medication_request['dispenseRequest']['dispenseInterval']['unit'];
                }
            }

            if (isset($data_medication_request['dispenseRequest']['performer']['reference_id_organization'])) {
                $this->medication_request['dispenseRequest']['performer']['reference'] = 'Organization/' . $data_medication_request['dispenseRequest']['performer']['reference_id_organization'];
            }
        }
    }

    public function json($data_medication_request)
    {
        $this->add_identifier($data_medication_request);
        $this->add_status($data_medication_request);
        $this->add_intent($data_medication_request);
        $this->add_category($data_medication_request);
        $this->add_priority($data_medication_request);
        $this->add_medicationReference($data_medication_request);
        $this->add_subject($data_medication_request);
        $this->add_encounter($data_medication_request);
        $this->add_authoredOn($data_medication_request);
        $this->add_requester($data_medication_request);
        $this->add_reasonCode($data_medication_request);
        $this->add_courseOfTherapyType($data_medication_request);
        $this->add_dosageInstruction($data_medication_request);
        $this->add_dispenseRequest($data_medication_request);

        return json_encode($this->medication_request, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_medication_request(MedicationRequestRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_medication_request = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_medication_request));
        $data = $this->json($data_medication_request);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/MedicationRequest';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                MedicationRequest::create([
                    'medication_request_id' => $response->id,
                    'medication_request_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Medication Request Success',
                    'data' => [
                        'medication_request_id' => $response->id,
                        'medication_request_status' => $response->status
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
                'message' => 'Create Medication Request Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
