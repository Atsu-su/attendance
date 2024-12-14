<?php

namespace App\Traits;

use Carbon\Carbon;

trait DateTimeFormatTrait
{
    /**
     * 入力された時間が正しい形式かどうかを判定
     * @param string $time
     * @return boolean
     * 例）09:00（それぞれ2桁） → true
     */
    public function isValidTF($time) //TF = TimeFormat
    {
        return preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $time);
    }

    /**
     * y-m-d形式の日付をy/m/dへ変換
     * @param string $date
     * @return string
     */
    public function dateFormatConvert($date)
    {
        return date('Y/m/d', strtotime($date));
    }

    /**
     * h:m:s形式の時間をh:mへ変換
     * @param string $time
     * @return string
     */
    public function timeFormatConvert($time)
    {
        return date('H:i', strtotime($time));
    }

    /**
     * 時間の差分を取得
     * @param string $time1, $time2
     * @return string
     */
    public function diffTime($time1, $time2)
    {
        $diff = Carbon::parse($time1)->diff(Carbon::parse($time2));
        return $diff->format('%H:%I');
    }

    /**
     * 日付を日本語フォーマットへ変換
     * @param string $date
     * @return string
     * 例）2024-01-01 → 2024年01月01日
     */
    public function toJapaneseDate($date)
    {
        return Carbon::parse($date)->isoFormat('YYYY年MM月DD日');
    }

    /**
     * 1桁の時間を2桁へ変換
     * @param string $time
     * @return string
     * 例）9:9 → 09:09
     */
    public function timeToTwoDigits($time)
    {
        // 時間が1桁で入力された場合、2桁に変換する
        if (preg_match('/^(0?[0-9]|1[0-9]|2[0-3]):([0-5]?[0-9])$/', $time, $matches)) {
            return sprintf('%02d:%02d', $matches[1], $matches[2]);
        }
        return $time;
    }
}
