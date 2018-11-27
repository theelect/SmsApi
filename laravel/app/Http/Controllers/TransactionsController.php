<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;

class TransactionsController extends Controller
{
    public function index()
    {
    	$data['transactions'] = Transaction::where(['user_id' => _id()])->orderBy('created_at', 'desc')->get();

    	return view('transactions.index', $data);
    }
}
