<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InventoryUsageApprovalNotificationMail extends Mailable
{
  use Queueable, SerializesModels;

  protected $approver_name;
  protected $form;
  protected $warehouse;
  protected $day_time;
  protected $created_at;
  protected $created_by;
  protected $notes;
  protected $items;
  protected $urls;


  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct($payload)
  {
    $this->approver_name = $payload["approver_name"];
    $this->form = $payload["form"];
    $this->warehouse = $payload["warehouse"];
    $this->day_time = $payload["day_time"];
    $this->created_at = $payload["created_at"];
    $this->created_by = $payload["created_by"];
    $this->notes = $payload["notes"];
    $this->items = $payload["items"];
    $this->urls = $payload["urls"];
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->subject(title_case("Approval Email"))
      ->view(['html' => 'emails.inventory-usages.approval'])
      ->with([
        'approver_name' => $this->approver_name,
        'form' => $this->form,
        'warehouse' => $this->warehouse,
        'day_time' => $this->day_time,
        'created_at' => $this->created_at,
        'created_by' => $this->created_by,
        'notes' => $this->notes,
        'items' => $this->items,
        'urls' => $this->urls,
      ]);
  }
}
