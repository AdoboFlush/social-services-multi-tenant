<?php


use App\ExchangeRate;
use App\Maintenance;
use App\Transaction;
use App\PaymentRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

if (!function_exists('parse_name')) {
	function parse_name($name_raw)
	{
		$name_data = [
			'first_name' => '',
			'last_name' => '',
			'middle_name' => '',
			'suffix' => ''
		];
		$possible_suffixes = ["JR.", "JR", "SR", "SR.", "II", "III", "IV", "V", "VI", "VII"];
		$surname_prefixes = ["DE", "DELA", "DEL", "VAN", "VON", "DI", "DA", "LA", "LE", "MC", "MAC", "ST.", "SAN", "SANTA"];

		if (strpos($name_raw, ",") !== false) {
			$name_parts = explode(",", trim($name_raw), 2);
			$last_name = trim($name_parts[0]);
			$rest = isset($name_parts[1]) ? trim($name_parts[1]) : '';
			$rest_parts = preg_split('/\s+/', $rest);

			$suffix = '';
			// Check if last part is a suffix
			if (count($rest_parts) > 1 && in_array(strtoupper($rest_parts[count($rest_parts) - 1]), $possible_suffixes)) {
				$suffix = $rest_parts[count($rest_parts) - 1];
				array_pop($rest_parts);
			}

			$first_name = '';
			$middle_name = '';
			$count = count($rest_parts);
			if ($count == 3) {
				$has_prefix = false;
				foreach ($rest_parts as $w) {
					if (in_array(strtoupper($w), $surname_prefixes)) {
						$has_prefix = true;
						break;
					}
				}
				if ($has_prefix) {
					$first_name = $rest_parts[0];
					$middle_name = $rest_parts[1] . ' ' . $rest_parts[2];
				} else {
					$first_name = $rest_parts[0] . ' ' . $rest_parts[1];
					$middle_name = $rest_parts[2];
				}
			} elseif ($count == 5) {
				$first_name = $rest_parts[0] . ' ' . $rest_parts[1] . ' ' . $rest_parts[2];
				$middle_name = $rest_parts[3] . ' ' . $rest_parts[4];
			} elseif ($count == 4) {
				$first_name = $rest_parts[0] . ' ' . $rest_parts[1];
				$middle_name = $rest_parts[2] . ' ' . $rest_parts[3];
			} elseif ($count == 2) {
				$first_name = $rest_parts[0];
				$middle_name = $rest_parts[1];
			} elseif ($count == 1) {
				$first_name = $rest_parts[0];
			}

			$name_data = [
				'first_name' => trim($first_name),
				'last_name' => trim($last_name),
				'middle_name' => trim($middle_name),
				'suffix' => trim($suffix),
			];
		}
		return $name_data;
	}
}


if (! function_exists('_lang')) {

	function _lang($string = '', $option = null)
	{

		if (session()->has('forcedLanguage')) {
			$target_lang = session('forcedLanguage');
		} elseif (Auth::check() && Auth::user()->user_type == 'admin') {
			$target_lang = "english";
		} elseif (Request::has("language")) {
			$target_lang = Request::get("language");
		} elseif (Cookie::get('language')) {
			$target_lang = Cookie::get('language');
		} elseif (Auth::check()) {
			$target_lang = Auth::user()->user_type == 'user' ? Auth::user()->user_information->language : "english";
		} else {
			$target_lang = "english";
		}
		$target_lang = ucfirst($target_lang);

		if (file_exists(resource_path() . "/language/$target_lang.php")) {
			include(resource_path() . "/language/$target_lang.php");
		} else {
			include(resource_path() . "/language/language.php");
		}

		if (!array_key_exists($string, $language)) {
			return $string;
		}

		if (is_array($option) && !empty($option)) {
			foreach ($option as $key => $value) {
				$language[$string] = str_replace("{" . $key . "}", $value, $language[$string]);
			}
		}
		return $language[$string];
	}
}

if (! function_exists('is_bulk')) {

	function is_bulk_withdrawal($withdraw)
	{
		return $withdraw->transaction->type == "bulk_withdrawal" && $withdraw->method == "withdrawal_jp";
	}
}

