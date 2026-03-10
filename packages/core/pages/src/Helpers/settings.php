<?php

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Settings\Services\SettingsService;

static $settings = null;
if (!function_exists('settings')) {
    /**
     * Get a setting value by key for the pages package.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function settings(string $key, $default = null)
    {
        return SettingsService::getDataBaseSetting($key);
    }
}
if (!function_exists('settingsUrl')) {
  /**
   * Get a setting value by key for the pages package.
   *
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  function settingsUrl(string $key, $default = null)
  {
      return MediaCenterHelper::getUrl(settings($key));
  }
}
if (!function_exists('fileurls')) {
    /**
     * Get a setting value by key for the pages package.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function fileurls( $urls)
    {
        return MediaCenterHelper::getUrls($urls);
    }
  }

  if (!function_exists('getCanonicalUrl')) {
    /**
     * Get a setting value by key for the pages package.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function getCanonicalUrl()
    {
        $currentUrl = url()->current();
        $parsed = parse_url($currentUrl);

        // Ensure host exists
        if (!isset($parsed['host'])) {
            return $currentUrl;
        }

        $host = $parsed['host'];

        // Remove www. if present at the start of the host
        if (stripos($host, 'www.') === 0) {
            $host = substr($host, 4);
        }

        // Rebuild the URL without www.
        $scheme = isset($parsed['scheme']) ? $parsed['scheme'] : 'http';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $user = isset($parsed['user']) ? $parsed['user'] : '';
        $pass = isset($parsed['pass']) ? ':' . $parsed['pass']  : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($parsed['path']) ? $parsed['path'] : '';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return "$scheme://$user$pass$host$port$path$query$fragment";
    }
  }
