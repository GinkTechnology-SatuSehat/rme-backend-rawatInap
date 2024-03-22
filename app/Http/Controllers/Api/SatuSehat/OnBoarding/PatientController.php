<?php

namespace App\Http\Controllers\Api\SatuSehat\OnBoarding;

use GuzzleHttp\Client;
use App\Models\Patient;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\SatuSehat\OnBoarding\PatientRequest;

class PatientController extends Controller
{
    public $patient = [
        'resourceType' => 'Patient',
        'meta' => [
            'profile' => [
                "https://fhir.kemkes.go.id/r4/StructureDefinition/Patient"
            ]
        ]
    ];

    public function add_identifier($data_patient)
    {
        if (isset($data_patient['identifier'])) {
            for ($i = 0; $i < count($data_patient['identifier']); $i++) {
                $this->patient['identifier'][] = [
                    'system' => $data_patient['identifier'][$i]['system'],
                    'value' => $data_patient['identifier'][$i]['value'],
                    'use' => $data_patient['identifier'][$i]['use']
                ];
            }
        }
    }

    public function add_status($data_patient)
    {
        if (isset($data_patient['status_patient'])) {
            $this->patient['active'] = $data_patient['status_patient'];
        }
    }

    public function add_name($data_patient)
    {
        if (isset($data_patient['name'])) {
            $this->patient['name'][] = [
                'use' => $data_patient['name']['use'],
                'text' => $data_patient['name']['text']
            ];
        }
    }

    public function add_telecom($data_patient)
    {
        if (isset($data_patient['telecom'])) {
            for ($i = 0; $i < count($data_patient['telecom']); $i++) {
                $this->patient['telecom'][] = [
                    'system' => $data_patient['telecom'][$i]['system'],
                    'value' => $data_patient['telecom'][$i]['value'],
                    'use' => $data_patient['telecom'][$i]['use']
                ];
            }
        }
    }

    public function add_gender($data_patient)
    {
        if (isset($data_patient['gender'])) {
            $this->patient['gender'] = $data_patient['gender'];
        }
    }

    public function add_birthDate($data_patient)
    {
        if (isset($data_patient['birthDate'])) {
            $this->patient['birthDate'] = $data_patient['birthDate'];
        }
    }

    public function add_deceasedBoolean($data_patient)
    {
        if (isset($data_patient['deceasedBoolean'])) {
            $this->patient['deceasedBoolean'] = $data_patient['deceasedBoolean'];
        }
    }

    public function add_address($data_patient)
    {
        if (isset($data_patient['address'])) {
            for ($i = 0; $i < count($data_patient['address']); $i++) {
                $this->patient['address'][] = [
                    'use' => $data_patient['address'][$i]['use'],
                ];

                if (isset($data_patient['address'][$i]['type'])) {
                    $this->patient['address'][$i]['type'] = $data_patient['address'][$i]['type'];
                }
                if (isset($data_patient['address'][$i]['line'])) {
                    $this->patient['address'][$i]['line'] = $data_patient['address'][$i]['line'];
                }
                if (isset($data_patient['address'][$i]['city'])) {
                    $this->patient['address'][$i]['city'] = $data_patient['address'][$i]['city'];
                }
                if (isset($data_patient['address'][$i]['postalCode'])) {
                    $this->patient['address'][$i]['postalCode'] = $data_patient['address'][$i]['postalCode'];
                }
                if (isset($data_patient['address'][$i]['country'])) {
                    $this->patient['address'][$i]['country'] = $data_patient['address'][$i]['country'];
                }
                // if(isset($data_patient['address'][$i]['extension'])){
                //     $this->patient['address'][] = [
                //         'extension' => [
                //                 [
                //                     'url' => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                //                     'extension' => [
                //                         [
                //                             'url' => 'province',
                //                             'valueCode' => "18"
                //                         ],
                //                         [
                //                             'url' => 'city',
                //                             'valueCode' => "1871"
                //                         ],
                //                         [
                //                             'url' => 'district',
                //                             'valueCode' => "187101"
                //                         ],
                //                         [
                //                             'url' => 'village',
                //                             'valueCode' => "1871011003"
                //                         ],
                //                         [
                //                             'url' => 'rt',
                //                             'valueCode' => "01"
                //                         ],
                //                         [
                //                             'url' => 'rw',
                //                             'valueCode' => "04"
                //                         ],
                //                     ]
                //                 ]
                //             ]
                //         ];
                // }
            }
        }
    }

    public function add_maritalStatus($data_patient)
    {
        if (isset($data_patient['maritalStatus'])) {
            $this->patient['maritalStatus']['coding'][] = [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-MaritalStatus',
                'code' => $data_patient['maritalStatus']['code'],
            ];

            if (isset($data_patient['maritalStatus']['display'])) {
                $this->patient['maritalStatus']['coding'][0]['display'] = $data_patient['maritalStatus']['display'];
            }
        }
    }

    public function add_multipleBirthInteger($data_patient)
    {
        if (isset($data_patient['multipleBirthInteger'])) {
            $this->patient['multipleBirthInteger'] = $data_patient['multipleBirthInteger'];
        }
    }