if (! function_exists('parseRowToCsv')) {
	function parseRowToCsv($model, Collection $columns): string
	{
		return $columns
			->map(function ($column) use ($model) {
				return '"' . Arr::get($model, $column, "") . '"';
			})
			->implode(",");
	}
}

if (! function_exists('startsWith')) {

	function startsWith($haystack, $needle)

	{

		$length = strlen($needle);

		return (substr($haystack, 0, $length) === $needle);
	}
}



if (! function_exists('is_image')) {

	function is_image($file_path)
	{

		$imageExtensions = ['jpg', 'jpeg', 'gif', 'png'];



		$explodeImage = explode('.', $file_path);

		$extension = end($explodeImage);



		if (in_array($extension, $imageExtensions)) {

			return true;
		} else {

			return false;
		}
	}
}





if (! function_exists('create_option')) {

	function create_option($table, $value, $display, $selected = "", $where = NULL)
	{

		$options = "";

		$condition = "";

		if ($where != NULL) {

			$condition .= "WHERE ";

			foreach ($where as $key => $v) {

				$condition .= $key . "'" . $v . "' ";
			}
		}



		$query = DB::select("SELECT $value, $display FROM $table $condition");

		foreach ($query as $d) {

			if ($selected != "" && $selected == $d->$value) {

				$options .= "<option value='" . $d->$value . "' selected='true'>" . ucwords($d->$display) . "</option>";
			} else {

				$options .= "<option value='" . $d->$value . "'>" . ucwords($d->$display) . "</option>";
			}
		}



		echo $options;
	}
}



if (! function_exists('get_table')) {

	function get_table($table, $where = NULL)

	{

		$condition = "";

		if ($where != NULL) {

			$condition .= "WHERE ";

			foreach ($where as $key => $v) {

				$condition .= $key . "'" . $v . "' ";
			}
		}

		$query = DB::select("SELECT * FROM $table $condition");

		return $query;
	}
}



if (! function_exists('update_option')) {

	function update_option($name, $value)

	{

		date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));



		$data = array();

		$data['value'] = $value;

		$data['updated_at'] = \Carbon\Carbon::now();

		if (\App\Setting::where('name', $name)->exists()) {

			\App\Setting::where('name', $name)->update($data);
		} else {

			$data['name'] = $name;

			$data['created_at'] = \Carbon\Carbon::now();

			\App\Setting::insert($data);
		}
	}
}





if (! function_exists('user_count')) {

	function user_count($user_type)

	{

		$count = \App\User::where("user_type", $user_type)

			->selectRaw("COUNT(id) as total")

			->first()->total;

		return $count;
	}
}



if (! function_exists('transfer_request_count')) {

	function transfer_request_count($status = 'pending')

	{

		$count = \App\Transaction::where("status", $status)

			->where('dr_cr', 'dr')

			->whereRaw("(type ='transfer' OR type ='wire_transfer' OR type = 'card_transfer' OR type = 'payment')")

			->count();

		return $count;
	}
}



if (! function_exists('deposit_request_count')) {

	function deposit_request_count($status = 'pending')

	{

		$count = \App\WireDepositRequest::where("status", $status)->count();

		return $count;
	}
}

if (! function_exists('card_deposit_request_count')) {

	function card_deposit_request_count($status = 'pending')

	{

		$count = \App\DepositCard::where("status", $status)->count();

		return $count;
	}
}

if (! function_exists('withdrawal_request_count')) {
	function withdrawal_request_count($status = 'pending')
	{
		$count = \App\Transaction::where("status", $status)->where('type', 'bulk_withdrawal')->count();
		return $count;
	}
}



if (! function_exists('referral_commission_count')) {

	function referral_commission_count()

	{

		$count =  \App\ReferralCommission::where('user_id', Auth::id())

			->where('status', 1)

			->selectRaw('count(id) as c')

			->groupBy('currency_id')

			->get();

		return $count->count();
	}
}

if (! function_exists('user_document_count')) {
	function user_document_count()
	{
		return \App\User::where("kyc_status", "unreviewed")->where('user_type', 'user')->has('documents')->count();
	}
}



