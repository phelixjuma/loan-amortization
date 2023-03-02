<?php


/**
 * Schedule generator
 *
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2020, Kuza Lab
 * @package Kuzalab
 */

namespace Phelix\LoanAmortization;


use SebastianBergmann\CodeCoverage\Util;

final class ScheduleGenerator extends  Loan {


    public $harmonized_interest_rate;
    public $harmonized_duration;

    /**
     * FlatRateInterest constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Set loan interest rates and loan duration harmonized to the amortization frequency
     * For instance, if loan amortization is weekly, then we convert the interest rate to a weekly interest rate value
     * and the loan duration to weeks
     * @return $this
     */
    private function harmonizeParameters() {

        switch ($this->repayment_frequency_type) {
            // We set the harmonized interest rates and loan duration
            case 'days':
                $this->harmonized_interest_rate = Utils::getDailyInterestRate($this->interest_rate_per_period, $this->interest_rate_frequency_type, $this->repayment_frequency);
                $this->harmonized_duration = Utils::convertDurationToDays($this->loan_term_duration, $this->loan_term_duration_type);
                break;
            case 'weeks':
                $this->harmonized_interest_rate = Utils::getWeeklyInterestRate($this->interest_rate_per_period, $this->interest_rate_frequency_type, $this->repayment_frequency);
                $this->harmonized_duration = Utils::convertDurationToWeeks($this->loan_term_duration, $this->loan_term_duration_type);
                break;
            case 'months':
                $this->harmonized_interest_rate = Utils::getMonthlyInterestRate($this->interest_rate_per_period, $this->interest_rate_frequency_type, $this->repayment_frequency);
                $this->harmonized_duration = Utils::convertDurationToMonths($this->loan_term_duration, $this->loan_term_duration_type);
                break;
            case 'years':
                $this->harmonized_interest_rate = Utils::getYearlyInterestRate($this->interest_rate_per_period, $this->interest_rate_frequency_type, $this->repayment_frequency);
                $this->harmonized_duration = Utils::convertDurationToYears($this->loan_term_duration, $this->loan_term_duration_type);
                break;
        }

        // Calculate the number of installments
        $this->no_installments = $this->repayment_frequency * $this->harmonized_duration;

        return $this;
    }

    /**
     * Calculate the total interest payable.
     * Total interest is calculated using the simple interest formula: P*r*t
     *
     * @return $this
     */
    private function calculateFlatRateInterest() {

        // calculate the interest
        if ($this->interest_type == self::FLAT_INTEREST) {
            $this->interest =  Utils::format($this->principal * ($this->harmonized_interest_rate/100) * $this->no_installments);
        } elseif($this->interest_type == self::ABSOLUTE_INTEREST) {
            $this->interest = $this->absolute_interest_amount;
        }
        // get the total loan amount
        $this->amount = Utils::format($this->interest + $this->principal);
        // Get the effective interest rate
        $this->effective_interest_rate = Utils::format(($this->interest / $this->principal) * 100);

        return $this;
    }

