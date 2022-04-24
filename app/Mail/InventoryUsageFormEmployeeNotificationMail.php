<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InventoryUsageFormEmployeeNotificationMail extends Mailable
{
  use Queueable, SerializesModels;

  protected $payload;


  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($payload)
  {
    $this->payload = $payload;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->subject(title_case("Invoice From " . $this->payload['created_by']))
      ->view(['html' => 'emails.inventory-usages.send-to-employee'])
      ->with([
        'payload' => $this->payload,
      ]);
  }
}
