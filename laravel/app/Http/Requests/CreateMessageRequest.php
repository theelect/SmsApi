<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMessageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            
            
            'body'              => 'required',
            'sender'            => 'required|string|max:10',
            'recipients_type'    => 'required|in:all,customize,custom',
            'age_bracket'       => 'required_if:recipients_type,customize|array',
            'marital_status'    => 'required_if:recipients_type,customize|array',
            'birth_month'       => 'required_if:recipients_type,customize|array',
            'groups'            => 'required_if:recipients_type,customize|array',
            'states'            => 'required_if:recipients_type,customize|array',
            'locals'            => 'required_if:recipients_type,customize|array',
            'scheduled'         => 'numeric|in:0,1',
            'schedule_date'     => 'required_if:scheduled,1|date',
            'schedule_time'     => 'required_if:scheduled,1|date_format:H:i',
            'repitition_type'   => 'required_if:scheduled,1|in:none,daily,weekly,monthly,quarterly,yearly',
            'repitition_value'  => 'required_if:scheduled,1|numeric',
            'recipients'        => 'required_if:recipients_type,custom|array'
        ];
    }
}
