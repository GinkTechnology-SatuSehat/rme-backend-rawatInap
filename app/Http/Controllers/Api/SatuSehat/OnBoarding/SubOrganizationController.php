<?php

namespace App\Http\Controllers\Api\SatuSehat\OnBoarding;

use App\Models\SatuSehatToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\SatuSehat\OnBoarding\SubOrganizationRequest;
use App\Models\SubOrganization;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Exceptions\HttpResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;

class SubOrganizationController extends Controller
{
    public $organization = [
        'resourceType' => 'Organization',
    ];

    public function add_id($data_sub_organization)
    {
        if (isset($data_sub_organization['id'])) {
            $this->organization['id'] = $data_sub_organization['id'];
        }
    }

    public function add_status($data_sub_organization)
    {
        if (isset($data_sub_organization['status_organization'])) {
            $this->organization['active'] = $data_sub_organization['status_organization'];
        }
    }

    public function add_identifier($data_sub_organization)
    {
        if (isset($data_sub_organization['identifier'])) {
            for ($i = 0; $i < count($data_sub_organization['identifier']); $i++) {
                $this->organization['identifier'][] = [
                    'system' => $data_sub_organization['identifier'][$i]['system'],
                    'value' => $data_sub_organization['identifier'][$i]['value'],
                ];

                if (isset($data_sub_organization['identifier'][$i]['use'])) {
                    $this->organization['identifier'][$i]['use'] = $data_sub_organization['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_type($data_sub_organization)
    {
        if (isset($data_sub_organization['type'])) {
            $this->organization['type'][] = [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/organization-type',
                        'code' => $data_sub_organization['type']['code'],
                    ]
                ]
            ];

            if (isset($data_sub_organization['type']['display'])) {
                $this->organization['type'][0]['coding'][0]['display'] = $data_sub_organization['type']['display'];
            }
        }
    }

    public function add_name($data_sub_organization)
    {
        if (isset($data_sub_organization['name'])) {
            $this->organization['name'] = $data_sub_organization['name'];
        }
    }

    public function add_telecom($data_sub_organization)
    {
        if (isset($data_sub_organization['telecom'])) {
            for ($i = 0; $i < count($data_sub_organization['telecom']); $i++) {
                $this->organization['telecom'][] = [
                    'system' => $data_sub_organization['telecom'][$i]['system'],
                    'value' => $data_sub_organization['telecom'][$i]['value'],
                ];

                if (isset($data_sub_organization['telecom'][$i]['use'])) {
                    $this->organization['telecom'][$i]['use'] = $data_sub_organization['telecom'][$i]['use'];
                }
            }
        }
    }

    public function add_address($data_sub_organization)
    {
        if (isset($data_sub_organization['address'])) {
            for ($i = 0; $i < count($data_sub_organization['address']); $i++) {
                if (isset($data_sub_organization['address'][$i]['use'])) {
                    $this->organization['address'][$i]['use'] = $data_sub_organization['address'][$i]['use'];
                }
                if (isset($data_sub_organization['address'][$i]['type'])) {
                    $this->organization['address'][$i]['type'] = $data_sub_organization['address'][$i]['type'];
                }
                if (isset($data_sub_organization['address'][$i]['line'])) {
                    $this->organization['address'][$i]['line'] = $data_sub_organization['address'][$i]['line'];
                }
                if (isset($data_sub_organization['address'][$i]['city'])) {
                    $this->organization['address'][$i]['city'] = $data_sub_organization['address'][$i]['city'];
                }
                if (isset($data_sub_organization['address'][$i]['postalCode'])) {
                    $this->organization['address'][$i]['postalCode'] = $data_sub_organization['address'][$i]['postalCode'];
                }
                if (isset($data_sub_organization['address'][$i]['country'])) {
                    $this->organization['address'][$i]['country'] = $data_sub_organization['address'][$i]['country'];
                }
                if (isset($data_sub_organization['address'][$i]['extension'])) {
                    $this->organization['address'][$i]['extension'][] = [
                        'url' => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                    ];

                    for ($j = 0; $j < count($data_sub_organization['address'][$i]['extension']['extension']); $j++) {
                        $this->organization['address'][$i]['extension'][0]['extension'][] = [
                            'url' => $data_sub_organization['address'][$i]['extension']['extension'][$j]['url'],
                            'valueCode' => $data_sub_organization['address'][$i]['extension']['extension'][$j]['valueCode'],
                        ];
                    }
                }
            }
        }
    }

    public function add_partOf($data_sub_organization)
    {
        if (isset($data_sub_organization['partOf'])) {
            $this->organization['partOf'] = [
                'reference' => 'Organization/' . $data_sub_organization['partOf']['reference_id_organization'],
            ];

            if (isset($data_sub_organization['partOf']['display'])) {
                $this->organization['partOf']['display'] = $data_sub_organization['partOf']['display'];
            }
        }
    }

    public function json($data_sub_organization)
    {
        $this->add_id($data_sub_organization);
        $this->add_status($data_sub_organization);
        $this->add_identifier($data_sub_organization);
        $this->add_type($data_sub_organization);
        $this->add_name($data_sub_organization);
        $this->add_telecom($data_sub_organization);
        $this->add_address($data_sub_organization);
        $this->add_partOf($data_sub_organization);

        return json_encode($this->organization, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function get_sub_organization(SubOrganizationRequest $request, $sub_organization_id)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $partOf = $request->query('partOf');

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];

        //SETUP REQUEST
        if (isset($partOf)) {
            $url = $satusehat_base_url . '/Organization?partOf=' . $partOf;
        } else {
            $url = $satusehat_base_url . '/Organization' . '/' . $sub_organization_id;
        }
        $request = new Request('GET', $url, $headers, $data = null);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                return response([
                    'success' => true,
                    'message' => 'Get Sub Organization Success',
                    'data' => [
                        'sub_organization_id' => $response->id,
                        'sub_organization_name' => $response->name,
                        'sub_organization_status' => $response->active
                    ]
                ], $statusCode);
            } else if (isset($response->entry)) {
                $data_organization = [];
                for ($i = 0; $i < count($response->entry); $i++) {
                    $data = $response->entry[$i]->resource;
                }
                return response([
                    'success' => true,
                    'message' => 'Get Sub Organization Success',
                    'data' => [
                        $data_organization
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
                'message' => 'Get Sub Organization Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }

    public function create_sub_organization(SubOrganizationRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_sub_organization = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_sub_organization));
        $data = $this->json($data_sub_organization);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Organization';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                SubOrganization::create([
                    'sub_organization_id' => $response->id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Sub Organization Success',
                    'data' => [
                        'sub_organization_id' => $response->id,
                        'sub_organization_name' => $response->name,
                        'sub_organization_status' => $response->active
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
                'message' => 'Create Sub Organization Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }

    public function update_sub_organization(SubOrganizationRequest $request, $sub_organization_id)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_sub_organization = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_sub_organization));
        $data = $this->json($data_sub_organization);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Organization' . '/' . $sub_organization_id;
        $request = new Request('PUT', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                SubOrganization::where('sub_organization_id', $data_sub_organization['id'])->update([
                    'sub_organization_id' => $response->id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Update Sub Organization Success',
                    'data' => [
                        'sub_organization_id' => $response->id,
                        'sub_organization_status' => $response->active
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
                'message' => 'Update Sub Organization Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }

    public function update_partial_sub_organization(SubOrganizationRequest $request, $sub_organization_id)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_location_ID');
        $data_sub_organization = $request->all();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json-patch+json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];

        $data = [];

        for ($i = 0; $i < count($data_sub_organization); $i++) {
            $data[$i]['op'] = $data_sub_organization[$i]['op'];
            $data[$i]['path'] = $data_sub_organization[$i]['path'];

            switch ($data_sub_organization[$i]['op']) {
                case 'add':
                    $data[$i]['value'] = [
                        'system' => $data_sub_organization[$i]['value']['system'],
                        'code' => $data_sub_organization[$i]['value']['code'],
                        'display' => $data_sub_organization[$i]['value']['display'],
                    ];
                    break;
                case 'replace':
                    $data[$i]['value'] = $data_sub_organization[$i]['value'];
                    break;
            }
        }

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Organization' . '/' . $sub_organization_id;
        $request = new Request('PATCH', $url, $headers, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                SubOrganization::create([
                    'sub_organization_id' => $response->id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Update Sub Organization Success',
                    'data' => [
                        'sub_organization_id' => $response->id,
                        'sub_organization_name' => $response->name,
                        'sub_organization_status' => $response->active
                    ]
                ], $statusCode);
            } else {
                return null;
            }
        } catch (ClientException $e) {
            $res = json_decode($e->getResponse()->getBody()->getContents());
            $issue_information = $res;

            throw new HttpResponseException(response([
                'success' => 'false',
                'message' => 'Update Sub Organization Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
