<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\Observation;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\ObservationRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class ObservationController extends Controller
{
    public $observation = [
        'resourceType' => 'Observation',
    ];

    public function add_identifier($data_observation)
    {
        if (isset($data_observation['identifier'])) {
            for ($i = 0; $i < count($data_observation['identifier']); $i++) {
                $this->observation['identifier'][] = [
                    'system' => $data_observation['identifier'][$i]['system'],
                    'value' => $data_observation['identifier'][$i]['value'],
                ];

                if (isset($data_observation['identifier'][$i]['use'])) {
                    $this->observation['identifier'][$i]['use'] = $data_observation['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_observation)
    {
        if (isset($data_observation['status_observation'])) {
            $this->observation['status'] = $data_observation['status_observation'];
        }
    }

    public function add_category($data_observation)
    {
        if (isset($data_observation['category'])) {
            for ($i = 0; $i < count($data_observation['category']); $i++) {
                $this->observation['category'][] = [
                    'coding' => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                            'code' => $data_observation['category'][$i]['code'],
                        ]
                    ]
                ];

                if (isset($data_observation['category'][$i]['display'])) {
                    $this->observation['category'][$i]['coding'][0]['display'] = $data_observation['category'][$i]['display'];
                }
            }
        }
    }

    public function add_code($data_observation)
    {
        if (isset($data_observation['code'])) {
            $this->observation['code'] = [
                'coding' => [
                    [
                        'system' => $data_observation['code']['system'],
                        'code' => $data_observation['code']['code'],
                    ]
                ]
            ];

            if (isset($data_observation['code']['display'])) {
                $this->observation['code']['coding'][0]['display'] = $data_observation['code']['display'];
            }
        }
    }

    public function add_subject($data_observation)
    {
        if (isset($data_observation['subject'])) {
            $this->observation['subject'] = [
                'reference' => 'Patient/' . $data_observation['subject']['reference_id_patient']
            ];

            if (isset($data_observation['subject']['display'])) {
                $this->observation['subject']['display'] = $data_observation['subject']['display'];
            }
        }
    }

    public function add_performer($data_observation)
    {
        if (isset($data_observation['performer'])) {
            for ($i = 0; $i < count($data_observation['performer']); $i++) {
                if (isset($data_observation['performer'][$i]['reference_id_practitioner'])) {
                    $this->observation['performer'][$i] = [
                        'reference' => 'Practitioner/' . $data_observation['performer'][$i]['reference_id_practitioner']
                    ];
                }

                if (isset($data_observation['performer'][$i]['reference_id_organization'])) {
                    $this->observation['performer'][$i] = [
                        'reference' => 'Organization/' . $data_observation['performer'][$i]['reference_id_organization']
                    ];
                }

                if (isset($data_observation['performer'][$i]['display'])) {
                    $this->observation['performer'][$i]['display'] = $data_observation['performer'][$i]['display'];
                }
            }
        }
    }

    public function add_encounter($data_observation)
    {
        if (isset($data_observation['encounter'])) {
            $this->observation['encounter'] = [
                'reference' => 'Encounter/' . $data_observation['encounter']['reference_id_encounter']
            ];

            if (isset($data_observation['encounter']['display'])) {
                $this->observation['encounter']['display'] = $data_observation['encounter']['display'];
            }
        }
    }

    public function add_effectiveDateTime($data_observation)
    {
        if (isset($data_observation['effectiveDateTime'])) {
            $this->observation['effectiveDateTime'] = $data_observation['effectiveDateTime'];
        }
    }

    public function add_issued($data_observation)
    {
        if (isset($data_observation['issued'])) {
            $this->observation['issued'] = $data_observation['issued'];
        }
    }

    public function add_valueQuantity($data_observation)
    {
        if (isset($data_observation['valueQuantity'])) {
            $this->observation['valueQuantity'] = [
                'value' => $data_observation['valueQuantity']['value'],
                'unit' => $data_observation['valueQuantity']['unit'],
                'system' => $data_observation['valueQuantity']['system'],
                'code' => $data_observation['valueQuantity']['code']
            ];
        }
    }

    public function add_bodySite($data_observation)
    {
        if (isset($data_observation['bodySite'])) {
            $this->observation['bodySite'] = [
                'coding' => [
                    [
                        'system' => "http://snomed.info/sct",
                        'code' => $data_observation['bodySite']['code'],
                    ]
                ]
            ];

            if (isset($data_observation['bodySite']['display'])) {
                $this->observation['bodySite']['coding'][0]['display'] = $data_observation['bodySite']['display'];
            }
        }
    }

    public function add_interpretation($data_observation)
    {
        if (isset($data_observation['interpretation'])) {
            $this->observation['interpretation'][] = [
                'coding' => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation",
                        'code' => $data_observation['interpretation'][0]['code'],
                    ]
                ]
            ];

            if (isset($data_observation['interpretation']['display'])) {
                $this->observation['interpretation'][0]['coding'][0]['display'] = $data_observation['interpretation']['display'];
            }
        }
    }

    public function add_valueableCodeConcept($data_observation)
    {
        if (isset($data_observation['valueableCodeConcept'])) {
            $this->observation['valueableCodeConcept'] = [
                'coding' => [
                    [
                        'system' => "http://snomed.info/sct",
                        'code' => $data_observation['valueableCodeConcept']['code'],
                    ]
                ]
            ];

            if (isset($data_observation['valueableCodeConcept']['display'])) {
                $this->observation['valueableCodeConcept']['coding'][0]['display'] = $data_observation['valueableCodeConcept']['display'];
            }
        }
    }

    public function add_spesimen($data_observation)
    {
        if (isset($data_observation['specimen'])) {
            $this->observation['specimen'] = [
                'reference' => 'Specimen/' . $data_observation['specimen']['reference_id_specimen']
            ];

            if (isset($data_observation['specimen']['display'])) {
                $this->observation['specimen']['display'] = $data_observation['specimen']['display'];
            }
        }
    }

    public function add_basedOn($data_observation)
    {
        if (isset($data_observation['basedOn'])) {
            for ($i = 0; $i < count($data_observation['basedOn']); $i++) {
                if (isset($data_observation['basedOn'][$i]['reference_id_service_request'])) {
                    $this->observation['basedOn'][$i] = [
                        'reference' => 'ServiceRequest/' . $data_observation['basedOn'][$i]['reference_id_service_request']
                    ];
                }

                if (isset($data_observation['basedOn'][$i]['display'])) {
                    $this->observation['basedOn'][$i]['display'] = $data_observation['basedOn'][$i]['display'];
                }
            }
        }
    }

    public function add_referenceRange($data_observation)
    {
        if (isset($data_observation['referenceRange'])) {
            for ($i = 0; $i < count($data_observation['referenceRange']); $i++) {
                if (isset($data_observation['referenceRange'][$i]['text'])) {
                    $this->observation['referenceRange'][$i] = [
                        'text' => $data_observation['referenceRange'][$i]['text']
                    ];
                }

                if (isset($data_observation['referenceRange'][$i]['low'])) {
                    $this->observation['referenceRange'][$i]['low'] = [
                        'value' => $data_observation['referenceRange'][$i]['low']['value'],
                        'unit' => $data_observation['referenceRange'][$i]['low']['unit'],
                        'system' => $data_observation['referenceRange'][$i]['low']['system'],
                        'code' => $data_observation['referenceRange'][$i]['low']['code']
                    ];
                }

                if (isset($data_observation['referenceRange'][$i]['high'])) {
                    $this->observation['referenceRange'][$i]['high'] = [
                        'value' => $data_observation['referenceRange'][$i]['high']['value'],
                        'unit' => $data_observation['referenceRange'][$i]['high']['unit'],
                        'system' => $data_observation['referenceRange'][$i]['high']['system'],
                        'code' => $data_observation['referenceRange'][$i]['high']['code']
                    ];
                }
            }
        }
    }

    public function add_valueString($data_observation)
    {
        if (isset($data_observation['valueString'])) {
            $this->observation['valueString'] = $data_observation['valueString'];
        }
    }

    public function add_derivedFrom($data_observation)
    {
        if (isset($data_observation['derivedFrom'])) {
            $this->observation['derivedFrom'] = [
                'reference' => 'ImagingStudy/' . $data_observation['derivedFrom']['reference_id_imaging_study']
            ];

            if (isset($data_observation['derivedFrom']['display'])) {
                $this->observation['derivedFrom']['display'] = $data_observation['derivedFrom']['display'];
            }
        }
    }

    public function json($data_observation)
    {
        $this->add_identifier($data_observation);
        $this->add_status($data_observation);
        $this->add_category($data_observation);
        $this->add_code($data_observation);
        $this->add_subject($data_observation);
        $this->add_performer($data_observation);
        $this->add_encounter($data_observation);
        $this->add_effectiveDateTime($data_observation);
        $this->add_issued($data_observation);
        $this->add_valueQuantity($data_observation);
        $this->add_bodySite($data_observation);
        $this->add_interpretation($data_observation);
        $this->add_valueableCodeConcept($data_observation);
        $this->add_spesimen($data_observation);
        $this->add_basedOn($data_observation);
        $this->add_referenceRange($data_observation);
        $this->add_valueString($data_observation);
        $this->add_derivedFrom($data_observation);

        return json_encode($this->observation, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_observation(ObservationRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_observation = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_observation));
        $data = $this->json($data_observation);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Observation';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Observation::create([
                    'observation_id' => $response->id,
                    'observation_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Observation Success',
                    'data' => [
                        'observation_id' => $response->id,
                        'observation_status' => $response->status
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
                'message' => 'Create Observation Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
