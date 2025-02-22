<?php

namespace App\Http\Requests\Inventory\Usage;

use App\Http\Requests\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Model\Master\Item;

class UpdateRequest extends FormRequest
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
      'warehouse_id' => 'required',
      'request_approval_to' => 'required',
      'employee_id' => 'required',
      'items.*.item_id' => ValidationRule::foreignKey('items'),
      'items.*.quantity' => ValidationRule::quantity(),
      'items.*.unit' => ValidationRule::unit(),
      'items.*.converter' => ValidationRule::converter(),
      'items.*.chart_of_account_id' => 'required',
      'items.*' => [
        function ($attribute, $value, $fail) {
          $itemModel = Item::find($value["item_id"]);
          if (!$itemModel) {
            $fail($attribute . ' is not found');
          }
          if ($itemModel->require_production_number || $itemModel->require_expiry_date) {
            if (empty($value['dna']) || !$value['dna']) {
              $fail($attribute . ' required production number or expiry date');
            }
          }
        },
      ],
      'items' => ['required', 'array', 'min:1'],
    ];
  }
}
