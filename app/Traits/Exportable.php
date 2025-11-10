<?php

namespace App\Traits;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait Exportable
{
    public function exportToCSV(Closure $source, Collection $columns): StreamedResponse
    {
        $data = $source();

        return response()->streamDownload(function () use ($data, $columns) {
            echo $columns->keys()->implode(",") . "\r\n";

            $data->chunk(2000, fn (Collection $transactions) => $transactions
                ->each(function ($transaction) use ($columns) {
                    echo $columns
                        ->map(function ($column) use ($transaction) {
                            if(is_callable($column)) {
                                return $column($transaction);
                            }
                            return Arr::get($transaction->toArray(), Arr::get($column, "value"))
                                ?? Arr::get($column, "default");
                        })->implode(",") . "\r\n";
                }));
        });
    }

    public function escapeComma($value): string
    {
        return '"' . $value . '"';
    }

    public function toString($value): string
    {
        return '"=""' . $value . '"""';
    }

    public function toStartCase(string $value): string
    {
        return Str::replace("_", " ", ucwords($value, "_"));
    }
}
