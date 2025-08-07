<?php

include_once __DIR__ . '/transactions.php';
include_once __DIR__ . '/../cairnsgames/email.php';

function updateOrderStatus($orderId, $status)
{
  $sql = "UPDATE orders SET status = ? WHERE id = ?";
  executeQuery($sql, [$status, $orderId]);
}

function createTickets($orderId)
{
  $orders = executeQuery("SELECT * FROM orders WHERE id = ?", [$orderId]);
  $order = $orders[0] ?? null;

  if (!$order) {
    throw new Exception("Order not found.");
  }

  $orderItems = executeQuery("SELECT tt.name, oi.id, oi.order_id, oi.ticket_type_id, oi.quantity, oi.price, tt.event_id
    FROM order_items oi, ticket_types tt 
    WHERE oi.ticket_type_id = tt.id 
    AND order_id = ?", [$orderId]);
  if (empty($orderItems)) {
    throw new Exception("Order has no items.");
  }

  foreach ($orderItems as $item) {
    $ticketCode = sprintf(
      '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000,
      mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff)
    );
    $sql = "INSERT INTO tickets (order_item_id, user_id, quantity, event_id, ticket_type_id, ticket_code, assigned_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $ticket = executeQuery($sql, [
      $item['id'],
      $order['user_id'],
      $item['quantity'],
      $item['event_id'],
      $item['ticket_type_id'],
      $ticketCode
    ]);
    $item["ticket_id"] = $ticket['id'] ?? null;
    $event = executeQuery("SELECT * FROM events WHERE id = ?", [$item['event_id']]);
    $event = $event[0] ?? null;


    $eventData = [
      "name" => $event['title'] ?? '',
      "date" => $event['start_datetime'] ?? '',
      "location" => $event['location'] ?? ''
    ];

    $ticketsData = [
      [
        "type" => $item["name"],
        "price" => $item['price'],
        "quantity" => $item['quantity']
      ]
    ];

    $emailData = [
      "event" => $eventData,
      "tickets" => $ticketsData
    ];

    // var_dump("EMAIL DATA", $emailData);

    sendEmailByTemplate($order['user_id'], 'thank_you_for_buying_tickets', $emailData);

    $organizerEmailData = [
      "event" => $eventData,
      "tickets" => $ticketsData
    ];

    sendEmailByTemplate($event["organizer_id"], 'someone_bought_tickets', $organizerEmailData);

    // Update quantity_sold in ticket_types table
    $updateTicketTypeSql = "UPDATE ticket_types SET quantity_sold = quantity_sold + ? WHERE id = ?";
    executeQuery($updateTicketTypeSql, [$item['quantity'], $item['ticket_type_id']]);

    createTransaction(
      "e671937d-54c9-11f0-9ec0-1a220d8ac2c9",
      $item['ticket_id'],
      $order['user_id'],
      $item['event_id'],
      $item['price'],
      $order['promo_code'] ?? null,
      $item['event_id'] ?? 0,
      $event['title'] ?? null,
      $item['ticket_type_id'] ?? 0,
      $item['name']
    );
  }

  executeQuery("UPDATE order_items SET purchase_datetime = NOW() WHERE order_id = ?", [$orderId]);
}

function processOrderPayment($orderId)
{
  $order = executeQuery("SELECT * FROM orders WHERE id = ?", [$orderId]);
  if (empty($order)) {
    throw new Exception("Order not found.");
  }
  $order = $order[0];

  if ($order['status'] !== 'pending') {
    http_response_code(422);
    echo json_encode(["error" => "Unable to process: Order is not in pending status."]);
    exit;
  }

  $orderItems = executeQuery("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
  if (empty($orderItems)) {
    throw new Exception("Order has no items.");
  }

  $totalPrice = 0;
  foreach ($orderItems as $item) {
    $totalPrice += $item['price'];
  }

  // echo "Updating order status to paid";
  updateOrderStatus($orderId, 'paid');
  createTickets($orderId);
  // createTransactions($order);
}