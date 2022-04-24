<?php

namespace App\Http\Requests\Inventory\Usage;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'employee_id' => 'required|integer',
      'inventory_usage_id' => 'required',
      'created_by' => 'required|integer',
      'email' => 'required|email',
      'notes' => 'string',
    ];
  }
}