    /**
     * Set repayment schedule based on the interest frequency type
     * @return $this
     */
    private function setFlatRateInterestSchedule() {

        $this->calculateFlatRateInterest();

        $total_installments = ceil($this->no_installments);

        $totalPrincipalRepayment = 0;
        $totalInterestRepayment = 0;
        $balance = $this->principal;
        $totalPeriodLength = 0;
        $totalGracedInterest = 0;

        // Check grace on interest amount
        $graceOnInterestCharged = $this->graceOnInterestCharged($this->harmonized_duration);

        for ($i = 1; $i <= $total_installments; $i++) {

            if ($i != $total_installments ) {

                $interest_repayment = Utils::format($this->interest * ((1/$this->repayment_frequency)/$this->harmonized_duration));
                $principal_repayment = Utils::format($this->principal * ((1/$this->repayment_frequency)/$this->harmonized_duration));

                // Apply grace on principal repayments
                if ($this->grace_on_principal_repayment !== null && $this->grace_on_principal_repayment <= ($total_installments/2)) {
                    if ($i <= $this->grace_on_principal_repayment) {
                        $principal_repayment = 0;
                    } else {
                        $principal_repayment = Utils::format( ((1/$this->repayment_frequency)/($this->harmonized_duration - ($this->grace_on_principal_repayment*(1/$this->repayment_frequency)))) * $this->principal);
                    }
                }
                $total_amount_repayment = Utils::format($principal_repayment + $interest_repayment);
                $periodLength = 1/$this->repayment_frequency;

            } else {
                $principal_repayment = $this->principal - $totalPrincipalRepayment;
                $interest_repayment = ($this->interest - $totalGracedInterest) - $totalInterestRepayment;
                $total_amount_repayment = $principal_repayment + $interest_repayment;
                $periodLength = $this->harmonized_duration - $totalPeriodLength;
            }
            // Get the total period length
            $totalPeriodLength += $periodLength;

            // We apply grace on interest amount
            if ($graceOnInterestCharged['qualifies'] === true) {

                if( $totalPeriodLength <= $graceOnInterestCharged['grace_on_interest_charged'] ) {
                    // subtract from total interest payment
                    //$this->interest -= $interest_repayment;
                    // Add the grace amount to total graced interest
                    $totalGracedInterest += $interest_repayment;
                    // we reduce the total payment
                    $total_amount_repayment -= $interest_repayment;
                    // set the interest repayment to zero
                    $interest_repayment = 0;
                }
            }

            $totalPrincipalRepayment += $principal_repayment;
            $totalInterestRepayment += $interest_repayment;
            $balance -= $principal_repayment;

            $this->amortization_schedule[] = [
                "period_length"         => $periodLength,
                "principal_repayment" => $principal_repayment,
                "interest_repayment"  => $interest_repayment,
                "total_amount_repayment"  => $total_amount_repayment,
                "principal_repayment_balance"   => Utils::format($balance),
            ];
        }

        // We get the net interest
        $this->interest -= $totalGracedInterest;

        // We set total amount
        $this->amount = $this->interest + $totalPrincipalRepayment;

        return $this;
    }

    /**
     * Calculate even principal repayment for a reducing balance interest rate
     * The interest is calculated on a reducing balance
     * Principal repayments stay constant over time
     * @return $this
     */
    private function setReducingBalanceEvenPrincipalRepaymentSchedule() {

        $total_installments = ceil($this->no_installments);

        $totalPrincipalRepayment = 0;
        $totalInterestRepayment = 0;
        $balance = $this->principal;
        $totalPeriodLength = 0;

        // Check grace on interest
        $graceOnInterestCharged = $this->graceOnInterestCharged($this->harmonized_duration);

        for ($i = 1; $i <= $total_installments; $i++) {

            $interest_repayment = Utils::format(($this->harmonized_interest_rate/100) * $balance);
            if ($i != $total_installments) {

                $principal_repayment = Utils::format( ((1/$this->repayment_frequency) / $this->harmonized_duration) * $this->principal);

                // Apply grace on principal repayments
                if ($this->grace_on_principal_repayment !== null && $this->grace_on_principal_repayment <= ($total_installments/2)) {
                    if ($i <= $this->grace_on_principal_repayment) {
                        $principal_repayment = 0;
                    } else {
                        $principal_repayment = Utils::format( ((1/$this->repayment_frequency) / ($this->harmonized_duration - ($this->grace_on_principal_repayment*(1/$this->repayment_frequency)))) * $this->principal);
                    }
                }

                $total_amount_repayment = Utils::format($principal_repayment + $interest_repayment);
                $periodLength = 1/$this->repayment_frequency;

            } else {
                $principal_repayment = $this->principal - $totalPrincipalRepayment;
                $total_amount_repayment = $principal_repayment + $interest_repayment;
                $periodLength = $this->harmonized_duration - $totalPeriodLength;
            }
            // Get the total period length
            $totalPeriodLength += $periodLength;

            // Apply grace on interest
            if ($graceOnInterestCharged['qualifies'] === true) {

                if( $totalPeriodLength <= $graceOnInterestCharged['grace_on_interest_charged'] ) {
                    // we reduce the total payment
                    $total_amount_repayment -= $interest_repayment;
                    // set the interest repayment to zero
                    $interest_repayment = 0;
                }
            }

            $totalPrincipalRepayment += $principal_repayment;
            $totalInterestRepayment += $interest_repayment;
            $balance -= $principal_repayment;

            $this->amortization_schedule[] = [
                "period_length"         => $periodLength,
                "principal_repayment" => $principal_repayment,
                "interest_repayment"  => $interest_repayment,
                "total_amount_repayment"  => $total_amount_repayment,
                "principal_repayment_balance"   => Utils::format($balance),
            ];
        }

        // We set the total interest values
        // calculate the interest
        $this->interest = $totalInterestRepayment;
        // get the total loan amount
        $this->amount = $totalInterestRepayment + $totalPrincipalRepayment;
        // Get the effective interest rate
        $this->effective_interest_rate = Utils::format(($this->interest / $this->principal) * 100);

        return $this;
    }

