<?php

namespace Drupal\social_auth_dribbble\Plugin\Network;

use Drupal\Core\Url;
use Drupal\social_api\SocialApiException;
use Drupal\social_auth\Plugin\Network\NetworkBase;
use Drupal\social_auth_dribbble\Settings\DribbbleAuthSettings;
use CrewLabs\OAuth2\Client\Provider\Dribbble;

/**
 * Defines a Network Plugin for Social Auth Dribbble.
 *
 * @package Drupal\social_auth_dribbble\Plugin\Network
 *
 * @Network(
 *   id = "social_auth_dribbble",
 *   social_network = "Dribbble",
 *   type = "social_auth",
 *   handlers = {
 *     "settings": {
 *       "class": "\Drupal\social_auth_dribbble\Settings\DribbbleAuthSettings",
 *       "config_id": "social_auth_dribbble.settings"
 *     }
 *   }
 * )
 */
class DribbbleAuth extends NetworkBase implements DribbbleAuthInterface {

  /**
   * Sets the underlying SDK library.
   *
   * @return \CrewLabs\OAuth2\Client\Provider\Dribbble|false
   *   The initialized 3rd party library instance.
   *
   * @throws \Drupal\social_api\SocialApiException
   *   If the SDK library does not exist.
   */
  protected function initSdk() {
    $class_name = '\CrewLabs\OAuth2\Client\Provider\Dribbble';
    if (!class_exists($class_name)) {
      throw new SocialApiException(sprintf('The Dribbble Library for the League OAuth not found. Class: %s.', $class_name));
    }

    /* @var \Drupal\social_auth_dribbble\Settings\DribbbleAuthSettings $settings */
    $settings = $this->settings;

    if ($this->validateConfig($settings)) {
      // All these settings are mandatory.
      $league_settings = [
        'clientId' => $settings->getClientId(),
        'clientSecret' => $settings->getClientSecret(),
        'redirectUri' => Url::fromRoute('social_auth_dribbble.callback')->setAbsolute()->toString(),
      ];

      // Proxy configuration data for outward proxy.
      $proxyUrl = $this->siteSettings->get('http_client_config')['proxy']['http'];
      if ($proxyUrl) {
        $league_settings = [
          'proxy' => $proxyUrl,
        ];
      }

      return new Dribbble($league_settings);
    }

    return FALSE;
  }

  /**
   * Checks that module is configured.
   *
   * @param \Drupal\social_auth_dribbble\Settings\DribbbleAuthSettings $settings
   *   The Dribbble auth settings.
   *
   * @return bool
   *   True if module is configured.
   *   False otherwise.
   */
  protected function validateConfig(DribbbleAuthSettings $settings) {
    $client_id = $settings->getClientId();
    $client_secret = $settings->getClientSecret();
    if (!$client_id || !$client_secret) {
      $this->loggerFactory
        ->get('social_auth_dribbble')
        ->error('Define Client ID and Client Secret on module settings.');

      return FALSE;
    }

    return TRUE;
  }

}
