<?php

/**
 * Sends a CURL request to create a transaction.
 *
 * @param string $appid The application ID.
 * @param int $ticketId The ticket ID.
 * @param int $userId The user ID.
 * @param int $eventId The event ID.
 * @param float $amount The transaction amount.
 * @param string|null $promoCode Optional promo code.
 * @param int $ref1Id Reference 1 ID.
 * @param string|null $ref1Desc Reference 1 description.
 * @param int $ref2Id Reference 2 ID.
 * @param string|null $ref2Desc Reference 2 description.
 * @param string $authToken The authorization token.
 * @return array The response from the API.
 */
function createTransaction($appid, $ticketId, $userId, $eventId, $amount, $promoCode = null, $ref1Id = 0, $ref1Desc = null, $ref2Id = 0, $ref2Desc = null, $authToken = null) {
    $url = "https://tx.cairnsgames.co.za/php/tx/api.php/transaction";
    $url = "http://localhost/tx/php/tx/api.php/transaction";

    $data = [
        'ticket_id' => $ticketId,
        'user_id' => $userId,
        'event_id' => $eventId,
        'amount' => $amount,
        'ref_1_id' => $ref1Id,
        'ref_1_desc' => $ref1Desc,
        'ref_2_id' => $ref2Id,
        'ref_2_desc' => $ref2Desc
    ];

    if ($promoCode !== null) {
        $data['promoCode'] = $promoCode;
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'app_id: ' . $appid,
        'Authorization: Bearer ' . $authToken
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception('CURL Error: ' . curl_error($ch));
    }

    curl_close($ch);

    var_dump("Transaction response:", $response);

    return json_decode($response, true);
}

?>
