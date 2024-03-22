<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\Medication;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\MedicationRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicationController extends Controller
{
    public $medication = [
        "resourceType" => "Medication",
        "meta" =>  [
            "profile" =>  [
                "https://fhir.kemkes.go.id/r4/StructureDefinition/Medication"
            ]
        ],
    ];

    public function add_identifier($data_medication)
    {
        if (isset($data_medication['identifier'])) {
            for ($i = 0; $i < count($data_medication['identifier']); $i++) {
                $this->medication['identifier'][] = [
                    'system' => $data_medication['identifier'][$i]['system'],
                    'value' => $data_medication['identifier'][$i]['value'],
                ];

                if (isset($data_medication['identifier'][$i]['use'])) {
                    $this->medication['identifier'][$i]['use'] = $data_medication['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_medication)
    {
        if (isset($data_medication['status_medication'])) {
            $this->medication['status'] = $data_medication['status_medication'];
        }
    }

    public function add_code($data_medication)
    {
        if (isset($data_medication['code'])) {
            $this->medication['code'] = [
                'coding' => [
                    [
                        'system' => "http://sys-ids.kemkes.go.id/kfa",
                        'code' => $data_medication['code']['code'],
                    ]
                ]
            ];

            if (isset($data_medication['code']['display'])) {
                $this->medication['code']['coding'][0]['display'] = $data_medication['code']['display'];
            }
        }
    }

    public function add_manufacturer($data_medication)
    {
        if (isset($data_medication['subject'])) {
            $this->medication['subject'] = [
                'reference' => 'Organization/' . $data_medication['subject']['reference_id_organization']
            ];

            if (isset($data_medication['subject']['display'])) {
                $this->medication['subject']['display'] = $data_medication['subject']['display'];
            }
        }
    }

    public function add_form($data_medication)
    {
        if (isset($data_medication['form'])) {
            $this->medication['form'] = [
                'coding' => [
                    [
                        "system" => "http://terminology.kemkes.go.id/CodeSystem/medication-form",
                        'code' => $data_medication['form']['code'],
                    ]
                ]
            ];

            if (isset($data_medication['form']['display'])) {
                $this->medication['form']['coding'][0]['display'] = $data_medication['form']['display'];
            }
        }
    }

    public function add_ingredient($data_medication)
    {
        if (isset($data_medication['ingredient'])) {
            for ($i = 0; $i < count($data_medication['ingredient']); $i++) {
                $this->medication['ingredient'][] = [
                    'itemCodeableConcept' => [
                        'coding' => [
                            [
                                'system' => "http://sys-ids.kemkes.go.id/kfa",
                                'code' => $data_medication['ingredient'][$i]['itemCodeableConcept']['code'],
                            ]
                        ]
                    ],
                    'isActive' => $data_medication['ingredient'][$i]['isActive'],
                    'strength' => [
                        'numerator' => [
                            'system' => "http://unitsofmeasure.org",
                            'value' => $data_medication['ingredient'][$i]['strength']['numerator']['value'],
                            'code' => $data_medication['ingredient'][$i]['strength']['numerator']['code'],
                        ],
                        'denominator' => [
                            'system' => "http://terminology.hl7.org/CodeSystem/v3-orderableDrugForm",
                            'value' => $data_medication['ingredient'][$i]['strength']['denominator']['value'],
                            'code' => $data_medication['ingredient'][$i]['strength']['denominator']['code'],
                        ],
                    ]
                ];

                if (isset($data_medication['ingredient'][$i]['itemCodeableConcept']['display'])) {
                    $this->medication['ingredient'][$i]['itemCodeableConcept']['coding'][0]['display'] = $data_medication['ingredient'][$i]['itemCodeableConcept']['display'];
                }
            }
        }
    }

    public function add_extension($data_medication)
    {
        if (isset($data_medication['extension'])) {
            for ($i = 0; $i < count($data_medication['extension']); $i++) {
                $this->medication['extension'][] = [
                    'url' => $data_medication['extension'][$i]['url'],
                    'valueCodeableConcept' => [
                        'coding' => [
                            [
                                'system' => "http://terminology.kemkes.go.id/CodeSystem/medication-type",
                                'code' => $data_medication['extension'][$i]['valueCodeableConcept']['code'],
                            ]
                        ]
                    ],
                ];

                if (isset($data_medication['extension'][$i]['valueCodeableConcept']['display'])) {
                    $this->medication['extension'][$i]['valueCodeableConcept']['coding'][0]['display'] = $data_medication['extension'][$i]['valueCodeableConcept']['display'];
                }
            }
        }
    }

    public function add_batch($data_medication)
    {
        if (isset($data_medication['batch'])) {
            $this->medication['batch'] = [
                'lotNumber' => $data_medication['batch']['lotNumber'],
                'expirationDate' => $data_medication['batch']['expirationDate'],
            ];
        }
    }

    public function json($data_medication)
    {
        $this->add_identifier($data_medication);
        $this->add_status($data_medication);
        $this->add_code($data_medication);
        $this->add_manufacturer($data_medication);
        $this->add_form($data_medication);
        $this->add_ingredient($data_medication);
        $this->add_extension($data_medication);

        return json_encode($this->medication, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_medication(MedicationRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_diagnosis_report = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_diagnosis_report));
        $data = $this->json($data_diagnosis_report);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Medication';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Medication::create([
                    'medication_id' => $response->id,
                    'medication_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Medication Success',
                    'data' => [
                        'medication_id' => $response->id,
                        'medication_status' => $response->status
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
                'message' => 'Create Medication Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
