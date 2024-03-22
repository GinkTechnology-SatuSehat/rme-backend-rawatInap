<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\ImagingStudyRequest;
use App\Models\ImagingStudy;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use GuzzleHttp\Exception\ClientException;

class ImagingStudyController extends Controller
{
    public $imaging_study = [
        'resourceType' => 'ImagingStudy',
    ];

    public function add_identifier($data_imaging_study)
    {
        if (isset($data_imaging_study['identifier'])) {
            for ($i = 0; $i < count($data_imaging_study['identifier']); $i++) {
                $this->imaging_study['identifier'][] = [
                    'system' => $data_imaging_study['identifier'][$i]['system'],
                    'value' => $data_imaging_study['identifier'][$i]['value'],
                ];

                if (isset($data_imaging_study['identifier'][$i]['type'])) {
                    $this->imaging_study['identifier'][$i]['type'] = [
                        'coding' => [
                            [
                                'system' => "http://terminology.hl7.org/CodeSystem/v2-0203",
                                'code' => $data_imaging_study['identifier'][$i]['type']['code']
                            ]
                        ]
                    ];

                    if (isset($data_imaging_study['identifier'][$i]['type']['display'])) {
                        $this->imaging_study['identifier'][$i]['type']['display'] = $data_imaging_study['identifier'][$i]['type']['display'];
                    }
                }

                if (isset($data_imaging_study['identifier'][$i]['use'])) {
                    $this->imaging_study['identifier'][$i]['use'] = $data_imaging_study['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_imaging_study)
    {
        if (isset($data_imaging_study['status_imaging_study'])) {
            $this->imaging_study['status'] = $data_imaging_study['status_imaging_study'];
        }
    }

    public function add_modality($data_imaging_study)
    {
        if (isset($data_imaging_study['modality'])) {
            for ($i = 0; $i < count($data_imaging_study['modality']); $i++) {
                $this->imaging_study['modality'][] = [
                    "system" => $data_imaging_study['modality'][$i]['system'],
                    'code' => $data_imaging_study['modality'][$i]['code'],
                ];

                if (isset($data_imaging_study['modality'][$i]['display'])) {
                    $this->imaging_study['modality'][$i]['display'] = $data_imaging_study['modality'][$i]['display'];
                }
            }
        }
    }

    public function add_subject($data_imaging_study)
    {
        if (isset($data_imaging_study['subject'])) {
            $this->imaging_study['subject'] = [
                'reference' => 'Patient/' . $data_imaging_study['subject']['reference_id_patient']
            ];

            if (isset($data_imaging_study['subject']['display'])) {
                $this->imaging_study['subject']['display'] = $data_imaging_study['subject']['display'];
            }
        }
    }

    public function add_started($data_imaging_study)
    {
        if (isset($data_imaging_study['started'])) {
            $this->imaging_study['started'] = $data_imaging_study['started'];
        }
    }

    public function add_basedOn($data_imaging_study)
    {
        if (isset($data_imaging_study['basedOn'])) {
            for ($i = 0; $i < count($data_imaging_study['basedOn']); $i++) {
                if (isset($data_imaging_study['basedOn'][$i]['reference_id_service_request'])) {
                    $this->imaging_study['basedOn'][$i] = [
                        'reference' => 'ServiceRequest/' . $data_imaging_study['basedOn'][$i]['reference_id_service_request']
                    ];
                }

                if (isset($data_imaging_study['basedOn'][$i]['display'])) {
                    $this->imaging_study['basedOn'][$i]['display'] = $data_imaging_study['basedOn'][$i]['display'];
                }
            }
        }
    }

    public function add_numberOfSeries($data_imaging_study)
    {
        if (isset($data_imaging_study['numberOfSeries'])) {
            $this->imaging_study['numberOfSeries'] = $data_imaging_study['numberOfSeries'];
        }
    }

    public function add_numberOfInstances($data_imaging_study)
    {
        if (isset($data_imaging_study['numberOfInstances'])) {
            $this->imaging_study['numberOfInstances'] = $data_imaging_study['numberOfInstances'];
        }
    }

    public function add_series($data_imaging_study)
    {
        if (isset($data_imaging_study['series'])) {
            for ($i = 0; $i < count($data_imaging_study['series']); $i++) {
                $this->imaging_study['series'][$i] = [
                    'uid' => $data_imaging_study['series'][$i]['uid'],
                    'modality' => [
                        "system" => $data_imaging_study['series'][$i]['modality']['system'],
                        'code' => $data_imaging_study['series'][$i]['modality']['code'],
                    ]
                ];

                if (isset($data_imaging_study['series'][$i]['number'])) {
                    $this->imaging_study['series'][$i]['number'] = $data_imaging_study['series'][$i]['number'];
                }

                if (isset($data_imaging_study['series'][$i]['modality']['display'])) {
                    $this->imaging_study['series'][$i]['modality']['display'] = $data_imaging_study['series'][$i]['modality']['display'];
                }

                if (isset($data_imaging_study['series'][$i]['numberOfInstances'])) {
                    $this->imaging_study['series'][$i]['numberOfInstances'] = $data_imaging_study['series'][$i]['numberOfInstances'];
                }

                if (isset($data_imaging_study['series'][$i]['started'])) {
                    $this->imaging_study['series'][$i]['started'] = $data_imaging_study['series'][$i]['started'];
                }

                if (isset($data_imaging_study['series'][$i]['instance'])) {
                    for ($j = 0; $j < count($data_imaging_study['series'][$i]['instance']); $j++) {
                        $this->imaging_study['series'][$i]['instance'][$j] = [
                            'uid' => $data_imaging_study['series'][$i]['instance'][$j]['uid'],
                        ];

                        if (isset($data_imaging_study['series'][$i]['instance'][$j]['sopClass'])) {
                            $this->imaging_study['series'][$i]['instance'][$j]['sopClass'] = [
                                "system" => $data_imaging_study['series'][$i]['instance'][$j]['sopClass']['system'],
                                'code' => $data_imaging_study['series'][$i]['instance'][$j]['sopClass']['code'],
                            ];
                        }

                        if (isset($data_imaging_study['series'][$i]['instance'][$j]['number'])) {
                            $this->imaging_study['series'][$i]['instance'][$j]['number'] = $data_imaging_study['series'][$i]['instance'][$j]['number'];
                        }

                        if (isset($data_imaging_study['series'][$i]['instance'][$j]['title'])) {
                            $this->imaging_study['series'][$i]['instance'][$j]['title'] = $data_imaging_study['series'][$i]['instance'][$j]['title'];
                        }
                    }
                }
            }
        }
    }

    public function json($data_imaging_study)
    {
        $this->add_identifier($data_imaging_study);
        $this->add_status($data_imaging_study);
        $this->add_modality($data_imaging_study);
        $this->add_subject($data_imaging_study);
        $this->add_started($data_imaging_study);
        $this->add_basedOn($data_imaging_study);
        $this->add_numberOfSeries($data_imaging_study);
        $this->add_numberOfInstances($data_imaging_study);
        $this->add_series($data_imaging_study);

        return json_encode($this->imaging_study, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_imaging_study(ImagingStudyRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_imaging_study = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_imaging_study));
        $data = $this->json($data_imaging_study);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/ImagingStudy';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                ImagingStudy::create([
                    'imaging_study_id' => $response->id,
                    'imaging_study_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Imaging Study Success',
                    'data' => [
                        'imaging_study_id' => $response->id,
                        'imaging_study_status' => $response->status
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
                'message' => 'Create Imaging Study Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
