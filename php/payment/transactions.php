<?php

function createTransactions($order)
{
  // Fetch all order items for the given order
  $orderItemsSql = "SELECT oi.id AS order_item_id, oi.price, t.id AS ticket_id, e.organizer_id 
                          FROM order_items oi 
                          JOIN tickets t ON oi.id = t.order_item_id 
                          JOIN events e ON t.event_id = e.id 
                          WHERE oi.order_id = ?";
  $orderItems = executeQuery($orderItemsSql, [$order['id']]);

  // Create a transaction for each order item
  foreach ($orderItems as $item) {
    $transactionSql = "INSERT INTO transactions (user_id, order_id, order_item_id, ticket_id, organizer_id, transaction_type, currency, amount, payout_amount, transaction_date, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    executeQuery($transactionSql, [
      $order['user_id'],
      $order['id'],
      $item['order_item_id'],
      $item['ticket_id'],
      $item['organizer_id'],
      'ticket_purchase',
      'ZAR',
      $item['price'],
      $item['price'] * 0.85
    ]);
  }
}
