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
     * Format monetary values
     * @param $value
     * @return float
     */
    public static function format($value) {
        return round($value, 2);
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

}