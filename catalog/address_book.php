<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

  use OSC\OM\HTML;
  use OSC\OM\OSCOM;

  require('includes/application_top.php');

  if (!isset($_SESSION['customer_id'])) {
    $_SESSION['navigation']->set_snapshot();
    OSCOM::redirect('login.php', '', 'SSL');
  }

  require('includes/languages/' . $_SESSION['language'] . '/address_book.php');

  $breadcrumb->add(NAVBAR_TITLE_1, OSCOM::link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, OSCOM::link('address_book.php', '', 'SSL'));

  require($oscTemplate->getFile('template_top.php'));
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<?php
  if ($messageStack->size('addressbook') > 0) {
    echo $messageStack->output('addressbook');
  }
?>

<div class="contentContainer">
  <h2><?php echo PRIMARY_ADDRESS_TITLE; ?></h2>

  <div class="contentText row">
    <div class="col-sm-8">
      <div class="alert alert-warning"><?php echo PRIMARY_ADDRESS_DESCRIPTION; ?></div>
    </div>
    <div class="col-sm-4">
      <div class="panel panel-primary">
        <div class="panel-heading"><?php echo PRIMARY_ADDRESS_TITLE; ?></div>

        <div class="panel-body">
          <?php echo tep_address_label($_SESSION['customer_id'], $_SESSION['customer_default_address_id'], true, ' ', '<br />'); ?>
        </div>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>

  <h2><?php echo ADDRESS_BOOK_TITLE; ?></h2>

  <div class="alert alert-warning"><?php echo sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES); ?></div>

  <div class="contentText row">
<?php
  $Qab = $OSCOM_Db->prepare('select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from :table_address_book where customers_id = :customers_id order by firstname, lastname');
  $Qab->bindInt(':customers_id', $_SESSION['customer_id']);
  $Qab->execute();

  while ($Qab->fetch()) {
    $format_id = tep_get_address_format_id($Qab->valueInt('country_id'));
?>
      <div class="col-sm-4">
        <div class="panel panel-<?php echo ($Qab->valueInt('address_book_id') == $_SESSION['customer_default_address_id']) ? 'primary' : 'default'; ?>">
          <div class="panel-heading"><?php echo HTML::outputProtected($Qab->value('firstname') . ' ' . $Qab->value('lastname')); ?></strong><?php if ($Qab->valueInt('address_book_id') == $_SESSION['customer_default_address_id']) echo '&nbsp;<small><i>' . PRIMARY_ADDRESS . '</i></small>'; ?></div>
          <div class="panel-body">
            <?php echo tep_address_format($format_id, $Qab->toArray(), true, ' ', '<br />'); ?>
          </div>
          <div class="panel-footer text-center"><?php echo HTML::button(SMALL_IMAGE_BUTTON_EDIT, 'fa fa-file', OSCOM::link('address_book_process.php', 'edit=' . $Qab->valueInt('address_book_id'), 'SSL')) . ' ' . HTML::button(SMALL_IMAGE_BUTTON_DELETE, 'fa fa-trash', OSCOM::link('address_book_process.php', 'delete=' . $Qab->valueInt('address_book_id'), 'SSL')); ?></div>
        </div>
      </div>
<?php
  }
?>
  </div>

  <div class="clearfix"></div>

  <div class="buttonSet row">
    <div class="col-xs-6"><?php echo HTML::button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', OSCOM::link('account.php', '', 'SSL')); ?></div>
<?php
  if (tep_count_customer_address_book_entries() < MAX_ADDRESS_BOOK_ENTRIES) {
?>
    <div class="col-xs-6 text-right"><?php echo HTML::button(IMAGE_BUTTON_ADD_ADDRESS, 'fa fa-home', OSCOM::link('address_book_process.php', '', 'SSL')); ?></div>
<?php
  }
?>
  </div>
</div>

<?php
  require($oscTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
