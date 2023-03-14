<?php


/**
 * Utility functions
 *
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2020, Kuza Lab
 * @package Kuzalab
 */

namespace Phelix\LoanAmortization;



final class Utils extends  Loan {


    /**
     * FlatRateInterest constructor.
     */
    public function __construct() {
    }

    /**
     * Convert different durations to yearly frequency
     *
     * @param $value
     * @param $durationType
     * @return float|int
     */
    public static function convertDurationToYears($value, $durationType) {
        if ($durationType == "years") {
            return $value;
        }
        if ($durationType == "months") {
            return $value/12;
        }
        if ($durationType == "weeks") {
            return  $value / (365/7);
        }
        if ($durationType == 'days') {
            return $value / 365;
        }
        return 0;
    }

    /**
     * Convert different durations to monthly frequency
     *
     * @param $value
     * @param $durationType
     * @return float|int
     */
    public static function convertDurationToMonths($value, $durationType) {
        if ($durationType == "years") {
            return $value * 12;
        }
        if ($durationType == "months") {
            return $value;
        }
        if ($durationType == "weeks") {
            return  $value / (30/7);
        }
        if ($durationType == 'days') {
            return $value / 30;
        }
        return 0;
    }

    /**
     * Convert different durations to weekly frequency
     *
     * @param $value
     * @param $durationType
     * @return float|int
     */
    public static function convertDurationToWeeks($value, $durationType) {
        if ($durationType == "years") {
            return $value * (365/7);
        }
        if ($durationType == "months") {
            return $value * (30/7);
        }
        if ($durationType == "weeks") {
            return  $value;
        }
        if ($durationType == 'days') {
            return $value / 7;
        }
        return 0;
    }

    /**
     * Convert different durations to daily frequency
     *
     * @param $value
     * @param $durationType
     * @return float|int
     */
    public static function convertDurationToDays($value, $durationType) {
        if ($durationType == "years") {
            return $value * 365;
        }
        if ($durationType == "months") {
            return $value * 30;
        }
        if ($durationType == "weeks") {
            return  $value * 7;
        }
        if ($durationType == 'days') {
            return $value;
        }
        return 0;
    }

    /**
     * Get yearly interest rate
     *
     * @param $value
     * @param $frequencyType
     * @param $repaymentFrequency
     * @return float|int
     */
    public static function getYearlyInterestRate($value, $frequencyType, $repaymentFrequency) {
        if ($frequencyType == "yearly") {
            return $value / $repaymentFrequency;
        }
        if ($frequencyType == "monthly") {
            return ($value * 12) / $repaymentFrequency;
        }
        if ($frequencyType == "weekly") {
            return  ($value * (365/7)) / $repaymentFrequency;
        }
        if ($frequencyType == 'daily') {
            return ($value * 365) / $repaymentFrequency;
        }
        return 0;
    }

    /**
     * Get monthly interest rates
     *
     * @param $value
     * @param $frequencyType
     * @param $repaymentFrequency
     * @return float|int
     */
    public static function getMonthlyInterestRate($value, $frequencyType, $repaymentFrequency) {
        if ($frequencyType == "yearly") {
            return ($value / 12) / $repaymentFrequency;
        }
        if ($frequencyType == "monthly") {
            return $value / $repaymentFrequency;
        }
        if ($frequencyType == "weekly") {
            return  ($value * (30/7)) / $repaymentFrequency;
        }
        if ($frequencyType == 'daily') {
            return ($value * 30) / $repaymentFrequency;
        }
        return 0;
    }

    /**
     * Get weekly interest rates
     *
     * @param $value
     * @param $frequencyType
     * @param $repaymentFrequency
     * @return float|int
     */
    public static function getWeeklyInterestRate($value, $frequencyType, $repaymentFrequency) {
        if ($frequencyType == "yearly") {
            return ($value / (365/7)) / $repaymentFrequency;
        }
        if ($frequencyType == "monthly") {
            return ($value / (30/7)) / $repaymentFrequency;
        }
        if ($frequencyType == "weekly") {
            return  $value / $repaymentFrequency;
        }
        if ($frequencyType == 'daily') {
            return ($value * 7) / $repaymentFrequency;
        }
        return 0;
    }

    /**
     * Get daily interest rates
     *
     * @param $value
     * @param $frequencyType
     * @param $repaymentFrequency
     * @return float|int
     */
    public static function getDailyInterestRate($value, $frequencyType, $repaymentFrequency) {
        if ($frequencyType == "yearly") {
            return ($value / 365) / $repaymentFrequency;
        }
        if ($frequencyType == "monthly") {
            return ($value / 30) / $repaymentFrequency;
        }
        if ($frequencyType == "weekly") {
            return  ($value / 7) / $repaymentFrequency;
        }
        if ($frequencyType == 'daily') {
            return $value / $repaymentFrequency;
        }
        return 0;
    }