if (! function_exists('status')) {

	function status($label, $badge)

	{

		return "<span class='badge badge-$badge'>$label</span>";
	}
}





if (! function_exists('get_logo')) {

	function get_logo()

	{

		$logo = get_option("logo");

		if ($logo == "") {

			return asset("images/company-logo.png");
		}

		//$v = filemtime(public_path("uploads/$logo"));

		return asset("uploads/$logo");
	}
}



if (! function_exists('profile_picture')) {

	function profile_picture($profile_picture = '')

	{

		if ($profile_picture == '') {

			$profile_picture = Auth::user()->profile_picture;
		}

		return $profile_picture != '' ? asset('uploads/profile/' . $profile_picture) : asset('images/avatar.png');
	}
}



if (! function_exists('sql_escape')) {

	function sql_escape($unsafe_str)

	{

		if (get_magic_quotes_gpc()) {

			$unsafe_str = stripslashes($unsafe_str);
		}

		return $escaped_str = str_replace("'", "", $unsafe_str);
	}
}



if (! function_exists('get_option')) {

	function get_option($name, $optional = "")

	{

		$setting = DB::table('settings')->where('name', $name)->get();

		if (! $setting->isEmpty()) {

			return $setting[0]->value;
		}

		return $optional;
	}
}





if (! function_exists('timezone_list')) {



	function timezone_list()
	{

		$zones_array = array();

		$timestamp = time();

		foreach (timezone_identifiers_list() as $key => $zone) {

			date_default_timezone_set($zone);

			$zones_array[$key]['ZONE'] = $zone;

			$zones_array[$key]['GMT'] = 'UTC/GMT ' . date('P', $timestamp);
		}

		return $zones_array;
	}
}



if (! function_exists('create_timezone_option')) {



	function create_timezone_option($old = "")
	{

		$option = "";

		$timestamp = time();

		foreach (timezone_identifiers_list() as $key => $zone) {

			date_default_timezone_set($zone);

			$selected = $old == $zone ? "selected" : "";

			$option .= '<option value="' . $zone . '"' . $selected . '>' . 'GMT ' . date('P', $timestamp) . ' ' . $zone . '</option>';
		}

		echo $option;
	}
}





if (! function_exists('get_country_list')) {

	function get_country_list($old_data = '')
	{

		if ($old_data == '') {

			echo file_get_contents(app_path() . '/Helpers/country.txt');
		} else {

			$pattern = '<option value="' . $old_data . '">';

			$replace = '<option value="' . $old_data . '" selected="selected">';

			$country_list = file_get_contents(app_path() . '/Helpers/country.txt');

			$country_list = str_replace($pattern, $replace, $country_list);

			echo $country_list;
		}
	}
}



if (! function_exists('decimalPlace')) {



	function decimalPlace($number)
	{

		return number_format((float) $number, 2);
	}
}





if (!function_exists('load_language')) {

	function load_language($active = '')
	{

		$path = resource_path() . "/language";

		$files = scandir($path);

		$options = "";



		foreach ($files as $file) {

			$name = pathinfo($file, PATHINFO_FILENAME);

			if ($name == "." || $name == "" || $name == "language") {

				continue;
			}



			$selected = "";

			if ($active == $name) {

				$selected = "selected";
			} else {

				$selected = "";
			}



			$options .= "<option value='$name' $selected>" . ucwords($name) . "</option>";
		}

		echo $options;
	}
}



if (!function_exists('get_language_list')) {

	function get_language_list()
	{

		$path = resource_path() . "/language";

		$files = scandir($path);

		$array = array();



		foreach ($files as $file) {

			$name = pathinfo($file, PATHINFO_FILENAME);

			if ($name == "." || $name == "" || $name == "language") {

				continue;
			}



			$array[] = $name;
		}

		return $array;
	}
}



if (! function_exists('new_account_number()')) {

	function new_account_number()

	{

		$prefix = get_option('account_number_prefix');

		$account_number = get_option('next_account_number');

		if ($account_number == '') {

			$account_number = get_option('next_account_number', date('Y') . '1001');

			update_option('next_account_number', $account_number);
		}

		return $prefix . $account_number;
	}
}





