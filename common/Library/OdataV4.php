<?php

namespace common\Library;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;
use yii\httpclient\Request;

class Odatav4 extends Component
{
    public $baseUrl;  // Base URL for your OData service (e.g., https://your-business-central-instance/ODataV4)
    public $username; // Username for NTML authentication
    public $password; // Password for NTML authentication




    /**
     * Create an HTTP client configured to authenticate an OData v4 endpoint using NTLM and cURL transport.
     * 
     * @param string $endpointUrl The URL of the OData v4 endpoint.
     * @param string $username The NTLM username.
     * @param string $password The NTLM password.
     * @return Client The configured HTTP client instance.
     */
    public function createNtlmHttpClient($entity, $queryParams = null, $method = 'GET')
    {

        $username = $this->username;
        $password = $this->password;
        // Create HTTP client instance
        $http = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);

        // Append query parameters to the URL
        if ($queryParams) {
            $entity .= '?' . http_build_query($queryParams);
        }

        // Set request configuration
        $requestConfig = [
            'method' => $method, // Adjust method as needed (GET, POST, etc.)
            'url' => $this->baseUrl . $entity,
            'options' => [
                CURLOPT_HTTPAUTH => CURLAUTH_NTLM, // Use NTLM authentication
                CURLOPT_USERPWD => "$username:$password", // Set NTLM credentials
                CURLOPT_SSL_VERIFYPEER => false,
            ],
        ];

        // Set request options
        $http->requestConfig = [
            'method' => $requestConfig['method'],
            'url' => $requestConfig['url'],
            'options' => $requestConfig['options']
        ];