    /**
     * @param $value
     * @param $frequencyType
     * @param $loanDuration
     * @param $loanDurationFrequencyType
     * @return float|int
     */
    public static function getTotalInterestRate($value, $frequencyType, $loanDuration, $loanDurationFrequencyType) {
        if ($frequencyType == "yearly") {
            $durationInYears = ceil(self::convertDurationToYears($loanDuration, $loanDurationFrequencyType));
            return $value * $durationInYears;
        }
        if ($frequencyType == "monthly") {
            $durationInMonths = ceil(self::convertDurationToMonths($loanDuration, $loanDurationFrequencyType));
            return $value * $durationInMonths;
        }
        if ($frequencyType == "weekly") {
            $durationInWeeks = ceil(self::convertDurationToWeeks($loanDuration, $loanDurationFrequencyType));
            return $value * $durationInWeeks;
        }
        if ($frequencyType == 'daily') {
            $durationInDays = ceil(self::convertDurationToDays($loanDuration, $loanDurationFrequencyType));
            return $value * $durationInDays;
        }
        return 0;
    }

    /**
     * Format monetary values
     * @param $value
     * @return float
     */
    public static function format($value, $ceil=false) {
        if ($ceil === true) {
            return ceil($value);
        } else {
            return round($value,2);
        }
    }

    /**
     * Calculate the amortized loan discounting factor
     *
     * @param $interestRate
     * @param $numberOfInstallments
     * @return float|int
     */
    public static function discountingFactor($interestRate, $numberOfInstallments) {

        $interestRate = $interestRate/100;

        return (pow((1+$interestRate), $numberOfInstallments) - 1) / ($interestRate * pow((1 + $interestRate), $numberOfInstallments));
    }

    /**
     * Compute date additions
     * @param $date
     * @param $days
     * @return string
     * @throws \Exception
     */
    public static function addDaysToDate($date, $days) {

        $date = new \DateTime($date);

        $date->add(new \DateInterval("P{$days}D")); // P1D means a period of 1 day

        return $date->format('Y-m-d');
    }

    public static function formatDate($date, $format) {
        $date = new \DateTime($date);
        return $date->format($format);
    }

    /**
     * @param $dayOfTheWeek
     * @param $count
     * @param $skipEvery
     * @param $startDate
     * @return array
     * @throws \Exception
     */
    public static function getDatesForWeekDays($dayOfTheWeek, $count, $skipEvery=1, $startDate="") {

        $skipEvery -= 1;

        if (!empty($startDate)) {
            $date = new \DateTime($startDate);
        } else {
            // Create a new DateTime object
            $date = new \DateTime();
            // Modify the date it contains
            $date->modify("next $dayOfTheWeek");
        }

        $allDates = [$date->format('Y-m-d')];

        // skip, if set
        if ($skipEvery > 0) {
            for ($skip = 0; $skip < $skipEvery; $skip++) {
                $date->modify("next $dayOfTheWeek");
            }
            // We cater for the skips.
            $count = $count * $skipEvery;
        }

        for ($i=1; $i < $count; $i++) {
            $allDates[] = $date->modify("next $dayOfTheWeek")->format("Y-m-d");
            for ($skip = 0; $skip < $skipEvery; $skip++) {
                $date->modify("next $dayOfTheWeek");
            }
        }
        return $allDates;
    }

    /**
     * @param $monthDateNumber
     * @param $count
     * @param $skipEvery
     * @param $startDate
     * @return array
     * @throws \Exception
     */
    public static function getDatesForMonthDates($monthDateNumber, $count, $skipEvery=1, $startDate="") {

        $interval = $monthDateNumber - 1;
        $skipEvery -= 1;

        if (!empty($startDate)) {
            $date = new \DateTime($startDate);
        } else {
            // Create a new DateTime object
            $date = new \DateTime();
            // Modify the date it contains
            $date->modify("first day of next month")->add(new \DateInterval("P{$interval}D"));
        }

        $allDates = [$date->format('Y-m-d')];

        // skip, if set
        if ($skipEvery > 0) {
            for ($skip = 0; $skip < $skipEvery; $skip++) {
                $date->modify("first day of next month")->add(new \DateInterval("P{$interval}D"));
            }
        }

        for ($i=1; $i < $count; $i++) {

            $allDates[] = $date->modify("first day of next month")->add(new \DateInterval("P{$interval}D"))->format("Y-m-d");

            // skip, if set
            if ($skipEvery > 0) {
                for ($skip = 0; $skip < $skipEvery; $skip++) {
                    $date->modify("first day of next month")->add(new \DateInterval("P{$interval}D"));
                }
            }
        }
        return $allDates;
    }

    /**
     * @param $monthDate
     * @param $count
     * @param $startDate
     * @return array
     * @throws \Exception
     */
    public static function getDatesForYearDates($monthDate, $count, $startDate="") {

        if (!empty($startDate)) {
            $date = new \DateTime($startDate);
            $year = intval($date->format('Y'));
            $date = new \DateTime("$year-$monthDate");
        } else {
            // Create a new DateTime object
            $date = new \DateTime();

            // Modify the date it contains
            $year = 1 + intval($date->format('Y'));

            $date = new \DateTime("$year-$monthDate");
        }

        $allDates = [$date->format('Y-m-d')];

        for ($i=1; $i < $count; $i++) {

            // Modify the date it contains
            $year = 1+ intval($date->format('Y'));

            $date = new \DateTime("$year-$monthDate");

            $allDates[] = $date->format("Y-m-d");
        }
        return $allDates;
    }

}