    /**
     * Calculate even installment repayment for a reducing balance interest rate
     * The interest is calculated on a reducing balance
     * Sum of interest and principal remains constant over the repayment period
     * @return $this
     */
    private function setReducingBalanceEvenInstallmentRepaymentSchedule() {

        $total_installments = ceil($this->no_installments);

        $totalPrincipalRepayment = 0;
        $totalInterestRepayment = 0;
        $sumOfAmountRepayments = 0;
        $balance = $this->principal;
        $totalPeriodLength = 0;

        // Check grace on interest
        $graceOnInterestCharged = $this->graceOnInterestCharged($this->harmonized_duration);


        for ($i = 1; $i <= $total_installments; $i++) {

            // calculate the total amount to be repaid in each period
            $total_amount_repayment = Utils::format($this->principal / Utils::discountingFactor($this->harmonized_interest_rate, $total_installments));

            // Calculate interest repayment
            $interest_repayment = Utils::format(($this->harmonized_interest_rate/100) * $balance);

            if ($i != $total_installments) {

                $principal_repayment = $total_amount_repayment - $interest_repayment;

                // Apply grace on principal repayments
                if ($this->grace_on_principal_repayment !== null && $this->grace_on_principal_repayment <= ($total_installments/2)) {
                    if ($i <= $this->grace_on_principal_repayment) {
                        $total_amount_repayment -= $principal_repayment;
                        $principal_repayment = 0;
                    } else {
                        $total_amount_repayment = Utils::format($this->principal / Utils::discountingFactor($this->harmonized_interest_rate, ($total_installments - $this->grace_on_principal_repayment)));
                    }
                }

                $periodLength = 1/$this->repayment_frequency;
            } else {
                $principal_repayment = $this->principal - $totalPrincipalRepayment;
                $total_amount_repayment =  $principal_repayment + $interest_repayment;
                $periodLength = $this->harmonized_duration - $totalPeriodLength;
            }
            // Get the total period length
            $totalPeriodLength += $periodLength;

            // Apply grace on interest
            if ($graceOnInterestCharged['qualifies'] === true) {

                if( $totalPeriodLength <= $graceOnInterestCharged['grace_on_interest_charged'] ) {
                    // set the interest repayment to zero
                    $total_amount_repayment -= $interest_repayment;
                    $interest_repayment = 0;
                }
            }

            $totalPrincipalRepayment += $principal_repayment;
            $totalInterestRepayment += $interest_repayment;
            $sumOfAmountRepayments += $total_amount_repayment;
            $balance -= $principal_repayment;

            $this->amortization_schedule[] = [
                "period_length"         => $periodLength,
                "principal_repayment" => $principal_repayment,
                "interest_repayment"  => $interest_repayment,
                "total_amount_repayment"  => $total_amount_repayment,
                "principal_repayment_balance"   => Utils::format($balance),
            ];
        }

        // We set the total interest values
        // calculate the interest
        $this->interest = $totalInterestRepayment;
        // get the total loan amount
        $this->amount = $totalInterestRepayment + $totalPrincipalRepayment;
        // Get the effective interest rate
        $this->effective_interest_rate = Utils::format(($this->interest / $this->principal) * 100);

        return $this;
    }

