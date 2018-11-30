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
            'sender'            => 'required',
            'recipients_type'   => 'required|in:all,customize,custom',
            'ages'              => 'required_if:recipients_type,customize|array',
            'occupations'       => 'required_if:recipients_type,customize|array',
            'locals'            => 'required_if:recipients_type,customize|array',
            'wards'             => 'required_if:recipients_type,customize|array',
            'scheduled'         => 'numeric|in:0,1',
            'schedule_date'     => 'required_if:scheduled,1|date',
            'schedule_time'     => 'date_format:H:i',
            // 'repitition_type'   => 'required_if:scheduled,1|in:none,daily,weekly,monthly,quarterly,yearly',
            // 'repitition_value'  => 'required_if:scheduled,1|numeric',
            'recipients'        => 'required_if:recipients_type,custom|array'
        ];
    }
}
