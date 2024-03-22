<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use App\Models\DiagnosticReport;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\DiagnosticReportRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class DiagnosticReportController extends Controller
{
    public $diagnostic_report = [
        'resourceType' => 'DiagnosticReport',
    ];

    public function add_identifier($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['identifier'])) {
            for ($i = 0; $i < count($data_diagnostic_report['identifier']); $i++) {
                $this->diagnostic_report['identifier'][] = [
                    'system' => $data_diagnostic_report['identifier'][$i]['system'],
                    'value' => $data_diagnostic_report['identifier'][$i]['value'],
                ];

                if (isset($data_diagnostic_report['identifier'][$i]['use'])) {
                    $this->diagnostic_report['identifier'][$i]['use'] = $data_diagnostic_report['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['status_diagnostic_report'])) {
            $this->diagnostic_report['status'] = $data_diagnostic_report['status_diagnostic_report'];
        }
    }

    public function add_category($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['category'])) {
            for ($i = 0; $i < count($data_diagnostic_report['category']); $i++) {
                $this->diagnostic_report['category'][] = [
                    'coding' => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/v2-0074",
                            'code' => $data_diagnostic_report['category'][$i]['code'],
                        ]
                    ]
                ];

                if (isset($data_diagnostic_report['category'][$i]['display'])) {
                    $this->diagnostic_report['category'][$i]['coding'][0]['display'] = $data_diagnostic_report['category'][$i]['display'];
                }
            }
        }
    }

    public function add_code($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['code'])) {
            $this->diagnostic_report['code'] = [
                'coding' => [
                    [
                        'system' => "http://loinc.org",
                        'code' => $data_diagnostic_report['code']['code'],
                    ]
                ]
            ];

            if (isset($data_diagnostic_report['code']['display'])) {
                $this->diagnostic_report['code']['coding'][0]['display'] = $data_diagnostic_report['code']['display'];
            }
        }
    }

    public function add_subject($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['subject'])) {
            $this->diagnostic_report['subject'] = [
                'reference' => 'Patient/' . $data_diagnostic_report['subject']['reference_id_patient']
            ];

            if (isset($data_diagnostic_report['subject']['display'])) {
                $this->diagnostic_report['subject']['display'] = $data_diagnostic_report['subject']['display'];
            }
        }
    }

    public function add_performer($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['performer'])) {
            for ($i = 0; $i < count($data_diagnostic_report['performer']); $i++) {
                if (isset($data_diagnostic_report['performer'][$i]['reference_id_practitioner'])) {
                    $this->diagnostic_report['performer'][$i] = [
                        'reference' => 'Practitioner/' . $data_diagnostic_report['performer'][$i]['reference_id_practitioner']
                    ];
                }

                if (isset($data_diagnostic_report['performer'][$i]['reference_id_organization'])) {
                    $this->diagnostic_report['performer'][$i] = [
                        'reference' => 'Organization/' . $data_diagnostic_report['performer'][$i]['reference_id_organization']
                    ];
                }

                if (isset($data_diagnostic_report['performer'][$i]['display'])) {
                    $this->diagnostic_report['performer'][$i]['display'] = $data_diagnostic_report['performer'][$i]['display'];
                }
            }
        }
    }

    public function add_encounter($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['encounter'])) {
            $this->diagnostic_report['encounter'] = [
                'reference' => 'Encounter/' . $data_diagnostic_report['encounter']['reference_id_encounter']
            ];

            if (isset($data_diagnostic_report['encounter']['display'])) {
                $this->diagnostic_report['encounter']['display'] = $data_diagnostic_report['encounter']['display'];
            }
        }
    }

    public function add_effectiveDateTime($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['effectiveDateTime'])) {
            $this->diagnostic_report['effectiveDateTime'] = $data_diagnostic_report['effectiveDateTime'];
        }
    }

    public function add_issued($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['issued'])) {
            $this->diagnostic_report['issued'] = $data_diagnostic_report['issued'];
        }
    }

    public function add_result($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['result'])) {
            for ($i = 0; $i < count($data_diagnostic_report['result']); $i++) {
                if (isset($data_diagnostic_report['result'][$i]['reference_id_observation'])) {
                    $this->diagnostic_report['result'][$i] = [
                        'reference' => 'Observation/' . $data_diagnostic_report['result'][$i]['reference_id_observation']
                    ];
                }

                if (isset($data_diagnostic_report['result'][$i]['display'])) {
                    $this->diagnostic_report['result'][$i]['display'] = $data_diagnostic_report['result'][$i]['display'];
                }
            }
        }
    }

    public function add_spesimen($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['specimen'])) {
            for ($i = 0; $i < count($data_diagnostic_report['specimen']); $i++) {
                if (isset($data_diagnostic_report['specimen'][$i]['reference_id_spesimen'])) {
                    $this->diagnostic_report['specimen'][$i] = [
                        'reference' => 'Specimen/' . $data_diagnostic_report['specimen'][$i]['reference_id_spesimen']
                    ];
                }

                if (isset($data_diagnostic_report['specimen'][$i]['display'])) {
                    $this->diagnostic_report['specimen'][$i]['display'] = $data_diagnostic_report['specimen'][$i]['display'];
                }
            }
        }
    }

    public function add_imaging_study($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['imaging_study'])) {
            for ($i = 0; $i < count($data_diagnostic_report['imaging_study']); $i++) {
                if (isset($data_diagnostic_report['imaging_study'][$i]['reference_id_imaging_study'])) {
                    $this->diagnostic_report['imaging_study'][$i] = [
                        'reference' => 'ImagingStudy/' . $data_diagnostic_report['imaging_study'][$i]['reference_id_imaging_study']
                    ];
                }

                if (isset($data_diagnostic_report['imaging_study'][$i]['display'])) {
                    $this->diagnostic_report['imaging_study'][$i]['display'] = $data_diagnostic_report['imaging_study'][$i]['display'];
                }
            }
        }
    }

    public function add_basedOn($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['basedOn'])) {
            for ($i = 0; $i < count($data_diagnostic_report['basedOn']); $i++) {
                if (isset($data_diagnostic_report['basedOn'][$i]['reference_id_service_request'])) {
                    $this->diagnostic_report['basedOn'][$i] = [
                        'reference' => 'ServiceRequest/' . $data_diagnostic_report['basedOn'][$i]['reference_id_service_request']
                    ];
                }

                if (isset($data_diagnostic_report['basedOn'][$i]['display'])) {
                    $this->diagnostic_report['basedOn'][$i]['display'] = $data_diagnostic_report['basedOn'][$i]['display'];
                }
            }
        }
    }

    public function add_conclusionCode($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['conclusionCode'])) {
            for ($i = 0; $i < count($data_diagnostic_report['conclusionCode']); $i++) {
                $this->diagnostic_report['conclusionCode'][] = [
                    'coding' => [
                        [
                            "system" => $data_diagnostic_report['conclusionCode'][$i]['system'],
                            'code' => $data_diagnostic_report['conclusionCode'][$i]['code'],
                        ]
                    ]
                ];

                if (isset($data_diagnostic_report['conclusionCode'][$i]['display'])) {
                    $this->diagnostic_report['conclusionCode'][$i]['coding'][0]['display'] = $data_diagnostic_report['conclusionCode'][$i]['display'];
                }
            }
        }
    }

    public function add_conclusion($data_diagnostic_report)
    {
        if (isset($data_diagnostic_report['conclusion'])) {
            $this->diagnostic_report['conclusion'] = $data_diagnostic_report['conclusion'];
        }
    }

    public function json($data_diagnostic_report)
    {
        $this->add_identifier($data_diagnostic_report);
        $this->add_status($data_diagnostic_report);
        $this->add_category($data_diagnostic_report);
        $this->add_code($data_diagnostic_report);
        $this->add_subject($data_diagnostic_report);
        $this->add_performer($data_diagnostic_report);
        $this->add_encounter($data_diagnostic_report);
        $this->add_effectiveDateTime($data_diagnostic_report);
        $this->add_issued($data_diagnostic_report);
        $this->add_result($data_diagnostic_report);
        $this->add_spesimen($data_diagnostic_report);
        $this->add_imaging_study($data_diagnostic_report);
        $this->add_basedOn($data_diagnostic_report);
        $this->add_conclusionCode($data_diagnostic_report);
        $this->add_conclusion($data_diagnostic_report);

        return json_encode($this->diagnostic_report, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_diagnostic_report(DiagnosticReportRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_diagnostic_report = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_diagnostic_report));
        $data = $this->json($data_diagnostic_report);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/DiagnosticReport';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                DiagnosticReport::create([
                    'diagnostic_report_id' => $response->id,
                    'diagnostic_report_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Diagnostic Report Success',
                    'data' => [
                        'diagnostic_report_id' => $response->id,
                        'diagnostic_report_status' => $response->status
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
                'message' => 'Create Diagnostic Report Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
