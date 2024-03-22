<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\Encounter;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\SatuSehat\Interoperability\EncounterRequest;

class EncounterController extends Controller
{
    public $encounter = [
        'resourceType' => 'Encounter',
    ];

    public function add_id($data_encounter)
    {
        if (isset($data_encounter['id'])) {
            $this->encounter['id'] = $data_encounter['id'];
        }
    }

    public function add_identifier($data_encounter)
    {
        if (isset($data_encounter['identifier'])) {
            for ($i = 0; $i < count($data_encounter['identifier']); $i++) {
                $this->encounter['identifier'][] = [
                    'system' => $data_encounter['identifier'][$i]['system'],
                    'value' => $data_encounter['identifier'][$i]['value'],
                ];

                if (isset($data_encounter['identifier'][$i]['use'])) {
                    $this->encounter['identifier'][$i]['use'] = $data_encounter['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_encounter)
    {
        if (isset($data_encounter['status_encounter'])) {
            $this->encounter['status'] = $data_encounter['status_encounter'];
        }
    }

    public function add_class($data_encounter)
    {
        if (isset($data_encounter['class'])) {
            $this->encounter['class'] = [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code' => $data_encounter['class']['code'],
            ];

            if (isset($data_encounter['class']['display'])) {
                $this->encounter['class']['display'] = $data_encounter['class']['display'];
            }
        }
    }

    public function add_subject($data_encounter)
    {
        if (isset($data_encounter['subject'])) {
            $this->encounter['subject'] = [
                'reference' => 'Patient/' . $data_encounter['subject']['reference_id_patient']
            ];

            if (isset($data_encounter['subject']['display'])) {
                $this->encounter['subject']['display'] = $data_encounter['subject']['display'];
            }
        }
    }

    public function add_participant($data_encounter)
    {
        if (isset($data_encounter['participant'])) {
            for ($i = 0; $i < count($data_encounter['participant']); $i++) {
                $this->encounter['participant'][] = [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                    'code' => $data_encounter['participant'][$i]['type']['code'],
                                ]
                            ]
                        ]
                    ],
                    'individual' => [
                        'reference' => 'Practitioner/' . $data_encounter['participant'][$i]['individual']['reference_id_practitioner']
                    ]
                ];

                if (isset($data_encounter['participant'][$i]['type']['display'])) {
                    $this->encounter['participant'][$i]['type'][0]['coding'][0]['display'] = $data_encounter['participant'][$i]['type']['display'];
                }

                if (isset($data_encounter['participant'][$i]['individual']['display'])) {
                    $this->encounter['participant'][$i]['individual']['display'] = $data_encounter['participant'][$i]['individual']['display'];
                }
            }
        }
    }

    public function add_period($data_encounter)
    {
        if (isset($data_encounter['period'])) {
            $this->encounter['period'] = [
                'start' => $data_encounter['period']['start'],
            ];

            if (isset($data_encounter['period']['end'])) {
                $this->encounter['period']['end'] = $data_encounter['period']['end'];
            }
        }
    }

    public function add_location($data_encounter)
    {
        if (isset($data_encounter['location'])) {
            for ($i = 0; $i < count($data_encounter['location']); $i++) {
                $this->encounter['location'][] = [
                    'location' => [
                        'reference' => 'Location/' . $data_encounter['location'][$i]['location']['reference_id_location']
                    ],
                ];

                if (isset($data_encounter['location'][$i]['location']['display'])) {
                    $this->encounter['location'][$i]['location']['display'] = $data_encounter['location'][$i]['location']['display'];
                }
            }
        }
    }

    public function add_statusHistory($data_encounter)
    {
        if (isset($data_encounter['statusHistory'])) {
            for ($i = 0; $i < count($data_encounter['statusHistory']); $i++) {
                $this->encounter['statusHistory'][] = [
                    'status' => $data_encounter['statusHistory'][$i]['status'],
                    'period' => [
                        'start' => $data_encounter['statusHistory'][$i]['period']['start'],
                    ]
                ];

                if (isset($data_encounter['statusHistory'][$i]['period']['end'])) {
                    $this->encounter['statusHistory'][$i]['period']['end'] = $data_encounter['statusHistory'][$i]['period']['end'];
                }
            }
        }
    }

    public function add_serviceProvider($data_encounter)
    {
        if (isset($data_encounter['serviceProvider'])) {
            $this->encounter['serviceProvider'] = [
                'reference' => 'Organization/' . $data_encounter['serviceProvider']['reference_id_organization']
            ];

            if (isset($data_encounter['serviceProvider']['display'])) {
                $this->encounter['serviceProvider'] = [
                    'display' => $data_encounter['serviceProvider']['display']
                ];
            }
        }
    }

    public function add_diagnosis($data_encounter)
    {
        if (isset($data_encounter['diagnosis'])) {
            for ($i = 0; $i < count($data_encounter['diagnosis']); $i++) {
                $this->encounter['diagnosis'][] = [
                    'condition' => [
                        'reference' => 'Condition/' . $data_encounter['diagnosis'][$i]['condition']['reference_id_condition']
                    ],
                    'use' => [
                        'coding' => [
                            [
                                'system' => 'http://terminology.hl7.org/CodeSystem/diagnosis-role',
                                'code' => $data_encounter['diagnosis'][$i]['use']['code'],
                            ]
                        ]
                    ]
                ];

                if (isset($data_encounter['diagnosis'][$i]['condition']['display'])) {
                    $this->encounter['diagnosis'][$i]['condition']['display'] = $data_encounter['diagnosis'][$i]['condition']['display'];
                }

                if (isset($data_encounter['diagnosis'][$i]['use']['display'])) {
                    $this->encounter['diagnosis'][$i]['use']['coding'][0]['display'] = $data_encounter['diagnosis'][$i]['use']['display'];
                }

                if (isset($data_encounter['diagnosis'][$i]['rank'])) {
                    $this->encounter['diagnosis'][$i]['rank'] = $data_encounter['diagnosis'][$i]['rank'];
                }
            }
        }
    }

    public function add_basedOn($data_encounter)
    {
        if (isset($data_encounter['basedOn'])) {
            for ($i = 0; $i < count($data_encounter['basedOn']); $i++) {
                $this->encounter['basedOn'][] = [
                    'reference' => 'ServiceRequest/' . $data_encounter['basedOn'][$i]['reference_id_service_request']
                ];
            }
        }
    }

    public function add_hospitalization($data_encounter)
    {
        if (isset($data_encounter['hospitalization'])) {
            $this->encounter['hospitalization'] = [
                'dischargeDisposition' => [
                    'coding' => [
                        [
                            'system' => 'http://terminology.hl7.org/CodeSystem/discharge-disposition',
                            'code' => $data_encounter['hospitalization']['dischargeDisposition']['code'],
                        ]
                    ]
                ]
            ];

            if (isset($data_encounter['hospitalization']['dischargeDisposition']['display'])) {
                $this->encounter['hospitalization']['dischargeDisposition']['coding'][0]['display'] = $data_encounter['hospitalization']['dischargeDisposition']['display'];
            }
        }
    }

    public function json($data_encounter)
    {
        $this->add_id($data_encounter);
        $this->add_identifier($data_encounter);
        $this->add_status($data_encounter);
        $this->add_class($data_encounter);
        $this->add_subject($data_encounter);
        $this->add_participant($data_encounter);
        $this->add_period($data_encounter);
        $this->add_location($data_encounter);
        $this->add_statusHistory($data_encounter);
        $this->add_serviceProvider($data_encounter);
        $this->add_diagnosis($data_encounter);
        $this->add_basedOn($data_encounter);
        $this->add_hospitalization($data_encounter);

        return json_encode($this->encounter, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_encounter(EncounterRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_encounter = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        $data = $this->json($data_encounter);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Encounter';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Encounter::create([
                    'encounter_id' => $response->id,
                    'encounter_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Encounter Success',
                    'data' => [
                        'encounter_id' => $response->id,
                        'encounter_status' => $response->status
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
                'message' => 'Create Encounter Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }

    public function update_encounter(EncounterRequest $request, $encounter_id)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_encounter = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        $data = $this->json($data_encounter);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Encounter' . '/' . $encounter_id;
        $request = new Request('PUT', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Encounter::where('encounter_id', $data_encounter['id'])->update([
                    'encounter_id' => $response->id,
                    'encounter_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Update Encounter Success',
                    'data' => [
                        'encounter_id' => $response->id,
                        'encounter_status' => $response->status
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
                'message' => 'Update Encounter Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