        return $http;
    }

    // Async cache: stale while revalidate

    public function AsyncNtlmHttpClient($entity, $queryParams = null, $method = 'GET')
    {
        $username = $this->username;
        $password = $this->password;

        // Append query parameters to the URL
        $url = $this->baseUrl . $entity;
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        // Create HTTP client instance
        $http = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);

        // Check if response is cached
        $cacheKey = md5($url);

        // Listen to cache invalidation event

        Yii::$app->on('invalidateCache', function ($event) use (&$cacheKey) {
            Yii::info('Handled Cache invalidation event:' . $event->data, 'cache');
            $invalidatedEntity = $event->data['entity'];
            // Check if the invalidated entity matches the cached entity the delete that cache entry
            if ($invalidatedEntity === $cacheKey) {
                Yii::$app->cache->delete($cacheKey);
            }
        });


        $cachedResponse = Yii::$app->cache->getOrSet($cacheKey, function () use ($http, $url, $username, $password, $method) {
            // Set request configuration
            $requestConfig = [
                'method' => $method,
                'url' => $url,
                'options' => [
                    CURLOPT_HTTPAUTH => CURLAUTH_NTLM, // Use NTLM authentication
                    CURLOPT_USERPWD => "$username:$password", // Set NTLM credentials
                ],
            ];

            // Set request options
            $http->requestConfig = [
                'method' => $requestConfig['method'],
                'url' => $requestConfig['url'],
                'options' => $requestConfig['options']
            ];

            try {
                // Make REST request
                $response = $http->createRequest()->send();
                $cacheKey = md5($requestConfig['url']);
                if ($response->isOk) {
                    // Retrieve response as JSON
                    $responseData = $response->getData();
                    // Cache response with a TTL of 2 min
                    Yii::$app->cache->set($cacheKey, $responseData, 300);
                    return $responseData;
                } else {
                    // Handle error
                    $errorMessage = $response->getContent();
                    // Handle error...
                    echo "Error: $errorMessage";
                    return null;
                }
            } catch (\yii\httpclient\Exception $e) {
                echo "Curl Request Error: " . $e->getMessage();
                return null;
            }
        }, 300); // Use 2-minute TTL for cache

        return (object) $cachedResponse;
    }

    // Retrieve data without caching

    public function getData($entity, $queryParams = null, $method = 'GET')
    {
        $username = $this->username;
        $password = $this->password;

        $responseData = [];

        // Append query parameters to the URL
        $url = $this->baseUrl . $entity;
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        // Create HTTP client instance
        $http = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);

        // Set request configuration
        $requestConfig = [
            'method' => $method,
            'url' => $url,
            'options' => [
                CURLOPT_HTTPAUTH => CURLAUTH_NTLM, // Use NTLM authentication
                CURLOPT_USERPWD => "$username:$password", // Set NTLM credentials
            ],
        ];

        // Set request options
        $http->requestConfig = [
            'method' => $requestConfig['method'],
            'url' => $requestConfig['url'],
            'options' => $requestConfig['options']
        ];

        try {
            // Make REST request
            $response = $http->createRequest()->send();
            if ($response->isOk) {
                // Retrieve response as JSON
                $responseData = $response->getData();
            } else {
                // Handle error
                $errorMessage = $response->getContent();
                // Handle error...
                echo "Error: $errorMessage";
                return null;
            }
        } catch (\yii\httpclient\Exception $e) {
            echo "Curl Request Error: " . $e->getMessage();
            return null;
        }

        return (object) $responseData;
    }

    // Retrieve a card

    public function getCard($entity, $key, $queryParams = null, $method = 'GET')
    {
        $username = $this->username;
        $password = $this->password;

        $responseData = [];

        // Format key for OData access — ensure it's properly quoted if needed (e.g., No='CUST001')
        $key = $this->formatODataKey($key);
        $encodedKey = urlencode($key);
        $url = $this->baseUrl . $entity . "($encodedKey)";
        // Yii::$app->utility->printrr($url);

        // Append query parameters if any (e.g., $select)
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        $http = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);

        $http->requestConfig = [
            'method' => $method,
            'url' => $url,
            'options' => [
                CURLOPT_HTTPAUTH => CURLAUTH_NTLM,
                CURLOPT_USERPWD => "$username:$password",
            ],
        ];

        try {
            $response = $http->createRequest()->send();
            if ($response->isOk) {
                $responseData = $response->getData();
            } else {
                echo "Error: " . $response->getContent();
                return null;
            }
        } catch (\yii\httpclient\Exception $e) {
            echo "Curl Request Error: " . $e->getMessage();
            return null;
        }

        return (object) $responseData;
    }

    // Update Record

    public function patchCard($entity, $key, $etag, $data)
    {
        $username = $this->username;
        $password = $this->password;

        $encodedKey = urlencode($key);
        $url = $this->baseUrl . $entity . "($encodedKey)";

        $http = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);

        $request = $http->createRequest()
            ->setMethod('PATCH')
            ->setUrl($url)
            ->addHeaders([
                'Content-Type' => 'application/json',
                'If-Match' => $etag, // Required for concurrency control
            ])
            ->setOptions([
                CURLOPT_HTTPAUTH => CURLAUTH_NTLM,
                CURLOPT_USERPWD => "$username:$password",
            ])
            ->setContent(json_encode($data));

        try {
            $response = $request->send();
            if ($response->isOk) {
                return true;
            } else {
                echo "PATCH Error: " . $response->getContent();
                return false;
            }
        } catch (\yii\httpclient\Exception $e) {
            echo "Curl Request Error: " . $e->getMessage();
            return false;
        }
    }


    // Delete a record

    public function deleteCard($entity, $key, $etag)
    {
        $username = $this->username;
        $password = $this->password;

        $encodedKey = urlencode($key);
        $url = $this->baseUrl . $entity . "($encodedKey)";

        $http = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);

        $request = $http->createRequest()
            ->setMethod('DELETE')
            ->setUrl($url)
            ->addHeaders([
                'If-Match' => $etag, // Required for safe delete
            ])
            ->setOptions([
                CURLOPT_HTTPAUTH => CURLAUTH_NTLM,
                CURLOPT_USERPWD => "$username:$password",
            ]);

        try {
            $response = $request->send();
            if ($response->isOk || $response->getStatusCode() == 204) {
                return true;
            } else {
                echo "DELETE Error: " . $response->getContent();
                return false;
            }
        } catch (\yii\httpclient\Exception $e) {
            echo "Curl Request Error: " . $e->getMessage();
            return false;
        }
    }




    public function generateOdataQuery($filter, $op = 'and')
    {
        // Pad $op value with a single space
        $paddedOp = str_pad($op, strlen($op) + 2, " ", STR_PAD_BOTH);

        $filterString = implode($paddedOp, array_filter(array_map(function ($key, $value) {
            return !empty($value) ? "$key eq '" . $value . "'" : null;
        }, array_keys($filter), $filter)));

        // define query params
        $queryParams = [
            '$filter' => $filterString,
        ];
        return $queryParams;
    }

    public function formatODataKey($key)
    {
        // Check if the key is numeric
        if (is_numeric($key)) {
            return $key;
        }

        // If already quoted, return as-is (optional logic)
        if (preg_match("/^'.*'$/", $key)) {
            return $key;
        }

        // Quote and URL-encode string key
        return "'" . rawurlencode($key) . "'";
    }
}
