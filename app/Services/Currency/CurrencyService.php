<?php

namespace App\Services\Currency;

use App\Rate;
use App\Repositories\ExchangeRate\ExchangeRateInterface;
use Illuminate\Support\Facades\Log;
use App\Services\BaseService;
use App\Repositories\Currency\CurrencyInterface;
use Validator;
use DB;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CurrencyService extends BaseService
{

    private const DECIMAL_PLACE = 8;
    private const EXCHANGE_MARKUP = 0.02;

    protected $currencyInterface;

    public function __construct(
        CurrencyInterface $currencyInterface,
        ExchangeRateInterface $exchangeRateInterface
    ) {
        $this->currencyInterface = $currencyInterface;
        $this->exchangeRateInterface = $exchangeRateInterface;
    }

    public function retrieveAll($condition)
    {
        return $this->currencyInterface->where($condition);
    }


    public function history() : View
    {
        return view('backend.rates.history');
    }

    public function getHistory(Request $request) : JsonResponse
    {
        $request->request->add(["per_page" => 10]);
        $history = $this->exchangeRateInterface->getAll($request);
        return response()->json($history);
    }

    public function store($request)
    {
        activity()->disableLogging();
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:3|min:3'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error','message' => $validator->errors()->all()]);
            } else {
                return redirect('admin/currency/create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $currency = $this->currencyInterface->create($request->input('name'));
        activity()->enableLogging();
        activity('Currency')
            ->performedOn($currency)
            ->withProperties(['currency' => $currency->name])->log('Added Currency');

        //Update Exchange Rate
        $this->generateExchangeRates($request->input('name'));
        return back()->with('success', _lang('Saved Successfully'));
    }

    public function destroy(int $id) : RedirectResponse
    {
        activity()->disableLogging();
        $currency = $this->currencyInterface->delete($id);
        $this->exchangeRateInterface->updateStatus($currency->name, 0);
        activity()->enableLogging();
        activity('Currency')
            ->performedOn($currency)
            ->withProperties(['currency' => $currency->name])->log('Deleted Currency');
        return redirect('admin/currency')->with('success', _lang('Deleted Successfully'));
    }

    private function generateExchangeRates($currency)
    {
        $currencies = $this->currencyInterface->getAll()->pluck('name');

        $combinations = array();
        foreach ($currencies as $second) {
            $combination = array($currency, $second);
            sort($combination);
            if ($currency === $second || in_array($combination, $combinations)) {
                continue;
            }
            $combinations[] = $combination;
        }

        DB::beginTransaction();
        $this->insertExchangeRate($combinations);
        DB::commit();
    }

    private function insertExchangeRate($combinations)
    {
        foreach ($combinations as $currency) {
            $rate = $this->getRate($currency[0], $currency[1]);
            $markup = $rate * self::EXCHANGE_MARKUP;
            $markup = $rate - $markup;

            $param = array(
                "currency_from" => $currency[0],
                "currency_to" => $currency[1],
                "rate" => round($rate, self::DECIMAL_PLACE),
                "rate_markup" => round($markup, self::DECIMAL_PLACE),
                "active" => 1
            );
            $this->exchangeRateInterface->create($param);

            $rate = $this->getRate($currency[1], $currency[0]);
            $markup = $rate * self::EXCHANGE_MARKUP;
            $markup = $rate - $markup;

            $param = array(
                "currency_from" => $currency[1],
                "currency_to" => $currency[0],
                "rate" => round($rate, self::DECIMAL_PLACE),
                "rate_markup" => round($markup, self::DECIMAL_PLACE),
                "active" => 1
            );
            $this->exchangeRateInterface->create($param);
        }
    }

    private function getRate($from, $to)
    {
        $rateFrom = Rate::where('currency', $from)->value('rate');
        $rateTo = Rate::where('currency', $to)->value('rate');

        $exchangeRate = round(floatval($rateTo) / floatval($rateFrom), self::DECIMAL_PLACE);

        return $exchangeRate;
    }
}
