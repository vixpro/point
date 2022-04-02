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
  public function __construct($approver_name, $form, $warehouse, $day_time, $created_at, $created_by, $notes, $items, $urls)
  {
    $this->approver_name = $approver_name;
    $this->form = $form;
    $this->warehouse = $warehouse;
    $this->day_time = $day_time;
    $this->created_at = $created_at;
    $this->created_by = $created_by;
    $this->notes = $notes;
    $this->items = $items;
    $this->urls = $urls;
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
