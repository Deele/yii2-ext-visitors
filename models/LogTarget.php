<?php

namespace visitors\models;

use visitors\VisitorsModule;
use Yii;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\log\Target;
use yii\web\Request;

/**
 * The visitor stats LogTarget is used to store logs for later use in the visitor stats tool
 */
class LogTarget extends Target
{
    /**
     * @var VisitorsModule
     */
    public VisitorsModule $module;
    /**
     * @var Request
     */
    public Request $request;

    /**
     * @var string tag to identify the visitation entries
     */
    public string $tag;

    /**
     * @param VisitorsModule $module
     * @param Request $request
     * @param array $config
     */
    public function __construct(VisitorsModule $module, Request $request, array $config = [])
    {
        parent::__construct($config);
        $this->module = $module;
        $this->request = $request;
        $this->tag = uniqid('', true);
    }

    /**
     * @return VisitedUrl visited URL entity
     *
     * @noinspection UnknownInspectionInspection
     * @noinspection HttpUrlsUsage
     */
    protected function fetchVisitedUrl(): VisitedUrl
    {
        // URL without query string
        $visitedUrlData = [
            'url' => str_replace(['http://', 'https://'], '', explode('?', $this->request->absoluteUrl)[0]),
            'query_params_json' => (!empty($this->request->queryParams) ? Json::encode($this->request->queryParams) : null)
        ];
        $visitedUrl = VisitedUrl::find()->where($visitedUrlData)->one();
        if ($visitedUrl === null) {
            $statusCode = Yii::$app->response->statusCode;
            $visitedUrl = new VisitedUrl($visitedUrlData);
            $visitedUrl->status = $statusCode >= 200 && $statusCode < 400 ? VisitedUrl::STATUS_OK : VisitedUrl::STATUS_ERROR;
            $visitedUrl->save();
        }
        return $visitedUrl;
    }

    /**
     * @return UserAgent|null user agent entity
     */
    protected function fetchUserAgent(): ?UserAgent
    {
        $allHeaders = $this->request->headers;
        $attributeToHeaderMap = [
            // User-agent client hints
            'name' => 'user-agent',
            'version' => 'sec-ch-ua',
            'full_version' => 'sec-ch-ua-full-version-list',
            'arch' => 'sec-ch-ua-arch',
            'bitness' => 'sec-ch-ua-bitness',
            'mobile' => 'sec-ch-ua-mobile',
            'model' => 'sec-ch-ua-model',
            'platform' => 'sec-ch-ua-platform',
            'platform_version' => 'sec-ch-ua-platform-version',
            // User preference media features client hints
            'prefers_color_scheme' => 'sec-ch-prefers-color-scheme',
            'prefers_reduced_motion' => 'sec-ch-prefers-reduced-motion',
        ];
        // Gather user agent data
        $userAgentData = [];
        foreach ($attributeToHeaderMap as $attribute => $headerKey) {
            if ($allHeaders->has($headerKey)) {
                $headerValue = $allHeaders->get($headerKey);
                // Trim certain values
                if (in_array($attribute, ['arch', 'bitness', 'model', 'platform', 'platform_version'])) {
                    $headerValue = trim($headerValue, " \n\r\t\v\0\"");
                }
                $userAgentData[$attribute] = $headerValue;
            }
        }
        if (empty($userAgentData)) {
            return null;
        }
        $userAgent = UserAgent::find()->where($userAgentData)->one();
        if ($userAgent === null) {
            $userAgent = new UserAgent($userAgentData);
            if ($userAgent->save()) {
                return $userAgent;
            }
        }
        return $userAgent;
    }

    /**
     * @return array headers data
     */
    protected function fetchHeaders(): array
    {
        $allHeaders = $this->request->headers;
        // Include only certain headers
        $headers = [];
        foreach (
            [
                'accept',
                'accept-encoding',
                'accept-language',
                'from',
                'host',
                'referer',
                'referrer-policy',
                // Device client hints
                'device-memory',
                'width',
                'viewport-width',
                // Network client hints
                'save-data',
                'downlink',
                'ect',
                'rtt',
                'sec-gpc',
            ] as $headerName) {
            if ($allHeaders->has($headerName)) {
                $headers[$headerName] = $allHeaders->get($headerName);
            }
        }
        return $headers;
    }