if (! function_exists('get_account_balance')) {



	function get_account_balance($account_id)
	{

		$result = App\Account::find($account_id);

		return is_null($result) ? 0 : $result->opening_balance;
	}
}



if (! function_exists('get_card_balance')) {



	function get_card_balance($card_id)
	{



		$result = DB::select("SELECT ((SELECT IFNULL(SUM(amount),0) FROM card_transactions WHERE dr_cr = 'cr'

	   AND card_id = $card_id AND status = 1) - (SELECT IFNULL(SUM(amount),0) FROM card_transactions

	   WHERE dr_cr = 'dr' AND card_id = $card_id AND status = 1)) as balance");

		return $result[0]->balance;
	}
}





if (! function_exists('get_unread_inbox_messages')) {

	function get_unread_inbox_messages()
	{

		$id = Auth::id();

		$messages = \App\Conversation::join('messages', 'messages.conversation_id', 'conversations.id')

			->join('users', 'users.id', 'messages.user_id')

			->whereRaw('(conversations.sender_id = ? OR conversations.receiver_id = ?)', [$id, $id])

			->where('messages.user_id', '!=', $id)

			->where('messages.is_seen', 0)

			->select('messages.*')

			->get();

		return $messages;
	}
}





if (! function_exists('generate_fee')) {

	function generate_fee($amount, $fee, $fee_type)
	{



		if ($fee_type == 'percent') {

			return ($fee / 100) * $amount;
		} else if ($fee_type == 'fixed') {

			return $fee;
		}
	}
}



if (! function_exists('generate_gift_card')) {

	function generate_gift_card($length = '16')
	{

		$code = substr(str_shuffle(str_repeat('01F23LP45FSMQ678QZ9', $length)), 0, $length);

		$code = implode("-", str_split($code, 4));



		$db = DB::select("SELECT code FROM gift_cards WHERE code = '$code'");



		if ($db) {

			generate_gift_card();
		}



		return $code;
	}
}



if (! function_exists('send_message')) {

	function send_message($receiver_id, $subject, $body, $object = null)
	{

		//Repalce Message Paremeter

		if ($object != null) {

			foreach ($object as $key => $value) {

				$src = '{' . $key . '}';

				$body = str_replace($src, $value, $body);
			}
		}



		$admin = \App\User::where('user_type', 'admin')->first();



		//Create Conversation

		$conversation              = new \App\Conversation();

		$conversation->subject     = $subject;

		$conversation->sender_id   = $admin->id;

		$conversation->receiver_id = $receiver_id;

		$conversation->status      = 1;

		$conversation->save();



		//Create Message

		$message                  = new \App\Message();

		$message->message         = $body;

		$message->user_id         = $conversation->sender_id;

		$message->conversation_id = $conversation->id;

		$message->save();
	}
}



if (! function_exists('get_next_id')) {

	function get_next_id($table)

	{

		$statement = DB::select("show table status like '$table'");

		return $statement[0]->Auto_increment;
	}
}





/** Currency Functions **/



if (! function_exists('global_currency_list')) {

	function global_currency_list($old_data = '', $serialize = false)
	{

		$currency_list = file_get_contents(app_path() . '/Helpers/currency.txt');



		if ($old_data == "") {

			echo $currency_list;
		} else {

			if ($serialize == true) {

				$old_data = unserialize($old_data);

				for ($i = 0; $i < count($old_data); $i++) {

					$pattern = '<option value="' . $old_data[$i] . '">';

					$replace = '<option value="' . $old_data[$i] . '" selected="selected">';

					$currency_list = str_replace($pattern, $replace, $currency_list);
				}

				echo $currency_list;
			} else {

				$pattern = '<option value="' . $old_data . '">';

				$replace = '<option value="' . $old_data . '" selected="selected">';

				$currency_list = str_replace($pattern, $replace, $currency_list);

				echo $currency_list;
			}
		}
	}
}



