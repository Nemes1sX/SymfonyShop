<?php

namespace App\Http\FormRequests;

use App\Http\FormRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StoreOrderRequest extends FormRequest
{ /**
    * Determine if the user is authorized to make this request.
    */
   public function authorize(): bool
   {
       return true;
   }

   /**
    * Get the validation rules that apply to the request.
    *
    * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
    */
   public function rules(): array
   {
       return [
           'full_name' => [new Assert\NotBlank(), new Assert\Length(['min' => 3])],
           'email' => [new Assert\NotBlank(), new Assert\Email()],
           'postcode' => [new Assert\NotBlank()],
           'address' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
           'city' => [new Assert\NotBlank(),  new Assert\Length(['min' => 4])],
           'terms' => [new Assert\IsTrue()]
       ];
   }
}