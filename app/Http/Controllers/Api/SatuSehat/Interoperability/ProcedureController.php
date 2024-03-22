<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\Procedure;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\SatuSehat\Interoperability\ProcedureRequest;

class ProcedureController extends Controller
{
    public $procedure = [
        'resourceType' => 'Procedure',
    ];

    public function add_status($data_procedure)
    {
        if (isset($data_procedure['status_procedure'])) {
            $this->procedure['status'] = $data_procedure['status_procedure'];
        }
    }

    public function add_category($data_procedure)
    {
        if (isset($data_procedure['category'])) {
            $this->procedure['category'] = [
                'coding' => [
                    [
                        "system" => "http://snomed.info/sct",
                        'code' => $data_procedure['category']['code'],
                    ]
                ]
            ];

            if (isset($data_procedure['category'][0]['display'])) {
                $this->procedure['category'][0]['coding'][0]['display'] = $data_procedure['category']['display'];
            }
        }
    }

    public function add_code($data_procedure)
    {
        if (isset($data_procedure['code'])) {
            $this->procedure['code'] = [
                'coding' => [
                    [
                        'system' => $data_procedure['code']['system'],
                        'code' => $data_procedure['code']['code'],
                    ]
                ]
            ];

            if (isset($data_procedure['code']['display'])) {
                $this->procedure['code']['coding'][0]['display'] = $data_procedure['code']['display'];
            }
        }
    }

    public function add_subject($data_procedure)
    {
        if (isset($data_procedure['subject'])) {
            $this->procedure['subject'] = [
                'reference' => 'Patient/' . $data_procedure['subject']['reference_id_patient']
            ];

            if (isset($data_procedure['subject']['display'])) {
                $this->procedure['subject']['display'] = $data_procedure['subject']['display'];
            }
        }
    }

    public function add_performer($data_procedure)
    {
        if (isset($data_procedure['performer'])) {
            for ($i = 0; $i < count($data_procedure['performer']); $i++) {
                $this->procedure['performer'][$i]['actor'] = [
                    'reference' => 'Practitioner/' . $data_procedure['performer'][$i]['actor']['reference_id_practitioner']
                ];

                if (isset($data_procedure['performer'][$i]['actor']['display'])) {
                    $this->procedure['performer'][$i]['actor']['display'] = $data_procedure['performer'][$i]['actor']['display'];
                }
            }
        }
    }

    public function add_performerPeriod($data_procedure)
    {
        if (isset($data_procedure['performerPeriod'])) {
            $this->procedure['performerPeriod'] = [
                'start' => $data_procedure['performerPeriod']['start'],
            ];

            if (isset($data_procedure['performerPeriod']['end'])) {
                $this->procedure['performerPeriod']['end'] = $data_procedure['performerPeriod']['end'];
            }
        }
    }

    public function add_encounter($data_procedure)
    {
        if (isset($data_procedure['encounter'])) {
            $this->procedure['encounter'] = [
                'reference' => 'Encounter/' . $data_procedure['encounter']['reference_id_encounter']
            ];

            if (isset($data_procedure['encounter']['display'])) {
                $this->procedure['encounter']['display'] = $data_procedure['encounter']['display'];
            }
        }
    }

    public function add_bodySite($data_procedure)
    {
        if (isset($data_procedure['bodySite'])) {
            $this->procedure['bodySite'][] = [
                'coding' => [
                    [
                        'system' => "http://snomed.info/sct",
                        'code' => $data_procedure['bodySite']['code'],
                    ]
                ]
            ];

            if (isset($data_procedure['bodySite']['display'])) {
                $this->procedure['bodySite'][0]['coding'][0]['display'] = $data_procedure['bodySite']['display'];
            }
        }
    }

    public function add_reasonCode($data_procedure)
    {
        if (isset($data_procedure['reasonCode'])) {
            $this->procedure['reasonCode'][] = [
                'coding' => [
                    [
                        'system' => "http://hl7.org/fhir/sid/icd-10",
                        'code' => $data_procedure['reasonCode']['code'],
                    ]
                ]
            ];

            if (isset($data_procedure['reasonCode']['display'])) {
                $this->procedure['reasonCode'][0]['coding'][0]['display'] = $data_procedure['reasonCode']['display'];
            }
        }
    }

    public function add_note($data_procedure)
    {
        if (isset($data_procedure['note'])) {
            for ($i = 0; $i < count($data_procedure['note']); $i++) {
                $this->procedure['note'][] = [
                    'text' => $data_procedure['note'][$i]['text']
                ];
            }
        }
    }

    public function json($data_procedure)
    {
        $this->add_status($data_procedure);
        $this->add_category($data_procedure);
        $this->add_code($data_procedure);
        $this->add_subject($data_procedure);
        $this->add_performer($data_procedure);
        $this->add_performerPeriod($data_procedure);
        $this->add_encounter($data_procedure);
        $this->add_bodySite($data_procedure);
        $this->add_reasonCode($data_procedure);
        $this->add_note($data_procedure);

        return json_encode($this->procedure, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_procedure(ProcedureRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_procedure = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_procedure));
        $data = $this->json($data_procedure);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Procedure';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Procedure::create([
                    'procedure_id' => $response->id,
                    'procedure_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Procedure Success',
                    'data' => [
                        'procedure_id' => $response->id,
                        'procedure_status' => $response->status
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
                'message' => 'Create Procedure Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}