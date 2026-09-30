<?php

declare(strict_types=1);

namespace Mlevent\Fatura;

use Mlevent\Fatura\Exceptions\ApiException;
use Mlevent\Fatura\Exceptions\BadResponseException;

class Client
{
    /**
     * @var array response
     */
    protected array $response = [];

    /**
     * @var array headers
     */
    protected static $headers = [
        'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
    ];
    
    /**
     * request
     *
     * @param string     $url
     * @param array|null $parameters
     * @param boolean    $post
     */
    public function __construct(string $url, ?array $parameters = null, bool $post = true)
    {
        try {
            $request = (new \GuzzleHttp\Client)->request($post ? 'POST' : 'GET', $url, [
                'headers' => self::$headers, 
                'form_params' => $parameters,
                'timeout' => 60,
                'connect_timeout' => 30,
            ]);
            if ($response = json_decode($request->getBody()->getContents(), true)) {
                if (is_array($response)) {
                    $this->response = $response;
                }
            }
            //if (!$this->response || (isset($this->response['data']) && !is_array($this->response['data'])) || isset($this->response['error']) || !empty($this->response['data']['hata'])) {
            if (!$this->response || isset($this->response['error']) || !empty($this->response['data']['hata'])) {
                throw new ApiException('İstek başarısız oldu.', $parameters, $this->response, $request->getStatusCode());
            }
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new BadResponseException($e->getMessage(), $parameters, null, $e->getCode());
        }
    }

    /**
     * get
     *
     * @param  string|null $element
     * @return mixed
     */
    public function get(?string $element = null): mixed
    {
        if ($element === null) {
            return $this->response;
        }
        return $this->response[$element] ?? null;
    }

    /**
     * object
     *
     * @param  string|null $element
     * @return mixed
     */
    public function object(?string $element = null): mixed
    {
        $response = json_decode(json_encode($this->response, JSON_FORCE_OBJECT), false);
        
        if ($element === null) {
            return $response;
        }
        return $response->{$element} ?? null;
    }
}