if (! function_exists('get_base_currency')) {

	function get_base_currency()

	{

		$currency = \App\Currency::where("base_currency", 1)->first();

		if (! $currency) {

			$currency = \App\Currency::all()->first();
		}

		return $currency->name;
	}
}



if (! function_exists('get_currency_list')) {

	function get_currency_list()

	{

		$currency_list = \App\Currency::where("status", 1)

			->orderBy("base_currency", "DESC")

			->get();

		return $currency_list;
	}
}



if (! function_exists('get_currency_symbol')) {

	function get_currency_symbol($currency_code)
	{

		include(app_path() . '/Helpers/currency_symbol.php');



		if (array_key_exists($currency_code, $currency_symbols)) {

			//return $currency_symbols[$currency_code];

			return html_entity_decode($currency_symbols[$currency_code], ENT_QUOTES, 'UTF-8');
		}

		return $currency_code;
	}
}



if (! function_exists('update_currency_exchange_rate')) {

	function update_currency_exchange_rate($reload = false)

	{

		if (get_option('currency_converter', 'manual') == 'manual') {

			return;
		}

		date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));



		$start  = new \Carbon\Carbon(get_option('currency_update_time', date("Y-m-d H:i:s", strtotime('-24 hours', time()))));

		$end    = \Carbon\Carbon::now();



		$last_run = $start->diffInHours($end);



		if ($last_run >= 12 || $reload == true) {

			// set API Endpoint and API key

			$endpoint = 'latest';

			$access_key = get_option('fixer_api_key');



			// Initialize CURL:

			$ch = curl_init('http://data.fixer.io/api/' . $endpoint . '?access_key=' . $access_key . '');

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



			// Store the data:

			$json = curl_exec($ch);

			curl_close($ch);



			// Decode JSON response:

			$exchangeRates = json_decode($json, true);



			if ($exchangeRates['success'] == false) {

				return false;
			}



			$base_currency =  $exchangeRates['base'];



			$currency_rates = array();



			foreach ($exchangeRates['rates'] as $currency => $rate) {

				$currency_rates[$currency] = array(

					"currency"   => $currency,

					"rate"       => $rate,

					"created_at" => date('Y-m-d H:i:s'),

					"updated_at" => date('Y-m-d H:i:s')

				);
			}



			$currency_list = \App\Currency::all();



			DB::beginTransaction();



			foreach ($currency_list as $currency) {

				$c = \App\Currency::find($currency->id);

				if (isset($currency_rates["{$currency->name}"])) {

					$c->exchange_rate = $currency_rates["{$currency->name}"]['rate'];

					$c->save();
				}
			}



			//Store Last Update time

			update_option("currency_update_time", \Carbon\Carbon::now());



			DB::commit();
		}
	}
}



if (! function_exists('convert_currency')) {

	function convert_currency($from_currency, $to_currency, $amount)
	{

		$currency1 = \App\Currency::where('name', $from_currency)->first()->exchange_rate;

		$currency2 = \App\Currency::where('name', $to_currency)->first()->exchange_rate;



		$converted_output = ($amount / $currency1) * $currency2;

		return $converted_output;
	}
}



if (! function_exists('convert_currency_2')) {

	function convert_currency_2($currency1_rate, $currency2_rate, $amount)
	{

		$currency1 = $currency1_rate;

		$currency2 = $currency2_rate;



		$converted_output = ($amount / $currency1) * $currency2;

		return $converted_output;
	}
}



if (! function_exists('account_currency')) {

	function account_currency($account_id)
	{

		$account = \App\Account::find($account_id);



		return $account->account_type->currency->name;
	}
}







if (! function_exists('card_currency')) {

	function card_currency($card_id)
	{

		$card = \App\Card::find($card_id);



		return $card->card_type->currency->name;
	}
}
if (! function_exists('generateMD5Checksum')) {
	function generateMD5Checksum($values)
	{

		$generatedCheckSum = md5($values);
		return $generatedCheckSum;
	}
}

