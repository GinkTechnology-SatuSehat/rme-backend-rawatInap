<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use App\Models\ClinicalImpression;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\ClinicalImpressionRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClinicalImpressionController extends Controller
{
    public $clinical_impression = [
        'resourceType' => 'ClinicalImpression',
    ];

    public function add_identifier($data_clinical_impression)
    {
        if (isset($data_clinical_impression['identifier'])) {
            for ($i = 0; $i < count($data_clinical_impression['identifier']); $i++) {
                $this->clinical_impression['identifier'][] = [
                    'system' => $data_clinical_impression['identifier'][$i]['system'],
                    'value' => $data_clinical_impression['identifier'][$i]['value'],
                ];

                if (isset($data_clinical_impression['identifier'][$i]['use'])) {
                    $this->clinical_impression['identifier'][$i]['use'] = $data_clinical_impression['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_clinical_impression)
    {
        if (isset($data_clinical_impression['status_clinical_impression'])) {
            $this->clinical_impression['status'] = $data_clinical_impression['status_clinical_impression'];
        }
    }

    public function add_description($data_clinical_impression)
    {
        if (isset($data_clinical_impression['description'])) {
            $this->clinical_impression['description'] = $data_clinical_impression['description'];
        }
    }

    public function add_subject($data_clinical_impression)
    {
        if (isset($data_clinical_impression['subject'])) {
            $this->clinical_impression['subject'] = [
                'reference' => 'Patient/' . $data_clinical_impression['subject']['reference_id_patient']
            ];

            if (isset($data_clinical_impression['subject']['display'])) {
                $this->clinical_impression['subject']['display'] = $data_clinical_impression['subject']['display'];
            }
        }
    }

    public function add_encounter($data_clinical_impression)
    {
        if (isset($data_clinical_impression['encounter'])) {
            $this->clinical_impression['encounter'] = [
                'reference' => 'Encounter/' . $data_clinical_impression['encounter']['reference_id_encounter']
            ];

            if (isset($data_clinical_impression['encounter']['display'])) {
                $this->clinical_impression['encounter']['display'] = $data_clinical_impression['encounter']['display'];
            }
        }
    }

    public function add_effectiveDateTime($data_clinical_impression)
    {
        if (isset($data_clinical_impression['effectiveDateTime'])) {
            $this->clinical_impression['effectiveDateTime'] = $data_clinical_impression['effectiveDateTime'];
        }
    }

    public function add_date($data_clinical_impression)
    {
        if (isset($data_clinical_impression['date'])) {
            $this->clinical_impression['date'] = $data_clinical_impression['date'];
        }
    }

    public function add_assessor($data_clinical_impression)
    {
        if (isset($data_clinical_impression['assessor'])) {
            $this->clinical_impression['assessor']['reference'] = 'Practitioner/' . $data_clinical_impression['assessor']['reference_id_practitioner'];

            if (isset($data_clinical_impression['assessor']['display'])) {
                $this->clinical_impression['assessor']['display'] = $data_clinical_impression['assessor']['display'];
            }
        }
    }

    public function add_problem($data_clinical_impression)
    {
        if (isset($data_clinical_impression['problem'])) {
            for ($i = 0; $i < count($data_clinical_impression['problem']); $i++) {
                if (isset($data_clinical_impression['problem'][$i]['reference_id_condition'])) {
                    $this->clinical_impression['problem'][$i] = [
                        'reference' => 'Condition/' . $data_clinical_impression['problem'][$i]['reference_id_condition']
                    ];
                }

                if (isset($data_clinical_impression['problem'][$i]['display'])) {
                    $this->clinical_impression['problem'][$i]['display'] = $data_clinical_impression['problem'][$i]['display'];
                }
            }
        }
    }

    public function add_investigasion($data_clinical_impression)
    {
        if (isset($data_clinical_impression['investigation'])) {
            for ($i = 0; $i < count($data_clinical_impression['investigation']); $i++) {
                if (isset($data_clinical_impression['investigation'][$i]['code'])) {
                    $this->clinical_impression['investigation'][$i]['code'] = [
                        'text' => $data_clinical_impression['investigation'][$i]['code']['text']
                    ];
                }

                if (isset($data_clinical_impression['investigation'][$i]['item'])) {
                    for ($j = 0; $j < count($data_clinical_impression['investigation'][$i]['item']); $j++) {
                        $this->clinical_impression['investigation'][$i]['item'][$j] = [
                            'reference' => 'Observation/' . $data_clinical_impression['investigation'][$i]['item'][$j]['reference_id_observation']
                        ];

                        if (isset($data_clinical_impression['investigation'][$i]['item'][$j]['display'])) {
                            $this->clinical_impression['investigation'][$i]['item'][$j]['display'] = $data_clinical_impression['investigation'][$i]['item'][$j]['display'];
                        }
                    }
                }
            }
        }
    }

    public function add_summary($data_clinical_impression)
    {
        if (isset($data_clinical_impression['summary'])) {
            $this->clinical_impression['summary'] = $data_clinical_impression['summary'];
        }
    }

    public function add_finding($data_clinical_impression)
    {
        if (isset($data_clinical_impression['finding'])) {
            for ($i = 0; $i < count($data_clinical_impression['finding']); $i++) {
                if (isset($data_clinical_impression['finding'][$i]['itemCodeableConcept'])) {
                    $this->clinical_impression['finding'][$i]['itemCodeableConcept'] = [
                        'coding' => [
                            [
                                'system' => 'http://hl7.org/fhir/sid/icd-10',
                                'code' => $data_clinical_impression['finding'][$i]['itemCodeableConcept']['code'],
                                'display' => $data_clinical_impression['finding'][$i]['itemCodeableConcept']['display'],
                            ]
                        ]
                    ];
                }

                if (isset($data_clinical_impression['finding'][$i]['itemReference'])) {
                    $this->clinical_impression['finding'][$i]['itemReference'] = [
                        'reference' => 'Condition/' . $data_clinical_impression['finding'][$i]['itemReference']['reference_id_condition']
                    ];

                    if (isset($data_clinical_impression['finding'][$i]['itemReference']['display'])) {
                        $this->clinical_impression['finding'][$i]['itemReference']['display'] = $data_clinical_impression['findings'][$i]['itemReference']['display'];
                    }
                }
            }
        }
    }

    public function add_prognosisCodeableConcept($data_clinical_impression)
    {
        if (isset($data_clinical_impression['prognosisCodeableConcept'])) {
            $this->clinical_impression['prognosisCodeableConcept'][] = [
                'coding' => [
                    [
                        'system' => 'http://snomed.info/sct',
                        'code' => $data_clinical_impression['prognosisCodeableConcept']['code'],
                    ]
                ]
            ];

            if (isset($data_clinical_impression['prognosisCodeableConcept']['display'])) {
                $this->clinical_impression['prognosisCodeableConcept'][0]['coding'][0]['display'] = $data_clinical_impression['prognosisCodeableConcept']['display'];
            }
        }
    }

    public function json($data_clinical_impression)
    {
        $this->add_identifier($data_clinical_impression);
        $this->add_status($data_clinical_impression);
        $this->add_description($data_clinical_impression);
        $this->add_subject($data_clinical_impression);
        $this->add_encounter($data_clinical_impression);
        $this->add_effectiveDateTime($data_clinical_impression);
        $this->add_date($data_clinical_impression);
        $this->add_assessor($data_clinical_impression);
        $this->add_problem($data_clinical_impression);
        $this->add_investigasion($data_clinical_impression);
        $this->add_summary($data_clinical_impression);
        $this->add_finding($data_clinical_impression);
        $this->add_prognosisCodeableConcept($data_clinical_impression);

        return json_encode($this->clinical_impression, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_clinical_impression(ClinicalImpressionRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_clinical_impression = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_clinical_impression));
        $data = $this->json($data_clinical_impression);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/ClinicalImpression';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                ClinicalImpression::create([
                    'clinical_impression_id' => $response->id,
                    'clinical_impression_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Clinical Impression Success',
                    'data' => [
                        'clinical_impression_id' => $response->id,
                        'clinical_impression_status' => $response->status
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
                'message' => 'Create Clinical Impression Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}