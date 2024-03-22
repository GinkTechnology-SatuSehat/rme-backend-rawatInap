<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\Composition;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\CompositionRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompositionController extends Controller
{
    public $composition = [
        'resourceType' => 'Composition',
    ];

    public function add_identifier($data_composition)
    {
        if (isset($data_composition['identifier'])) {
            $this->composition['identifier'] = [
                'system' => $data_composition['identifier']['system'],
                'value' => $data_composition['identifier']['value'],
            ];

            if (isset($data_composition['identifier']['use'])) {
                $this->composition['identifier']['use'] = $data_composition['identifier']['use'];
            }
        }
    }

    public function add_status($data_composition)
    {
        if (isset($data_composition['status_composition'])) {
            $this->composition['status'] = $data_composition['status_composition'];
        }
    }

    public function add_type($data_composition)
    {
        if (isset($data_composition['type'])) {
            $this->composition['type'] = [
                'coding' => [
                    [
                        'system' => 'http://loinc.org',
                        'code' => $data_composition['type']['code'],
                    ]
                ]
            ];

            if (isset($data_composition['type']['display'])) {
                $this->composition['type']['coding'][0]['display'] = $data_composition['type']['display'];
            }
        }
    }

    public function add_category($data_composition)
    {
        if (isset($data_composition['category'])) {
            $this->composition['category'][] = [
                'coding' => [
                    [
                        "system" => "http://loinc.org",
                        'code' => $data_composition['category']['code'],
                    ]
                ]
            ];

            if (isset($data_composition['category']['display'])) {
                $this->composition['category'][0]['coding'][0]['display'] = $data_composition['category']['display'];
            }
        }
    }

    public function add_subject($data_composition)
    {
        if (isset($data_composition['subject'])) {
            $this->composition['subject'] = [
                'reference' => 'Patient/' . $data_composition['subject']['reference_id_patient']
            ];

            if (isset($data_composition['subject']['display'])) {
                $this->composition['subject']['display'] = $data_composition['subject']['display'];
            }
        }
    }

    public function add_encounter($data_composition)
    {
        if (isset($data_composition['encounter'])) {
            $this->composition['encounter'] = [
                'reference' => 'Encounter/' . $data_composition['encounter']['reference_id_encounter']
            ];

            if (isset($data_composition['encounter']['display'])) {
                $this->composition['encounter']['display'] = $data_composition['encounter']['display'];
            }
        }
    }

    public function add_date($data_composition)
    {
        if (isset($data_composition['date'])) {
            $this->composition['date'] = $data_composition['date'];
        }
    }

    public function add_author($data_composition)
    {
        if (isset($data_composition['author'])) {
            for ($i = 0; $i < count($data_composition['author']); $i++) {
                if (isset($data_composition['author'][$i]['reference_id_practitioner'])) {
                    $this->composition['author'][$i] = [
                        'reference' => 'Practitioner/' . $data_composition['author'][$i]['reference_id_practitioner']
                    ];
                }

                if (isset($data_composition['author'][$i]['display'])) {
                    $this->composition['author'][$i]['display'] = $data_composition['author'][$i]['display'];
                }
            }
        }
    }

    public function add_title($data_composition)
    {
        if (isset($data_composition['title'])) {
            $this->composition['title'] = $data_composition['title'];
        }
    }

    public function add_custodian($data_composition)
    {
        if (isset($data_composition['custodian'])) {
            $this->composition['custodian']['reference'] = 'Organization/' . $data_composition['custodian']['reference_id_organization'];

            if (isset($data_composition['custodian']['display'])) {
                $this->composition['custodian']['display'] = $data_composition['custodian']['display'];
            }
        }
    }

    public function add_section($data_composition)
    {
        if (isset($data_composition['section'])) {
            for ($i = 0; $i < count($data_composition['section']); $i++) {
                $this->composition['section'][] = [
                    'code' => [
                        'coding' => [
                            [
                                'system' => "http://loinc.org",
                                'code' => $data_composition['section'][$i]['code']['code'],
                            ]
                        ]
                    ],
                    'text' => [
                        'status' => $data_composition['section'][$i]['text']['status'],
                        'div' => $data_composition['section'][$i]['text']['div']
                    ]
                ];

                if (isset($data_composition['section'][$i]['code']['display'])) {
                    $this->composition['section'][$i]['code']['coding'][0]['display'] = $data_composition['section'][$i]['code']['display'];
                }

                // if (isset($data_composition['section'][$i]['text'])) {
                //     $this->composition['section'][$i]['text'] = [
                //         'status' => $data_composition['section'][$i]['text']['status'],
                //         'div' => $data_composition['section'][$i]['text']['div']
                //     ];
                // }
            }
        }
    }

    public function json($data_composition)
    {
        $this->add_identifier($data_composition);
        $this->add_status($data_composition);
        $this->add_type($data_composition);
        $this->add_category($data_composition);
        $this->add_subject($data_composition);
        $this->add_encounter($data_composition);
        $this->add_date($data_composition);
        $this->add_author($data_composition);
        $this->add_title($data_composition);
        $this->add_custodian($data_composition);
        $this->add_section($data_composition);

        return json_encode($this->composition, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_composition(CompositionRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_composition = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_composition));
        $data = $this->json($data_composition);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Composition';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Composition::create([
                    'composition_id' => $response->id,
                    'composition_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Composition Success',
                    'data' => [
                        'composition_id' => $response->id,
                        'composition_status' => $response->status
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
                'message' => 'Create Composition Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}