if (! function_exists('getExchangeRateByUserCurrency')) {
	/*
    * @param string $senderCurrency
    * @param string $receiverCurrency
    *
    * This method gets exchange rate between currencies
    *
    * @return ExchangeRate
    */
	function getExchangeRateByUserCurrency(string $senderCurrency, string $receiverCurrency)
	{
		$exchange_rate = resolve(ExchangeRate::class);
		$rate = $exchange_rate->where('currency_from', $senderCurrency)
			->where('currency_to', $receiverCurrency)
			->where('active', 1)
			->first();
		return !empty($rate) ? $rate : null;
	}
}


if (! function_exists('calculateDebitCreditAmount')) {
	/*
     * @param  $request_details
     *
     *  Method to calculate amount to be debited and credited to the user
     *
     * return array
     */
	function calculateDebitCreditAmount($request_details): array
	{
		$amount = $request_details->amount;
		$sent_amount = roundCurrency($request_details->amount, $request_details->sender_currency);

		$receiveCurrency = isset($request_details->receiver_currency) ? $request_details->receiver_currency : null;
		if (is_null($receiveCurrency) && isset($request_details->beneficiary_currency)) {
			$receiveCurrency = $request_details->beneficiary_currency;
		}

		$received_amount = roundCurrency($request_details->amount, $receiveCurrency);

		if ($request_details->sender_currency != $receiveCurrency) {

			if ($request_details->internal_transfer_currency == 1) {
				$exchange_rate = getExchangeRateByUserCurrency(
					$request_details->sender_currency,
					$receiveCurrency
				);
				$amount = roundCurrency(($amount * $exchange_rate->rate_markup), $receiveCurrency);
				$received_amount = $amount;
			} else {
				$exchange_rate = getExchangeRateByUserCurrency(
					$request_details->sender_currency,
					$receiveCurrency
				);
				$amount = roundCurrency(($amount / $exchange_rate->rate_markup), $request_details->sender_currency);
				$sent_amount = $amount;
			}
		}

		return [
			'amount' => $amount,
			'sent_amount' => $sent_amount,
			'received_amount' => $received_amount
		];
	}
}

if (! function_exists('getUserAccountByUserId')) {
	/*
    * @param id $id
    *
    * @return User
    */
	function getUserAccountByUserId(int $id)
	{
		$user = resolve(\App\User::class);
		$user_data = $user->where('id', $id)
			->first();
		return !empty($user_data) ? $user_data : null;
	}
}


if (! function_exists('formatAccountName')) {
	/*
    * @param id $id
    *
    * @return User
    */
	function formatAccountName($first_name, $last_name)
	{
		return ucfirst($first_name) . " " . ucfirst($last_name);
	}
}

if (! function_exists('formatAmountWithCurrency')) {
	/*
    * @param id $id
    *
    * @return User
    */
	function formatAmountWithCurrency($amount, $currency)
	{
		return $currency . " " . $amount;
	}
}

if (! function_exists('roundCurrency')) {
	function roundCurrency($amount, $currency, $up = false, $decimal = 2)
	{
		$round_down_currency = ['JPY', 'PHP', 'MYR', 'IDR', 'THB', 'VND'];
		if (in_array($currency, $round_down_currency)) {
			$amount = ceil($amount);
		} else {
			$amount = $up ? $amount = round(ceil(round($amount * 100, 4)) / 100, $decimal, PHP_ROUND_HALF_DOWN) : round(floatval($amount), $decimal, PHP_ROUND_HALF_DOWN);
		}
		return $amount;
	}
}