    public function add_contact($data_patient)
    {
        if (isset($data_patient['contact'])) {
            for ($i = 0; $i < count($data_patient['contact']); $i++) {
                $this->patient['contact'][] = [
                    'name' => [
                        'use' => $data_patient['contact'][$i]['name']['use'],
                        'text' => $data_patient['contact'][$i]['name']['text']
                    ],
                ];

                if (isset($data_patient['contact'][$i]['relationship'])) {
                    for ($j = 0; $j < count($data_patient['contact'][$i]['relationship']); $j++) {
                        $this->patient['contact'][$i]['relationship'][] = [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v2-0131',
                                    'code' => $data_patient['contact'][$i]['relationship'][$j]['code'],
                                ]
                            ]
                        ];

                        if (isset($data_patient['contact'][$i]['relationship'][$j]['display'])) {
                            $this->patient['contact'][$i]['relationship'][$j]['coding'][0]['display'] = $data_patient['contact'][$i]['relationship'][$j]['display'];
                        }
                    }
                }

                if (isset($data_patient['contact'][$i]['telecom'])) {
                    for ($j = 0; $j < count($data_patient['contact'][$i]['telecom']); $j++) {
                        $this->patient['contact'][$i]['telecom'][] = [
                            'system' => $data_patient['contact'][$i]['telecom'][$j]['system'],
                            'value' => $data_patient['contact'][$i]['telecom'][$j]['value'],
                            'use' => $data_patient['contact'][$i]['telecom'][$j]['use']
                        ];
                    }
                }
            }
        }
    }

    public function add_communication($data_patient)
    {
        if (isset($data_patient['communication'])) {
            for ($i = 0; $i < count($data_patient['communication']); $i++) {
                $this->patient['communication'][] = [
                    'language' => [
                        'coding' => [
                            [
                                'system' => 'urn:ietf:bcp:47',
                                'code' => $data_patient['communication'][$i]['language']['code'],
                            ]
                        ]
                    ],
                    'preferred' => $data_patient['communication'][$i]['preferred']
                ];

                if (isset($data_patient['communication'][$i]['language']['display'])) {
                    $this->patient['communication'][$i]['language']['coding'][0]['display'] = $data_patient['communication'][$i]['language']['display'];
                }
            }
        }
    }


    public function json($data_patient)
    {
        $this->add_identifier($data_patient);
        $this->add_status($data_patient);
        $this->add_name($data_patient);
        $this->add_telecom($data_patient);
        $this->add_gender($data_patient);
        $this->add_birthDate($data_patient);
        $this->add_deceasedBoolean($data_patient);
        $this->add_address($data_patient);
        $this->add_maritalStatus($data_patient);
        $this->add_multipleBirthInteger($data_patient);
        $this->add_contact($data_patient);
        $this->add_communication($data_patient);

        return json_encode($this->patient, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function get_patient(PatientRequest $request, $patient_id)
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
            $url = $satusehat_base_url . '/Patient?identifier=' . 'https://fhir.kemkes.go.id/id/nik|' . $nik;
        } else {
            $url = $satusehat_base_url . '/Patient' . '/' . $patient_id;
        }
        $request = new Request('GET', $url, $headers, $data = null);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Patient::create([
                    'patient_id' => $response->id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Get Patient Success',
                    'data' => [
                        'patient_id' => $response->id,
                        // 'patient_name' => $response
                    ]
                ], $statusCode);
            } else if (isset($response->entry)) {
                $data_patient = [];
                for ($i = 0; $i < count($response->entry); $i++) {
                    if (isset($response->entry[$i]->resource)) {
                        $data_patient = $response->entry[$i]->resource;
                    }
                }
                return response([
                    'success' => true,
                    'message' => 'Get Patient Success',
                    'data' => [
                        $data_patient
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
                'message' => 'Get Patient Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }

    public function create_patient(PatientRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_patient = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        $data = $this->json($data_patient);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Patient';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->data->patient_id)) {
                Patient::create([
                    'patient_id' => $response->data->patient_id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Patient Success',
                    'data' => [
                        'patient_id' => $response->data->patient_id,
                        'patient_name' => $data_patient['name']['text'],
                        'patient_status' => $data_patient['status_patient'],
                    ]
                ], $statusCode);
            } else if (isset($response->create_patient->data->patient_id)) {
                Patient::create([
                    'patient_id' => $response->create_patient->data->patient_id,
                    'patient_related_id' => $response->create_related_patient->data->related_patient_id,
                    'patient_mother_id' => $response->update_link_patient->data->patient_id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Patient Success',
                    'data' => [
                        'patient_id' => $response->create_patient->data->patient_id,
                        'patient_related_id' => $response->create_related_patient->data->related_patient_id,
                        'patient_mother_id' => $response->update_link_patient->data->patient_id,
                        'patient_name' => $data_patient['name']['text'],
                        'patient_status' => $data_patient['status_patient']
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
                'message' => 'Create Patient Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
