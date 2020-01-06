<?php

namespace Drupal\social_auth_dribbble\Settings;

/**
 * Defines an interface for Social Auth Dribbble settings.
 */
interface DribbbleAuthSettingsInterface {

  /**
   * Gets the client ID.
   *
   * @return string
   *   The client ID.
   */
  public function getClientId();

  /**
   * Gets the client secret.
   *
   * @return string
   *   The client secret.
   */
  public function getClientSecret();

}