if (! function_exists('buildTransactionData')) {
	/*
      * Rebuild transaction data
      *
      * @param Transaction $transactions
      * @param $request
      */
	function buildTransactionData($transactions, $request): array
	{
		$transactionArray = [];
		$withdrawalStatus = ['completed', 'applying', 'canceled'];
		//DB::connection()->enableQueryLog();
		foreach ($transactions as $transaction) {

			if (
				$transaction->status == Transaction::STATUS_COMPLETED ||
				$transaction->type == 'withdrawal' && in_array($transaction->status, $withdrawalStatus) ||
				$transaction->type == Transaction::TYPE_CARD_TOPUP && $transaction->status == Transaction::STATUS_CANCELED
			) {

				$transaction_data = new \stdClass();
				$transaction_data->id = $transaction->id;

				$currency = $transaction->currency;
				$amount = $transaction->amount;

				$transaction_data->user_account = \App\User::where('id', $transaction->user_id)->first();
				$transaction_data->created_at = $transaction->created_at;
				$transaction_data->approval_date = $transaction->approval_date;

				if ($transaction->type == 'internal_transfer') {
					if ($transaction->parent_id == null) {
						$transaction_data->user_account = Transaction::where('parent_id', $transaction->id)
							->join('users as u', 'u.id', '=', 'transactions.user_id')
							->select('u.*')->first();
					} else {
						$transaction_data->user_account = Transaction::where('transactions.id', $transaction->parent_id)
							->join('users as u', 'u.id', '=', 'transactions.user_id')
							->select('u.*')->first();
					}
				} else if ($transaction->type == 'payment_request') {
					if ($transaction->ref_id != null) {
						$transaction_data->user_account = PaymentRequest::where('payment_requests.id', $transaction->ref_id)
							->join('users as u', 'u.id', '=', 'payment_requests.payer_id')
							->select('u.*')->first();
					} else if ($transaction->parent_id != null) {
						$tempTransaction = Transaction::find($transaction->parent_id);
						$transaction_data->user_account = PaymentRequest::where('payment_requests.id', $tempTransaction->ref_id)
							->join('users as u', 'u.id', '=', 'payment_requests.user_id')
							->select('u.*')->first();
					}
				} else if (isset($transaction->wire_transfer) && $transaction->type == 'withdrawal') {
					$currency = $transaction->account->currency;
					$amount = $transaction->wire_transfer->debit_amount;
				} else if ($transaction->type == 'deposit' && !empty($transaction->deposit) && $transaction->deposit->method == 'jp_deposit') {
					$transaction_data->created_at = $transaction->created_at;
					$transaction_data->updated_at = $transaction->created_at;
				}

				$transaction_data->dr_cr = $transaction->dr_cr;
				$transaction_data->type = $transaction->type;
				$transaction_data->currency = $currency;
				$transaction_data->fee = $transaction->fee;
				$transaction_data->transaction_number = $transaction->transaction_number;
				$transaction_data->note = $transaction->note;
				$transaction_data->status = $transaction->status;
				$transaction_data->current_balance = $transaction->current_balance;
				$transaction_data->fee = $transaction->fee;
				$transaction_data->account = $transaction->account;
				$transaction_data->amount = $amount;
				if ($transaction->type == Transaction::TYPE_CARD_TOPUP && isset($transaction->card_topup)) {
					$transaction_data->card_topup = $transaction->card_topup;
				} else {
					$transaction_data->wire_transfer = !is_null($transaction->wire_transfer) ? $transaction->wire_transfer : '';
				}


				$transactionArray[] = $transaction_data;
			}
		}
		//$queries = DB::getQueryLog();
		return $transactionArray;
	}

	if (! function_exists('formatAmount')) {
		function formatAmount($amount)
		{
			return  number_format($amount, 2, '.', ',');
		}
	}

	if (! function_exists('depositAmount')) {
		function depositAmount($amount, $fee)
		{
			$sum = floatval($amount) + floatval($fee);
			return  number_format($sum, 2, '.', ',');
		}
	}
}


if (! function_exists('getUserByAccountId')) {
	/*
    * @param id $id
    *
    * @return User
    */
	function getUserByAccountId(int $accountId)
	{
		$user = \App\Account::where("accounts.id", $accountId)
			->join('users', 'users.id', '=', 'accounts.user_id')
			->first();
		return !empty($user) ? $user : null;
	}
}

if (! function_exists('isDormant')) {
	/*
    * @param id $id
    *
    * @return User
    */
	function isDormant()
	{
		$user = \Auth::user();

		return $user->is_dormant;
	}
}

if (! function_exists('isSecurityPasswordRequired')) {
	function isSecurityPasswordRequired()
	{
		if (Auth::user()->account_type == "personal") {
			return false;
		} elseif (Auth::user()->security->id && Cookie::get('confirmed') == "confirmed") {
			return false;
		} elseif (isset(Auth::user()->security->status) && !Auth::user()->security->status) {
			return false;
		} elseif (!Auth::user()->security->id) {
			return false;
		}
		return true;
	}
}