    /**
     * Get details on grace on interest
     * @param $loanDuration
     * @return array
     */
    private function graceOnInterestCharged($loanDuration) {

        $response = [
            'qualifies'                 => false,
            'grace_on_interest_charged' => $this->grace_on_interest_charged
        ];

        if ($this->grace_on_interest_charged !== null) {

            $harmonized_condition_duration = $this->grace_on_interest_charged_condition_duration;
            $harmonized_grace_on_interest_charged = $this->grace_on_interest_charged;

            switch ($this->repayment_frequency_type) {
                // We set the harmonized interest rates and loan duration
                case 'days':
                    $harmonized_condition_duration = Utils::convertDurationToDays($this->grace_on_interest_charged_condition_duration, $this->grace_on_interest_charged_condition_duration_type);
                    $harmonized_grace_on_interest_charged = Utils::convertDurationToDays($this->grace_on_interest_charged, $this->grace_on_interest_charged_type);
                    break;
                case 'weeks':
                    $harmonized_condition_duration = Utils::convertDurationToWeeks($this->grace_on_interest_charged_condition_duration, $this->grace_on_interest_charged_condition_duration_type);
                    $harmonized_grace_on_interest_charged = Utils::convertDurationToWeeks($this->grace_on_interest_charged, $this->grace_on_interest_charged_type);
                    break;
                case 'months':
                    $harmonized_condition_duration = Utils::convertDurationToMonths($this->grace_on_interest_charged_condition_duration, $this->grace_on_interest_charged_condition_duration_type);
                    $harmonized_grace_on_interest_charged = Utils::convertDurationToMonths($this->grace_on_interest_charged, $this->grace_on_interest_charged_type);
                    break;
                case 'years':
                    $harmonized_condition_duration = Utils::convertDurationToYears($this->grace_on_interest_charged_condition_duration, $this->grace_on_interest_charged_condition_duration_type);
                    $harmonized_grace_on_interest_charged = Utils::convertDurationToYears($this->grace_on_interest_charged, $this->grace_on_interest_charged_type);
                    break;
            }

            // We check if the repayment schedule meets the condition
            if ($loanDuration <= $harmonized_condition_duration) {
                // schedule meets the condition;
                $response['qualifies'] = true;
            }
            $response['grace_on_interest_charged'] = $harmonized_grace_on_interest_charged;
        }
        return $response;
    }

    /**
     * Apply grace on interest repayment
     * Grace on interest repayment involves deferment of interest repayment to later time.
     *
     * @return $this
     */
    private function applyGraceOnInterestRepayment() {

        $noInstallments = sizeof($this->amortization_schedule);

        // check that the grace value is not more than half the number of installments
        if ($this->grace_on_interest_repayment !== null && $this->grace_on_interest_repayment <= ($noInstallments/2)) {

            for ($i = 0; $i < $this->grace_on_interest_repayment; $i ++) {

                // Get the interest
                $interest = $this->amortization_schedule[$i]['interest_repayment'];

                // Set the interest for this installment to zero
                $this->amortization_schedule[$i]['interest_repayment'] = 0;
                $this->amortization_schedule[$i]['total_amount_repayment'] -= $interest;

                // apply that interest to a later installment
                $this->amortization_schedule[($noInstallments-($i+1))]['interest_repayment'] += $interest;
                $this->amortization_schedule[($noInstallments-($i+1))]['total_amount_repayment'] += $interest;
            }
        }
        return $this;
    }

