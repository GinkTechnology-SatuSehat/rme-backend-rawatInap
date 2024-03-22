<?php

namespace App\Http\Controllers\Api\SatuSehat\OnBoarding;

use GuzzleHttp\Client;
use App\Models\SatuSehatToken;
use App\Models\Location;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;
use App\Http\Requests\SatuSehat\OnBoarding\LocationRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LocationController extends Controller
{
    public $location = [
        'resourceType' => 'Location',
    ];

    public function add_id($data_location)
    {
        if (isset($data_location['id'])) {
            $this->location['id'] = $data_location['id'];
        }
    }

    public function add_identifier($data_location)
    {
        if (isset($data_location['identifier'])) {
            for ($i = 0; $i < count($data_location['identifier']); $i++) {
                $this->location['identifier'][] = [
                    'system' => $data_location['identifier'][$i]['system'],
                    'value' => $data_location['identifier'][$i]['value'],
                ];

                if (isset($data_location['identifier'][$i]['use'])) {
                    $this->location['identifier'][$i]['use'] = $data_location['identifier'][$i]['use'];
                }
            }
        }
    }

    public function add_status($data_location)
    {
        if (isset($data_location['status_location'])) {
            $this->location['status'] = $data_location['status_location'];
        }
    }

    public function add_name($data_location)
    {
        if (isset($data_location['name'])) {
            $this->location['name'] = $data_location['name'];
        }
    }

    public function add_description($data_location)
    {
        if (isset($data_location['description'])) {
            $this->location['description'] = $data_location['description'];
        }
    }

    public function add_mode($data_location)
    {
        if (isset($data_location['mode'])) {
            $this->location['mode'] = $data_location['mode'];
        }
    }

    public function add_telecom($data_location)
    {
        if (isset($data_location['telecom'])) {
            for ($i = 0; $i < count($data_location['telecom']); $i++) {
                $this->location['telecom'][] = [
                    'system' => $data_location['telecom'][$i]['system'],
                    'value' => $data_location['telecom'][$i]['value'],
                ];

                if (isset($data_location['telecom'][$i]['use'])) {
                    $this->location['telecom'][$i]['use'] = $data_location['telecom'][$i]['use'];
                }
            }
        }
    }

    public function add_address($data_location)
    {
        if (isset($data_location['address'])) {
            for ($i = 0; $i < count($data_location['address']); $i++) {
                if (isset($data_location['address'][$i]['use'])) {
                    $this->location['address'][$i]['use'] = $data_location['address'][$i]['use'];
                }
                if (isset($data_location['address'][$i]['type'])) {
                    $this->location['address'][$i]['type'] = $data_location['address'][$i]['type'];
                }
                if (isset($data_location['address'][$i]['line'])) {
                    $this->location['address'][$i]['line'] = $data_location['address'][$i]['line'];
                }
                if (isset($data_location['address'][$i]['city'])) {
                    $this->location['address'][$i]['city'] = $data_location['address'][$i]['city'];
                }
                if (isset($data_location['address'][$i]['postalCode'])) {
                    $this->location['address'][$i]['postalCode'] = $data_location['address'][$i]['postalCode'];
                }
                if (isset($data_location['address'][$i]['country'])) {
                    $this->location['address'][$i]['country'] = $data_location['address'][$i]['country'];
                }
                if (isset($data_location['address'][$i]['extension'])) {
                    $this->location['address'][$i]['extension'][] = [
                        'url' => "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                    ];

                    for ($j = 0; $j < count($data_location['address'][$i]['extension']['extension']); $j++) {
                        $this->location['address'][$i]['extension'][0]['extension'][] = [
                            'url' => $data_location['address'][$i]['extension']['extension'][$j]['url'],
                            'valueCode' => $data_location['address'][$i]['extension']['extension'][$j]['valueCode'],
                        ];
                    }
                }
            }
        }
    }

    public function add_physicalType($data_location)
    {
        if (isset($data_location['physicalType'])) {
            $this->location['physicalType'] = [
                'coding' => [
                    [
                        'system' => 'http://terminology.hl7.org/CodeSystem/location-physical-type',
                        'code' => $data_location['physicalType']['code'],
                    ]
                ]
            ];

            if (isset($data_location['physicalType']['display'])) {
                $this->location['physicalType']['coding'][0]['display'] = $data_location['physicalType']['display'];
            }
        }
    }

    public function add_managingOrganization($data_location)
    {
        if (isset($data_location['managingOrganization'])) {
            $this->location['managingOrganization'] = [
                'reference' => 'Organization/' . $data_location['managingOrganization']['reference_id_organization'],
            ];

            if (isset($data_location['managingOrganization']['display'])) {
                $this->location['managingOrganization']['display'] = $data_location['managingOrganization']['display'];
            }
        }
    }

    public function add_partOf($data_location)
    {
        if (isset($data_location['partOf'])) {
            $this->location['partOf'] = [
                'reference' => 'Location/' . $data_location['partOf']['reference_id_location'],
            ];

            if (isset($data_location['partOf']['display'])) {
                $this->location['partOf']['display'] = $data_location['partOf']['display'];
            }
        }
    }

    public function json($data_location)
    {
        $this->add_id($data_location);
        $this->add_identifier($data_location);
        $this->add_status($data_location);
        $this->add_name($data_location);
        $this->add_description($data_location);
        $this->add_mode($data_location);
        $this->add_telecom($data_location);
        $this->add_address($data_location);
        $this->add_physicalType($data_location);
        $this->add_managingOrganization($data_location);
        $this->add_partOf($data_location);

        return json_encode($this->location, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function get_location(LocationRequest $request, $location_id)
    {
        var_dump($location_id);
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = $request->query('organization_id');

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];

        //SETUP REQUEST
        if (isset($organization_id)) {
            $url = $satusehat_base_url . '/Location?organization=' . $organization_id;
        } else {
            $url = $satusehat_base_url . '/Location' . '/' . $location_id;
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
                    'message' => 'Get Location Success',
                    'data' => [
                        'location_id' => $response->id,
                        'location_status' => $response->status
                    ]
                ], $statusCode);
            } else if (isset($response->entry)) {
                $data_location = [];
                for ($i = 0; $i < count($response->entry); $i++) {
                    $data_location = $response->entry[$i]->resource;
                }
                return response([
                    'success' => true,
                    'message' => 'Get Location Success',
                    'data' => [
                        $data_location
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
                'message' => 'Get Location Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }

    public function create_location(LocationRequest $request)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_location_ID');
        $data_location = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_location, $organization_id));
        $data = $this->json($data_location, $organization_id);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Location';
        $request = new Request('POST', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Location::create([
                    'location_id' => $response->id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Create Location Success',
                    'data' => [
                        'location_id' => $response->id,
                        'location_name' => $response->name,
                        'location_status' => $response->status
                    ]
                ], $statusCode);
            } else {
                return null;
            }
        } catch (ClientException $e) {
            $res = json_decode($e->getResponse()->getBody()->getContents());
            $issue_information = $res;

            Log::info($e->getResponse()->getBody());
            throw new HttpResponseException(response([
                'success' => 'false',
                'message' => 'Create Location Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }

    public function update_location(LocationRequest $request, $location_id)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_ORGANIZATION_ID');
        $data_location = $request->validated();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];
        Log::info($this->json($data_location));
        $data = $this->json($data_location);

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Location' . '/' . $location_id;
        $request = new Request('PUT', $url, $headers, $data);

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Location::where('location_id', $data_location['id'])->update([
                    'location_id' => $response->id,
                    // 'location_status' => $response->status
                ]);

                return response([
                    'success' => true,
                    'message' => 'Update Location Success',
                    'data' => [
                        'location_id' => $response->id,
                        'location_status' => $response->status
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
                'message' => 'Update Location Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }

    public function update_partial_location(LocationRequest $request, $location_id)
    {
        //SETUP VARIABLE
        $satusehat_base_url = env('SATUSEHAT_BASE_URL');
        $organization_id = env('SATUSEHAT_location_ID');
        $data_location = $request->all();

        //SETUP CLIENT REQUEST
        $client = new Client();

        //CONFIG HEADER AND BODY DATA
        $headers = [
            'Content-Type' => 'application/json-patch+json',
            'Authorization' => 'Bearer ' . SatuSehatToken::orderBy('id', 'desc')->first()->token
        ];

        $data = [];

        for ($i = 0; $i < count($data_location); $i++) {
            $data[$i]['op'] = $data_location[$i]['op'];
            $data[$i]['path'] = $data_location[$i]['path'];

            switch ($data_location[$i]['op']) {
                case 'add':
                    $data[$i]['value'] = [
                        'system' => $data_location[$i]['value']['system'],
                        'code' => $data_location[$i]['value']['code'],
                        'display' => $data_location[$i]['value']['display'],
                    ];
                    break;
                case 'replace':
                    $data[$i]['value'] = $data_location[$i]['value'];
                    break;
            }
        }

        //SETUP REQUEST
        $url = $satusehat_base_url . '/Location' . '/' . $location_id;
        $request = new Request('PATCH', $url, $headers, json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        //SEND REQUEST
        try {
            $res = $client->sendAsync($request)->wait();
            $statusCode = $res->getStatusCode();
            $response = json_decode($res->getBody()->getContents());

            if (isset($response->id)) {
                Location::create([
                    'location_id' => $response->id,
                ]);

                return response([
                    'success' => true,
                    'message' => 'Update Location Success',
                    'data' => [
                        'location_id' => $response->id,
                        'location_name' => $response->name,
                        'location_status' => $response->status
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
                'message' => 'Update Location Failed',
                'errors' => [
                    'details' => [
                        $issue_information
                    ]
                ]
            ], $e->getResponse()->getStatusCode()));
        };
    }
}