if (! function_exists('ticketTranslate')) {
	function ticketTranslate($status)
	{
		if ($status == "new" || $status == "open") {
			return _lang("Submitted");
		}
		return _lang(ucwords($status));
	}
}

if (! function_exists('newTicketsCount')) {
	function newTicketsCount()
	{
		$count =  \App\Ticket::where('status', 'new', 're-opened')->get();
		return ($count->count() >= 10) ? '9+' : $count->count();
	}
}

if (! function_exists('unseenUserTickets')) {
	function unseenUserTickets()
	{
		$count =  \App\Ticket::where('user_id', Auth::user()->id)
			->with(['conversations'])
			->get()
			->filter(function ($ticket) {
				$firstConv = $ticket->conversations->first();
				if (!$firstConv->is_seen) {
					return $ticket;
				}
			})->count();
		return ($count >= 10) ? '9+' : $count;
	}
}

if (! function_exists('unreadNotifications')) {
	function unreadNotifications()
	{
		$user = Auth::user();
		$language = ($user->user_information->language == 'English') ? 'EN' : 'JP';
		$notifs =  \App\UserNotification::where('user_id', Auth::user()->id)->where('read', 0);
		$notifs = $notifs->whereHas('notification', function ($query) use ($language) {
			return $query->where('language', $language);
		});
		$count = $notifs->get()->count();

		return ($count >= 10) ? '9+' : $count;
	}
}

if (! function_exists('toWords')) {
	function toWords($string)
	{
		return _lang(ucwords(str_replace("_", " ", $string)));
	}
}

if (!function_exists('maskFirstSixDigitsAndLastFourDigits')) {
	function maskFirstSixDigitsAndLastFourDigits($card_number)
	{
		return substr($card_number, 0, 6) . str_repeat("*", strlen($card_number) - 10) . substr($card_number, -4);
	}
}


if (!function_exists('keepOriginalValue')) {
	/**
	 * @param mixed
	 */

	function keepOriginalValue($value): string
	{
		return '"=""' . $value . '"""';
	}
}

if (!function_exists('escapeComma')) {
	/**
	 * @param mixed
	 */
	function escapeComma($value): string
	{
		return '"' . $value . '"';
	}
}

if (!function_exists('isServiceMaintenance')) {
	function isServiceMaintenance(string $module): bool
	{
		$service = Maintenance::where('slug', $module)->with(['affiliate_codes'])->first();
		if ($service->isMaintenance === Maintenance::ACTIVE) {

			$applies_to = $service->affiliate_codes->filter(function ($code) {
				return $code->applies_to;
			})->map(function ($code) {
				return $code->applies_to;
			});

			$exception = $service->affiliate_codes->filter(function ($code) {
				return $code->exception;
			})->map(function ($code) {
				return $code->exception;
			});

			$user_code = auth()->user()->affiliate_details->parent_code;

			if ($applies_to->isEmpty()) { // will apply to all
				if ($exception->isEmpty()) {
					return true;
				} else {
					if ($exception->contains($user_code)) { // except
						return false;
					} elseif ((is_null($user_code) || empty($user_code)) && $exception->contains('NO_AFFILIATES')) {
						return false;
					} else {
						return true;
					}
				}
			} else {
				if ($applies_to->contains('NO_AFFILIATES') && (is_null($user_code) || empty($user_code))) {
					return true;
				}

				if ($applies_to->contains($user_code)) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}
}

if (!function_exists('getMaintenanceContent')) {
	function getMaintenanceContent(string $module): string
	{
		$language = auth()->user()->user_information->language;

		switch ($language) {
			case 'English':
				$content = Maintenance::where('slug', $module)->firstOrFail()->content;
				break;

			case 'Japanese':
				$content = Maintenance::where('slug', $module)->firstOrFail()->jp_content;
				break;

			default:
				/**
				 *  default this to english
				 */
				$content = Maintenance::where('slug', $module)->firstOrFail()->content;
		}

		return $content ? $content : '';
	}
}
