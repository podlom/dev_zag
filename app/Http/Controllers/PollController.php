<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PollAnswer;
use App\Models\PollIp;

class PollController extends Controller
{
    public function process(Request $request)
    {
        $answers = $request->answers;
        $product_id = $request->product_id;
        $question_id = $request->question_id;
        

        if(PollIp::where('product_id', $product_id)->where('question_id', $question_id)->where('ip', $request->ip())->first())
            return;

        foreach($answers as $answer) {
            $row = PollAnswer::where('product_id', $product_id)->where('question_id', $question_id)->where('option_id', $answer)->first();
            
            if($row)
                $row->update(['votes' => $row->votes + 1]);
            else {
                $row = new PollAnswer;
                $row->votes = 1;
                $row->product_id = $product_id;
                $row->question_id = $question_id;
                $row->option_id = $answer;
                $row->save();
            }
        }

        $ip = new PollIp;
        $ip->product_id = $product_id;
        $ip->question_id = $question_id;
        $ip->ip = $request->ip();
        $ip->save();

        $poll_answers = PollAnswer::where('product_id', $product_id)->where('question_id', $question_id)->pluck('votes', 'option_id');

        return response()->json(['poll_answers' => $poll_answers]);
    }
}
