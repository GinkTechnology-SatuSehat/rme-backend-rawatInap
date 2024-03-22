<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use App\Models\MedicationDispense;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\MedicationDispenseRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicationDispenseController extends Controller
{
    public $medication_dispense = [
        "resourceType" => "MedicationDispense",
    ];

    public function add_identifier($data_medication_dispense)
    {
        if (isset($data_medication_dispense['identifier'])) {
            for ($i = 0; $i < count($data_medication_dispense['identifier']); $i++) {
                $this->medication_dispense['identifier'][] = [
                    'system' => $data_medication_dispense['identifier'][$i]['system'],
                    'value' => $data_medication_dispense['identifier'][$i]['value'],
                ];

                if (isset($data_medication_dispense['identifier'][$i]['use'])) {
                    $this->medication_dispense['identifier'][$i]['use'] = $data_medication_dispense['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_medication_dispense)
    {
        if (isset($data_medication_dispense['status_medication_dispense'])) {
            $this->medication_dispense['status'] = $data_medication_dispense['status_medication_dispense'];
        }
    }

    public function add_category($data_medication_dispense)
    {
        if (isset($data_medication_dispense['category'])) {
            $this->medication_dispense['category'] = [
                'coding' => [
                    [
                        "system" => "http://terminology.hl7.org/fhir/CodeSystem/medicationdispense-category",
                        'code' => $data_medication_dispense['category']['code'],
                    ]
                ]
            ];

            if (isset($data_medication_dispense['category']['display'])) {
                $this->medication_dispense['category']['coding'][0]['display'] = $data_medication_dispense['category']['display'];
            }
        }
    }

    public function add_medicationReference($data_medication_dispense)
    {
        if (isset($data_medication_dispense['medicationReference'])) {
            $this->medication_dispense['medicationReference'] = [
                'reference' => 'Medication/' . $data_medication_dispense['medicationReference']['reference_id_medication']
            ];

            if (isset($data_medication_dispense['medicationReference']['display'])) {
                $this->medication_dispense['medicationReference']['display'] = $data_medication_dispense['medicationReference']['display'];
            }
        }
    }

    public function add_subject($data_medication_dispense)
    {
        if (isset($data_medication_dispense['subject'])) {
            $this->medication_dispense['subject'] = [
                'reference' => 'Patient/' . $data_medication_dispense['subject']['reference_id_patient']
            ];

            if (isset($data_medication_dispense['subject']['display'])) {
                $this->medication_dispense['subject']['display'] = $data_medication_dispense['subject']['display'];
            }
        }
    }

    public function add_context($data_medication_dispense)
    {
        if (isset($data_medication_dispense['context'])) {
            $this->medication_dispense['context'] = [
                'reference' => 'Encounter/' . $data_medication_dispense['context']['reference_id_encounter']
            ];

            if (isset($data_medication_dispense['context']['display'])) {
                $this->medication_dispense['context']['display'] = $data_medication_dispense['context']['display'];
            }
        }
    }

    public function add_performer($data_medication_dispense)
    {
        if (isset($data_medication_dispense['performer'])) {
            for ($i = 0; $i < count($data_medication_dispense['performer']); $i++) {
                $this->medication_dispense['performer'][$i]['actor'] = [
                    'reference' => 'Practitioner/' . $data_medication_dispense['performer'][$i]['actor']['reference_id_practitioner']
                ];

                if (isset($data_medication_dispense['performer'][$i]['actor']['display'])) {
                    $this->medication_dispense['performer'][$i]['actor']['display'] = $data_medication_dispense['performer'][$i]['actor']['display'];
                }
            }
        }
    }

    public function add_location($data_medication_dispense)
    {
        if (isset($data_medication_dispense['location'])) {
            $this->medication_dispense['location'] = [
                'reference' => 'Location/' . $data_medication_dispense['location']['reference_id_location']
            ];

            if (isset($data_medication_dispense['location']['display'])) {
                $this->medication_dispense['location']['display'] = $data_medication_dispense['location']['display'];
            }
        }
    }

    public function add_authorizingPrescription($data_medication_dispense)
    {
        if (isset($data_medication_dispense['authorizingPrescription'])) {
            for ($i = 0; $i < count($data_medication_dispense['authorizingPrescription']); $i++) {
                $this->medication_dispense['authorizingPrescription'][$i] = [
                    'reference' => 'MedicationRequest/' . $data_medication_dispense['authorizingPrescription'][$i]['reference_id_medication_request']
                ];
            }
        }
    }

    public function add_quantity($data_medication_dispense)
    {
        if (isset($data_medication_dispense['quantity'])) {
            var_dump($data_medication_dispense['quantity']);
            $this->medication_dispense['quantity'] = [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm',
                'value' => $data_medication_dispense['quantity']['value'],
                'code' => $data_medication_dispense['quantity']['code']
            ];
        }
    }

    public function add_daySupply($data_medication_dispense)
    {
        if (isset($data_medication_dispense['daySupply'])) {
            $this->medication_dispense['daySupply'] = [
                'system' => 'http://unitsofmeasure.org',
                'value' => $data_medication_dispense['daySupply']['value'],
                'code' => $data_medication_dispense['daySupply']['code']
            ];

            if (isset($data_medication_dispense['daySupply']['unit'])) {
                $this->medication_dispense['daySupply']['unit'] = $data_medication_dispense['daySupply']['unit'];
            }
        }
    }

    public function add_whenHandedOver($data_medication_dispense)
    {
        if (isset($data_medication_dispense['whenHandedOver'])) {
            $this->medication_dispense['whenHandedOver'] = $data_medication_dispense['whenHandedOver'];
        }
    }

    public function add_dosageInstruction($data_medication_dispense)
    {
        if (isset($data_medication_dispense['ingredient'])) {
            for ($i = 0; $i < count($data_medication_dispense['ingredient']); $i++) {
                $this->medication_dispense['ingredient'][] = [
                    'timing' => [
                        'repeat' => [
                            "frequency" => $data_medication_dispense['ingredient'][$i]['timing']['repeat']['frequency'],
                            "period" => $data_medication_dispense['ingredient'][$i]['timing']['repeat']['period'],
                            "periodUnit" => $data_medication_dispense['ingredient'][$i]['timing']['repeat']['periodUnit'],
                        ]
                    ],
                    'route' => [
                        'coding' => [
                            [
                                'system' => "http://www.whocc.no/atc",
                                'code' => $data_medication_dispense['ingredient'][$i]['route']['code'],
                            ]
                        ]
                    ]
                ];

                if (isset($data_medication_dispense['ingredient'][$i]['sequence'])) {
                    $this->medication_dispense['ingredient'][$i]['sequence'] = $data_medication_dispense['ingredient'][$i]['sequence'];
                }

                if (isset($data_medication_dispense['ingredient'][$i]['text'])) {
                    $this->medication_dispense['ingredient'][$i]['text'] = $data_medication_dispense['ingredient'][$i]['text'];
                }

                if (isset($data_medication_dispense['ingredient'][$i]['additionalInstruction'])) {
                    $this->medication_dispense['ingredient'][$i]['additionalInstruction'][] = [
                        'text' => $data_medication_dispense['ingredient'][$i]['additionalInstruction'][0]['text']
                    ];
                }

                if (isset($data_medication_dispense['ingredient'][$i]['patientInstruction'])) {
                    $this->medication_dispense['ingredient'][$i]['patientInstruction'] = $data_medication_dispense['ingredient'][$i]['patientInstruction'];
                }

                if (isset($data_medication_dispense['ingredient'][$i]['doseAndRate'])) {
                    for ($j = 0; $j < count($data_medication_dispense['ingredient'][$i]['doseAndRate']); $j++) {
                        $this->medication_dispense['ingredient'][$i]['doseAndRate'][$j] = [
                            'type' => [
                                'coding' => [
                                    [
                                        'system' => "http://terminology.hl7.org/CodeSystem/dose-rate-type",
                                        'code' => $data_medication_dispense['ingredient'][$i]['doseAndRate'][$j]['type']['code'],
                                    ]
                                ]
                            ],
                            'doseQuantity' => [
                                "system" => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                                'value' => $data_medication_dispense['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['value'],
                                'code' => $data_medication_dispense['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['code'],
                            ]
                        ];

                        if (isset($data_medication_dispense['ingredient'][$i]['doseAndRate'][$j]['type']['display'])) {
                            $this->medication_dispense['ingredient'][$i]['doseAndRate'][$j]['type']['coding'][0]['display'] = $data_medication_dispense['ingredient'][$i]['doseAndRate'][$j]['type']['display'];
                        }

                        if (isset($data_medication_dispense['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['unit'])) {
                            $this->medication_dispense['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['unit'] = $data_medication_dispense['ingredient'][$i]['doseAndRate'][$j]['doseQuantity']['unit'];
                        }
                    }
                }
            }
        }
    }

    public function json($data_medication_dispense)
    {
        $this->add_identifier($data_medication_dispense);
        $this->add_status($data_medication_dispense);
        $this->add_category($data_medication_dispense);
        $this->add_medicationReference($data_medication_dispense);
        $this->add_quantity($data_medication_dispense);
        $this->add_daySupply($data_medication_dispense);
        $this->add_whenHandedOver($data_medication_dispense);
        $this->add_dosageInstruction($data_medication_dispense);
        $this->add_subject($data_medication_dispense);
        $this->add_context($data_medication_dispense);
        $this->add_performer($data_medication_dispense);
        $this->add_location($data_medication_dispense);
        $this->add_authorizingPrescription($data_medication_dispense);

        return json_encode($this->medication_dispense, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function get_medication_dispense(MedicationDispenseRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];

        //SETUP REQUEST
        $url = $satusehat_base_url . '/MedicationDispense?subject=' . $request->query('subject');
        $request = new Request('GET', $url, $headers, $data = null);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->entry)) {
                return response([
                    'success' => true,
                    'message' => 'Get Medication Dispense Success',
                    'data' => [
                        'medication_dispense_id' => $response->entry[0]->resource->id,
                        'medication_dispense_status' => $response->entry[0]->resource->status
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
                'message' => 'Get Medication Dispense Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }

    public function create_medication_dispense(MedicationDispenseRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_medication_dispense = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_medication_dispense));
        $data = $this->json($data_medication_dispense);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/MedicationDispense';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                MedicationDispense::create([
                    'medication_dispense_id' => $response->id,
                    'medication_dispense_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Medication Dispense Success',
                    'data' => [
                        'medication_dispense_id' => $response->id,
                        'medication_dispense_status' => $response->status
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
                'message' => 'Create Medication Dispense Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
