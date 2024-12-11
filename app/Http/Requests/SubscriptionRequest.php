<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(isset($this->subscription_type) && $this->subscription_type == 'product')
            $this->redirect = url()->previous().'#subscription_product';
        else
            $this->redirect = url()->previous().'#subscription';

        return [
            'subscription_email' => 'sometimes|required|email',
            'subscription_email_product' => 'sometimes|required|email',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => __('forms.errors.required'),
            'email' => __('forms.errors.email')
        ];
    }
}
