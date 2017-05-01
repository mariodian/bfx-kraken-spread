<?php

require_once('config.php');
require_once('libs/Bitfinex.php');
require_once('libs/KrakenAPIClient.php');

$kraken = new \Payward\KrakenAPI($config['kraken_api_key'], $config['kraken_api_secret'], 'https://api.kraken.com', 0, FALSE);
$bfx = new Bitfinex($config['bfx_api_key'], $config['bfx_api_secret']);

$currencies = array(
  'btc' => array(
    'bfx' => array(
      'price' => 0,
      'pair' => 'btcusd'
    ),
    'kraken' => array(
      'price' => 0,
      'pair' => 'XXBTZUSD'
    )
  ),
  'ltc' => array(
    'bfx' => array(
      'price' => 0,
      'pair' => 'ltcusd'
    ),
    'kraken' => array(
      'price' => 0,
      'pair' => 'XLTCZUSD'
    )
  ),
  'eth' => array(
    'bfx' => array(
      'price' => 0,
      'pair' => 'ethusd'
    ),
    'kraken' => array(
      'price' => 0,
      'pair' => 'XETHZUSD'
    )
  ),
  'etc' => array(
    'bfx' => array(
      'price' => 0,
      'pair' => 'etcusd'
    ),
    'kraken' => array(
      'price' => 0,
      'pair' => 'XETCZUSD'
    )
  ),
  'dash' => array(
    'bfx' => array(
      'price' => 0,
      'pair' => 'dshusd'
    ),
    'kraken' => array(
      'price' => 0,
      'pair' => 'DASHUSD'
    )
  ),
  'xmr' => array(
    'bfx' => array(
      'price' => 0,
      'pair' => 'xmrusd'
    ),
    'kraken' => array(
      'price' => 0,
      'pair' => 'XXMRZUSD'
    )
  ),
  'zec' => array(
    'bfx' => array(
      'price' => 0,
      'pair' => 'zecusd'
    ),
    'kraken' => array(
      'price' => 0,
      'pair' => 'XZECZUSD'
    )
  ),
);

// Bitfinex
foreach ($currencies as $cur_key => $cur_value) {
  $ticker = $bfx->get_ticker($cur_value['bfx']['pair']);
  $currencies[$cur_key]['bfx']['price'] = $ticker['last_price'];
}

// Kraken
$comma_pairs = '';
foreach ($currencies as $currency) {
  $comma_pairs .= $currency['kraken']['pair'] . ',';
}
$comma_pairs = rtrim($comma_pairs, ',');

$result = $kraken->QueryPublic('Ticker', array('pair' => $comma_pairs));

foreach ($result as $tickers) {
  foreach ($tickers as $name => $value) {
    foreach ($currencies as $cur_key => $cur_value) {
      if ($name === $cur_value['kraken']['pair']) {
        $currencies[$cur_key]['kraken']['price'] = $value['c'][0];
      }
    }
  }
}

echo '<table border="1" cellpadding="10" cellspacing="0">';

echo "<tr>";

echo '<td>Symbol</td>';
echo '<td>Bitfinex Price</td>';
echo '<td>Kraken Price</td>';
echo '<td>Difference</td>';
echo '<td>Difference %</td>';

echo "</tr>";

foreach ($currencies as $cur_key => $cur_value) {
  $bfx_price = $cur_value['bfx']['price'];
  $kraken_price = $cur_value['kraken']['price'];
  $diff = abs($bfx_price - $kraken_price);
  $percentage = number_format(($diff / ($bfx_price > $kraken_price ? $kraken_price : $bfx_price)) * 100, 2, '.', '');

  echo "<tr>";

  // Symbol
  echo "<td>";
  echo $cur_key;
  echo "</td>";

  // BFX price
  echo "<td>";
  echo "\$$bfx_price";
  echo "</td>";

  // KRAKEN price
  echo "<td>";
  echo "\$$kraken_price";
  echo "</td>";

  // Diff
  echo "<td>";
  echo "$" . $diff;
  echo "</td>";

  // Percentage
  echo "<td>";
  echo "$percentage%";
  echo "</td>";

  echo "</tr>";
}

echo "</table>";