    /**
     * Add repayment dates to the amortization schedule
     *
     * @param string|null $firstInstallmentDate
     * @return $this
     * @throws \Exception
     */
    private function addRepaymentDates($firstInstallmentDate = null) {

        $firstInstallmentDate = !empty($firstInstallmentDate) ? Utils::formatDate($firstInstallmentDate, "Y-m-d") : null;
        $startDate = date("Y-m-d", time());

        $interval = 0;

        switch ($this->repayment_frequency_type) {
            // We set the harmonized interest rates and loan duration
            case 'days':
                $interval = 1;
                break;
            case 'weeks':
                $interval = 7;
                break;
            case 'months':
                $interval = 30;
                break;
            case 'years':
                $interval = 365;
                break;
        }

        $noInstallments = sizeof($this->amortization_schedule);


        // Payment is set to specific days of the week, we use that.
        if ($this->repayment_frequency_type == 'weeks' && !empty($this->repayment_week_days)) {

            $dates = Utils::getDatesForWeekDays($this->repayment_week_days, $noInstallments, $this->repayment_every, $firstInstallmentDate);
            for ($i = 0; $i < $noInstallments; $i ++) {
                // set the repayment date
                $this->amortization_schedule[$i]['repayment_date'] = $dates[$i];
            }
        }
        // Payment is set to specific dates of the month, we use that.
        elseif ($this->repayment_frequency_type == 'months' && !empty($this->repayment_month_dates)) {
            $dates = Utils::getDatesForMonthDates($this->repayment_month_dates, $noInstallments, $this->repayment_every, $firstInstallmentDate);
            for ($i = 0; $i < $noInstallments; $i ++) {
                // set the repayment date
                $this->amortization_schedule[$i]['repayment_date'] = $dates[$i];
            }
        }
        // Payment is set to specific month dates of a year, we use that.
        elseif ($this->repayment_frequency_type == 'years' && !empty($this->repayment_year_dates)) {
            $dates = Utils::getDatesForYearDates($this->repayment_year_dates, $noInstallments, $firstInstallmentDate);
            for ($i = 0; $i < $noInstallments; $i ++) {
                // set the repayment date
                $this->amortization_schedule[$i]['repayment_date'] = $dates[$i];
            }
        }
        // No restrictions on specific times are set; we use dates.
        else {

            // We set the first installment date.
            if (!empty($firstInstallmentDate)) {
                $this->amortization_schedule[0]['repayment_date'] = $firstInstallmentDate;
            } else {
                $this->amortization_schedule[0]['repayment_date'] = Utils::addDaysToDate($startDate, ceil($this->amortization_schedule[0]['period_length'] * $interval));;
            }

            // We add the remaining dates.
            for ($i = 1; $i < $noInstallments; $i ++) {
                // get the number of days to add
                $noDaysToAdd = ceil($this->amortization_schedule[$i]['period_length'] * $interval);
                // set the repayment date
                $this->amortization_schedule[$i]['repayment_date'] = Utils::addDaysToDate($this->amortization_schedule[$i-1]['repayment_date'], $noDaysToAdd);
            }

        }
        return $this;
    }

    /**
     * Generate repayment schedule
     *
     * @param string|null $firstInstallmentDate The first installment date.
     * @return $this
     */
    public function generate($firstInstallmentDate = null) {

        $this->harmonizeParameters();

        if ($this->interest_type == self::FLAT_INTEREST || $this->interest_type == self::ABSOLUTE_INTEREST) {
            $this->setFlatRateInterestSchedule();
        } elseif ($this->interest_type == self::INTEREST_ON_REDUCING_BALANCE) {

            if ($this->amortization_type == self::EVEN_PRINCIPAL_REPAYMENT) {
                $this->setReducingBalanceEvenPrincipalRepaymentSchedule();
            } elseif($this->amortization_type == self::EVEN_INSTALLMENT_REPAYMENT) {
                $this->setReducingBalanceEvenInstallmentRepaymentSchedule();
            }
        }

        // Apply grace on interest and principal
        $this->applyGraceOnInterestRepayment();

        // Apply repayment dates
        try {
            $this->addRepaymentDates($firstInstallmentDate);
        } catch (\Exception $e) {
            print $e->getMessage();
        }

        return $this;
    }
}
