<?php

include_once __DIR__ . '/tx.php';

function createTransactions($order)
{
  var_dump("Creating transactions for order", $order);
  // Fetch all order items for the given order
  $orderItemsSql = "SELECT oi.id AS order_item_id, oi.price, t.id AS ticket_id, e.organizer_id, e.id as event_id, e.title as event_name 
                          FROM order_items oi 
                          JOIN tickets t ON oi.id = t.order_item_id 
                          JOIN events e ON t.event_id = e.id 
                          WHERE oi.order_id = ?";
  $orderItems = executeQuery($orderItemsSql, [$order['id']]);

  var_dump("ORDER", $order);
  var_dump("ORDER ITEMS", $orderItems);
  foreach ($orderItems as $item) {
    createTransaction(
      "e671937d-54c9-11f0-9ec0-1a220d8ac2c9",
      $item['ticket_id'],
      $order['user_id'],
      $item['event_id'],
      $item['price'],
      $order['promo_code'] ?? null,
      $item['event_id'] ?? 0,
      $item['event_name'] ?? null,
      $item['ticket_type_id'] ?? 0,
      $item['ref_2_desc'] ?? null,
    );
  }

  // Create a transaction for each order item
  foreach ($orderItems as $item) {
    // Fetch the balance of the previous transaction
    $previousBalanceSql = "SELECT balance FROM transactions WHERE organizer_id = ? ORDER BY created_at DESC LIMIT 1";
    $previousBalanceResult = executeQuery($previousBalanceSql, [$item['organizer_id']]);
    $previousBalance = $previousBalanceResult ? $previousBalanceResult[0]['balance'] : 0;

    // Calculate the new balance
    $payoutAmount = $item['price'] * 0.85;
    $newBalance = $previousBalance + $payoutAmount;

    // Insert the new transaction
    $transactionSql = "INSERT INTO transactions (user_id, order_id, order_item_id, ticket_id, organizer_id, transaction_type, currency, amount, payout_amount, cost_amount, balance, transaction_date, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    executeQuery($transactionSql, [
      $order['user_id'],
      $order['id'],
      $item['order_item_id'],
      $item['ticket_id'],
      $item['organizer_id'],
      'ticket_purchase',
      'ZAR',
      $item['price'],
      $payoutAmount,
      $item['price'] * 0.15,
      $newBalance
    ]);
  }
}
