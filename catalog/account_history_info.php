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

  if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    OSCOM::redirect('account_history.php', '', 'SSL');
  }

  $Qcheck = $OSCOM_Db->prepare('select o.customers_id from :table_orders o, :table_orders_status s where o.orders_id = :orders_id and o.orders_status = s.orders_status_id and s.language_id = :language_id and s.public_flag = "1"');
  $Qcheck->bindInt(':orders_id', $_GET['order_id']);
  $Qcheck->bindInt(':language_id', $_SESSION['languages_id']);
  $Qcheck->execute();

  if (($Qcheck->fetch() === false) || ($Qcheck->valueInt('customers_id') != $_SESSION['customer_id'])) {
    OSCOM::redirect('account_history.php', '', 'SSL');
  }

  require('includes/languages/' . $_SESSION['language'] . '/account_history_info.php');

  $breadcrumb->add(NAVBAR_TITLE_1, OSCOM::link('account.php', '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, OSCOM::link('account_history.php', '', 'SSL'));
  $breadcrumb->add(sprintf(NAVBAR_TITLE_3, $_GET['order_id']), OSCOM::link('account_history_info.php', 'order_id=' . $_GET['order_id'], 'SSL'));

  require('includes/classes/order.php');
  $order = new order($_GET['order_id']);

  require($oscTemplate->getFile('template_top.php'));
?>

<div class="page-header">
  <h1><?php echo HEADING_TITLE; ?></h1>
</div>

<div class="contentContainer">

  <div class="contentText">

    <div class="panel panel-default">
      <div class="panel-heading"><strong><?php echo sprintf(HEADING_ORDER_NUMBER, $_GET['order_id']) . ' <span class="badge pull-right">' . $order->info['orders_status'] . '</span>'; ?></strong></div>
      <div class="panel-body">

        <table border="0" width="100%" cellspacing="0" cellpadding="2" class="table-hover order_confirmation">
<?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>
          <tr>
            <td colspan="2"><strong><?php echo HEADING_PRODUCTS; ?></strong></td>
            <td align="right"><strong><?php echo HEADING_TAX; ?></strong></td>
            <td align="right"><strong><?php echo HEADING_TOTAL; ?></strong></td>
          </tr>
<?php
  } else {
?>
          <tr>
            <td colspan="2"><strong><?php echo HEADING_PRODUCTS; ?></strong></td>
            <td align="right"><strong><?php echo HEADING_TOTAL; ?></strong></td>
          </tr>
<?php
  }

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    echo '          <tr>' . "\n" .
         '            <td align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x&nbsp;</td>' . "\n" .
         '            <td valign="top">' . $order->products[$i]['name'];

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br /><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) {
      echo '            <td valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";
    }

    echo '            <td align="right" valign="top">' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table>
        <hr>
        <table width="100%" class="pull-right">
<?php
  for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
    echo '          <tr>' . "\n" .
         '            <td align="right" width="100%">' . $order->totals[$i]['title'] . '&nbsp;</td>' . "\n" .
         '            <td align="right">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
        </table>
      </div>


      <div class="panel-footer">
        <span class="pull-right hidden-xs"><?php echo HEADING_ORDER_TOTAL . ' ' . $order->info['total']; ?></span><?php echo HEADING_ORDER_DATE . ' ' . tep_date_long($order->info['date_purchased']); ?>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>

  <div class="row">
    <?php
    if ($order->delivery != false) {
      ?>
      <div class="col-sm-4">
        <div class="panel panel-info">
          <div class="panel-heading"><?php echo '<strong>' . HEADING_DELIVERY_ADDRESS . '</strong>'; ?></div>
          <div class="panel-body">
            <?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br />'); ?>
          </div>
        </div>
      </div>
      <?php
    }
    ?>
    <div class="col-sm-4">
      <div class="panel panel-warning">
        <div class="panel-heading"><?php echo '<strong>' . HEADING_BILLING_ADDRESS . '</strong>'; ?></div>
        <div class="panel-body">
          <?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br />'); ?>
        </div>
      </div>
    </div>
    <div class="col-sm-4">
      <?php
      if ($order->info['shipping_method']) {
        ?>
        <div class="panel panel-info">
          <div class="panel-heading"><?php echo '<strong>' . HEADING_SHIPPING_METHOD . '</strong>'; ?></div>
          <div class="panel-body">
            <?php echo $order->info['shipping_method']; ?>
          </div>
        </div>
        <?php
      }
      ?>
      <div class="panel panel-warning">
        <div class="panel-heading"><?php echo '<strong>' . HEADING_PAYMENT_METHOD . '</strong>'; ?></div>
        <div class="panel-body">
          <?php echo $order->info['payment_method']; ?>
        </div>
      </div>
    </div>


  </div>

  <hr>

  <h2><?php echo HEADING_ORDER_HISTORY; ?></h2>

  <div class="clearfix"></div>

  <div class="contentText">
    <ul class="timeline">
      <?php
      $Qstatuses = $OSCOM_Db->prepare('select os.orders_status_name, osh.date_added, osh.comments from :table_orders_status os, :table_orders_status_history osh where osh.orders_id = :orders_id and osh.orders_status_id = os.orders_status_id and os.language_id = :language_id and os.public_flag = "1" order by osh.date_added');
      $Qstatuses->bindInt(':orders_id', $_GET['order_id']);
      $Qstatuses->bindInt(':language_id', $_SESSION['languages_id']);
      $Qstatuses->execute();

      while ($Qstatuses->fetch()) {
        echo '<li>';
        echo '  <div class="timeline-badge"><i class="fa fa-check-square-o"></i></div>';
        echo '  <div class="timeline-panel">';
        echo '    <div class="timeline-heading">';
        echo '      <p class="pull-right"><small class="text-muted"><i class="fa fa-clock-o"></i> ' . tep_date_short($Qstatuses->value('date_added')) . '</small></p><h2 class="timeline-title">' . $Qstatuses->value('orders_status_name') . '</h2>';
        echo '    </div>';
        echo '    <div class="timeline-body">';
        echo '      <p>' . (tep_not_null($Qstatuses->value('comments')) ? '<blockquote>' . nl2br($Qstatuses->valueProtected('comments')) . '</blockquote>' : '&nbsp;') . '</p>';
        echo '    </div>';
        echo '  </div>';
        echo '</li>';
      }
      ?>
    </ul>
  </div>

<?php
  if (DOWNLOAD_ENABLED == 'true') include('includes/modules/downloads.php');
?>

  <div class="clearfix"></div>
  <div class="buttonSet">
    <?php echo HTML::button(IMAGE_BUTTON_BACK, 'fa fa-angle-left', OSCOM::link('account_history.php', tep_get_all_get_params(array('order_id')), 'SSL')); ?>
  </div>
</div>

<?php
  require($oscTemplate->getFile('template_bottom.php'));
  require('includes/application_bottom.php');
?>
