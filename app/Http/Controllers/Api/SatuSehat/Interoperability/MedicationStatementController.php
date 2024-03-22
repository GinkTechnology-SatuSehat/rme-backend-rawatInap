<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use App\Models\MedicationStatement;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\MedicationStatementRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class MedicationStatementController extends Controller
{
    public $medication_statement = [
        "resourceType" => "MedicationStatement",
    ];

    // public function add_identifier($data_medication_statement)
    // {
    //     if (isset($data_medication_statement['identifier'])) {
    //         for ($i = 0; $i < count($data_medication_statement['identifier']); $i++) {
    //             $this->medication_statement['identifier'][] = [
    //                 'system' => $data_medication_statement['identifier'][$i]['system'],
    //                 'value' => $data_medication_statement['identifier'][$i]['value'],
    //             ];

    //             if (isset($data_medication_statement['identifier'][$i]['use'])) {
    //                 $this->medication_statement['identifier'][$i]['use'] = $data_medication_statement['identifier'][$i]['use'];
    //             }
    //         }
    //     }
    // }

    public function add_status($data_medication_statement)
    {
        if (isset($data_medication_statement['status_medication_statement'])) {
            $this->medication_statement['status'] = $data_medication_statement['status_medication_statement'];
        }
    }

    public function add_category($data_medication_statement)
    {
        if (isset($data_medication_statement['category'])) {
            $this->medication_statement['category'] = [
                'coding' => [
                    [
                        "system" => "http://terminology.hl7.org/CodeSystem/medication-statement-category",
                        'code' => $data_medication_statement['category']['code'],
                    ]
                ]
            ];

            if (isset($data_medication_statement['category']['display'])) {
                $this->medication_statement['category']['coding'][0]['display'] = $data_medication_statement['category']['display'];
            }
        }
    }

    public function add_medicationReference($data_medication_statement)
    {
        if (isset($data_medication_statement['medicationReference'])) {
            $this->medication_statement['medicationReference'] = [
                'reference' => 'Medication/' . $data_medication_statement['medicationReference']['reference_id_medication']
            ];

            if (isset($data_medication_statement['medicationReference']['display'])) {
                $this->medication_statement['medicationReference']['display'] = $data_medication_statement['medicationReference']['display'];
            }
        }
    }

    public function add_subject($data_medication_statement)
    {
        if (isset($data_medication_statement['subject'])) {
            $this->medication_statement['subject'] = [
                'reference' => 'Patient/' . $data_medication_statement['subject']['reference_id_patient']
            ];

            if (isset($data_medication_statement['subject']['display'])) {
                $this->medication_statement['subject']['display'] = $data_medication_statement['subject']['display'];
            }
        }
    }

    public function add_dosage($data_medication_statement)
    {
        if (isset($data_medication_statement['dosage'])) {
            if (isset($data_medication_statement['dosage']['text'])) {
                $this->medication_statement['dosage'][] = [
                    'text' => $data_medication_statement['dosage']['text']
                ];
            }

            if (isset($data_medication_statement['dosage']['timing'])) {
                $this->medication_statement['dosage'][] = [
                    'timing' => [
                        'repeat' => [
                            'frequency' => $data_medication_statement['dosage']['timing']['repeat']['frequency'],
                            'period' => $data_medication_statement['dosage']['timing']['repeat']['period'],
                            'periodUnit' => $data_medication_statement['dosage']['timing']['repeat']['periodUnit'],
                        ]
                    ]
                ];
            }
        }
    }

    public function add_effectiveDateTime($data_medication_statement)
    {
        if (isset($data_medication_statement['effectiveDateTime'])) {
            $this->medication_statement['effectiveDateTime'] = $data_medication_statement['effectiveDateTime'];
        }
    }

    public function add_dateAsserted($data_medication_statement)
    {
        if (isset($data_medication_statement['dateAsserted'])) {
            $this->medication_statement['dateAsserted'] = $data_medication_statement['dateAsserted'];
        }
    }

    public function add_informationSource($data_medication_statement)
    {
        if (isset($data_medication_statement['informationSource'])) {
            $this->medication_statement['informationSource'] = [
                'reference' => 'Patient/' . $data_medication_statement['informationSource']['reference_id_patient']
            ];

            if (isset($data_medication_statement['informationSource']['display'])) {
                $this->medication_statement['informationSource']['display'] = $data_medication_statement['informationSource']['display'];
            }
        }
    }

    public function add_context($data_medication_statement)
    {
        if (isset($data_medication_statement['context'])) {
            $this->medication_statement['context'] = [
                'reference' => 'Encounter/' . $data_medication_statement['context']['reference_id_encounter']
            ];

            if (isset($data_medication_statement['context']['display'])) {
                $this->medication_statement['context']['display'] = $data_medication_statement['context']['display'];
            }
        }
    }

    public function json($data_medication_statement)
    {
        // $this->add_identifier($data_medication_statement);
        $this->add_status($data_medication_statement);
        $this->add_category($data_medication_statement);
        $this->add_medicationReference($data_medication_statement);
        $this->add_subject($data_medication_statement);
        $this->add_context($data_medication_statement);
        $this->add_dosage($data_medication_statement);
        $this->add_effectiveDateTime($data_medication_statement);
        $this->add_dateAsserted($data_medication_statement);
        $this->add_informationSource($data_medication_statement);

        return json_encode($this->medication_statement, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_medication_statement(MedicationStatementRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_medication_statement = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_medication_statement));
        $data = $this->json($data_medication_statement);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/MedicationStatement';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                MedicationStatement::create([
                    'medication_statement_id' => $response->id,
                    'medication_statement_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Medication Statement Success',
                    'data' => [
                        'medication_statement_id' => $response->id,
                        'medication_statement_status' => $response->status
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
                'message' => 'Create Medication Statement Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
