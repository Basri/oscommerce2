<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2013 osCommerce

  Released under the GNU General Public License
*/

  use OSC\OM\OSCOM;
  use OSC\OM\Registry;

  class securityCheck_extended_last_run {
    var $type = 'warning';

    function securityCheck_extended_last_run() {
      include(OSCOM::getConfig('dir_root') . 'includes/languages/' . $_SESSION['language'] . '/modules/security_check/extended_last_run.php');
    }

    function pass() {
      global $PHP_SELF;

      $OSCOM_Db = Registry::get('Db');

      if ( $PHP_SELF == 'security_checks.php' ) {
        if ( defined('MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME') ) {
          $OSCOM_Db->save('configuration', [
            'configuration_value' => time(),
          ], [
            'configuration_key' => 'MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME'
          ]);
        } else {
          $OSCOM_Db->save('configuration', [
            'configuration_title' => 'Security Check Extended Last Run',
            'configuration_key' => 'MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME',
            'configuration_value' => time(),
            'configuration_description' => 'The date and time the last extended security check was performed.',
            'configuration_group_id' => '6',
            'date_added' => 'now()'
          ]);
        }

        return true;
      }

      return defined('MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME') && (MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_DATETIME > strtotime('-30 days'));
    }

    function getMessage() {
      return '<a href="' . OSCOM::link('security_checks.php') . '">' . MODULE_SECURITY_CHECK_EXTENDED_LAST_RUN_OLD . '</a>';
    }
  }
?>
