<?php

namespace Aimix\Feedback\app\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
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
        $type = $this->type;
        
        $this->redirect = url()->previous().'#' . $type;
        
        if(request()->has($type . '_email')) 
            return [$type . '_email' => 'required|email'];
        if(request()->has($type . '_phone'))
            return [$type . '_phone' => 'required'];

        return [];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        $type = $this->type;

        return [
            $type . '_text' => 'Enquiry',
            $type . '_name' => 'Name',
            $type . '_email' => 'Email',
            $type . '_phone' => 'Phone'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        $type = $this->type;

        return [
            'required' => __('forms.errors.required'),
            'email' => __('forms.errors.email')
        ];
    }
}