    /**
     * @return array cookie data
     */
    protected function fetchCookies(): array
    {
        $allCookies = $_COOKIE;
        $cookies = [];
        // Include only certain cookies listed in adscookies.csv (https://business.safety.google/adscookies/)
        $adsCookiesPath = Yii::getAlias('@visitors') . '/adscookies.csv';
        if (file_exists($adsCookiesPath)) {
            $adsCookies = fopen($adsCookiesPath, 'rb');
            $rowNum = 0;
            while ($cookieData = fgetcsv($adsCookies, 1000)) {
                $rowNum++;
                if ($rowNum === 1) {
                    // Ignore header
                    continue;
                }
                $cookieName =  strtolower($cookieData[0]);
                if (
                    str_contains($cookieName, '<wpid>')
                    || str_contains($cookieName, '<property-id>')
                    || str_contains($cookieName, '[_<customname>]')
                ) {
                    $cookieNamePattern = str_replace(
                        ['<wpid>', '<property-id>', '[_<customname>]'],
                        ['[\w\-]+', '[\w\-]+', '[\w\-]+'],
                        $cookieName
                    );
                    foreach ($allCookies as $cookieName => $cookie) {
                        if (
                            !empty($cookie)
                            && preg_match('/^' . $cookieNamePattern . '$/i', $cookieName)
                        ) {
                            $cookies[$cookieName] = $cookie;
                            break;
                        }
                    }
                } elseif (array_key_exists($cookieName, $allCookies)) {
                    $cookie = $allCookies[$cookieName];
                    if (!empty($cookie)) {
                        $cookies[$cookieName] = $cookie;
                    }
                }
            }
        }
        return $cookies;
    }

    /**
     * @param string $ipAddress IP address to fetch GeoIP data for
     *
     * @return array GeoIP data
     *
     * @todo Replace with a more reliable GeoIP service, like MaxMind GeoIP2
     */
    protected function fetchGeoIpData(string $ipAddress): array
    {
        // Get GeoIUP extension is available
        $geoIpData = [];
        if (function_exists('geoip_country_name_by_name')) {
            $country = geoip_country_name_by_name($ipAddress);
            if (!empty($country)) {
                $geoIpData['country'] = $country;
            }
        }
        if (function_exists('geoip_org_by_name')) {
            $organization = geoip_org_by_name($ipAddress);
            if (!empty($organization)) {
                $geoIpData['organization'] = $organization;
            }
        }
        if (function_exists('geoip_region_by_name')) {
            $region = geoip_region_by_name($ipAddress);
            if (!empty($region)) {
                $geoIpData['region'] = $region;
            }
        }
        if (function_exists('geoip_isp_by_name')) {
            $isp = geoip_isp_by_name($ipAddress);
            if (!empty($isp)) {
                $geoIpData['isp'] = $isp;
            }
        }
        return $geoIpData;
    }

    public function export(): void
    {
        $visitedUrl = $this->fetchVisitedUrl();
        if ($visitedUrl->hasErrors()) {
            Yii::warning(
                sprintf(
                    "Could not store visited URL: %s",
                    VarDumper::dumpAsString($visitedUrl->errors)
                ),
                __CLASS__
            );
            return;
        }
        $visitedUrl->hits++;
        $visitedUrl->save(false, ['hits']);

        $visitationData = [
            'tag' => $this->tag,
            'visited_url_id' => $visitedUrl->id,
            'ip_address' => $this->request->userIP,
            'is_ajax' => $this->request->isAjax ? 1 : 0,
            'method' => $this->request->method,
        ];
        $geoIpData = $this->fetchGeoIpData($visitationData['ip_address']);
        if (!empty($geoIpData)) {
            $visitationData['geo_ip_json'] = Json::encode($geoIpData);
        }
        $userAgent = $this->fetchUserAgent();
        if ($userAgent !== null && !$userAgent->hasErrors()) {
            $visitationData['user_agent_id'] = $userAgent->id;
        }
        $headers = $this->fetchHeaders();
        if (!empty($headers)) {
            $visitationData['headers_json'] = Json::encode($headers);
        }
        $cookies = $this->fetchCookies();
        if (!empty($cookies)) {
            $visitationData['cookies_json'] = Json::encode($cookies);
        }
        $visitation = new Visitation($visitationData);
        if (!$visitation->save()) {
            Yii::warning(
                sprintf(
                    "Could not store visitation (%s): %s",
                    VarDumper::dumpAsString($this->tag),
                    VarDumper::dumpAsString($visitation->errors)
                ),
                __CLASS__
            );
        }
    }

    public function collect($messages, $final): void
    {
        if (!Visitation::find()->where(['tag' => $this->tag])->exists()) {
            $this->export();
        }
    }
}
