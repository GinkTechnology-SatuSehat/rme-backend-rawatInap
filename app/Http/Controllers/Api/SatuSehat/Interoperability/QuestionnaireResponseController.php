<?php

namespace App\Http\Controllers\Api\SatuSehat\Interoperability;

use GuzzleHttp\Client;
use App\Models\Observation;
use GuzzleHttp\Psr7\Request;
use App\Models\SatuSehatToken;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\Interoperability\QuestionnaireResponseRequest;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuestionnaireResponseController extends Controller
{
    public $questionnaire_response = [
        "resourceType" => "QuestionnaireResponse",
    ];

    public function add_questionnaire($data_questionnaire_response)
    {
        if (isset($questionnaire['questionnaire'])) {
            $this->questionnaire_response['questionnaire'] = $data_questionnaire_response['questionnaire'];
        }
    }

    public function add_status($data_questionnaire_response)
    {
        if (isset($data_questionnaire_response['status_questionnaire'])) {
            $this->questionnaire_response['status'] = $data_questionnaire_response['status_questionnaire'];
        }
    }

    public function add_subject($data_questionnaire_response)
    {
        if (isset($data_questionnaire_response['subject'])) {
            $this->questionnaire_response['subject'] = [
                'reference' => 'Patient/' . $data_questionnaire_response['subject']['reference_id_patient']
            ];

            if (isset($data_questionnaire_response['subject']['display'])) {
                $this->questionnaire_response['subject']['display'] = $data_questionnaire_response['subject']['display'];
            }
        }
    }

    public function add_encounter($data_questionnaire_response)
    {
        if (isset($data_questionnaire_response['encounter'])) {
            $this->questionnaire_response['encounter'] = [
                'reference' => 'Encounter/' . $data_questionnaire_response['encounter']['reference_id_encounter']
            ];

            if (isset($data_questionnaire_response['encounter']['display'])) {
                $this->questionnaire_response['encounter']['display'] = $data_questionnaire_response['encounter']['display'];
            }
        }
    }

    public function add_authored($data_questionnaire_response)
    {
        if (isset($data_questionnaire_response['authored'])) {
            $this->questionnaire_response['authored'] = $data_questionnaire_response['authored'];
        }
    }

    public function add_author($data_questionnaire_response)
    {
        if (isset($data_questionnaire_response['author'])) {
            $this->questionnaire_response['author'] = [
                'reference' => 'Practitioner/' . $data_questionnaire_response['author']['reference_id_practitioner']
            ];

            if (isset($data_questionnaire_response['author']['display'])) {
                $this->questionnaire_response['author']['display'] = $data_questionnaire_response['author']['display'];
            }
        }
    }

    public function add_source($data_questionnaire_response)
    {
        if (isset($data_questionnaire_response['source'])) {
            $this->questionnaire_response['source'] = [
                'reference' => 'Patient/' . $data_questionnaire_response['source']['reference_id_patient']
            ];

            if (isset($data_questionnaire_response['source']['display'])) {
                $this->questionnaire_response['source']['display'] = $data_questionnaire_response['source']['display'];
            }
        }
    }

    public function add_item($data_questionnaire_response)
    {
        if (isset($data_questionnaire_response['item'])) {
            for ($i = 0; $i < count($data_questionnaire_response['item']); $i++) {
                if (isset($data_questionnaire_response['item'][$i]['linkId'])) {
                    $this->questionnaire_response['item'][$i]['linkId'] = $data_questionnaire_response['item'][$i]['linkId'];
                }

                if (isset($data_questionnaire_response['item'][$i]['text'])) {
                    $this->questionnaire_response['item'][$i]['text'] = $data_questionnaire_response['item'][$i]['text'];
                }

                if (isset($data_questionnaire_response['item'][$i]['answer'])) {
                    for ($j = 0; $j < count($data_questionnaire_response['item'][$i]['answer']); $j++) {
                        if (isset($data_questionnaire_response['item'][$i]['answer'][$j]['valueCoding'])) {
                            $this->questionnaire_response['item'][$i]['answer'][$j]['valueCoding'] = [
                                'system' => $data_questionnaire_response['item'][$i]['answer'][$j]['valueCoding']['system'],
                                'code' => $data_questionnaire_response['item'][$i]['answer'][$j]['valueCoding']['code'],
                                'display' => $data_questionnaire_response['item'][$i]['answer'][$j]['valueCoding']['display']
                            ];
                        }

                        if (isset($data_questionnaire_response['item'][$i]['answer'][$j]['valueBoolean'])) {
                            $this->questionnaire_response['item'][$i]['answer'][$j]['valueBoolean'] = $data_questionnaire_response['item'][$i]['answer'][$j]['valueBoolean'];
                        }
                    }
                }
            }
        }
    }

    public function json($data_questionnaire_response)
    {
        $this->add_questionnaire($data_questionnaire_response);
        $this->add_status($data_questionnaire_response);
        $this->add_subject($data_questionnaire_response);
        $this->add_encounter($data_questionnaire_response);
        $this->add_authored($data_questionnaire_response);
        $this->add_author($data_questionnaire_response);
        $this->add_source($data_questionnaire_response);
        $this->add_item($data_questionnaire_response);

        return json_encode($this->questionnaire_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function create_questionnaire_response(QuestionnaireResponseRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_questionnaire_response = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_questionnaire_response));
        $data = $this->json($data_questionnaire_response);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/QuestionnaireResponse';
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
                    'message' => 'Create Questionnaire Response Success',
                    'data' => [
                        'questionnaire_response_id' => $response->id,
                        'questionnaire_response_status' => $response->status
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
                'message' => 'Create Questionnaire Response Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
