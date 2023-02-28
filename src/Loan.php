<?php


/**
 * This is main app class file
 * @author Phelix Juma <jumaphelix@kuzalab.com>
 * @copyright (c) 2021, Kuza Lab
 * @package Kuzalab
 */

namespace Phelix\LoanAmortization;

/**
 * Main application class.
 */
class Loan {

    const EVEN_PRINCIPAL_REPAYMENT = 'even_principal_repayment';
    const EVEN_INSTALLMENT_REPAYMENT = 'even_installment_repayment';
    const FLAT_INTEREST = 'flat_interest';
    const ABSOLUTE_INTEREST = 'absolute_interest';
    const INTEREST_ON_REDUCING_BALANCE = 'reducing_balance_interest';

    /**
     * The loan principal amount
     * @var double $principal
     */
    protected $principal;

    /**
     * The length of loan term
     * Eg: "10" months
     * @var integer $loan_term_frequency
     */
    protected $loan_term_duration;

    /**
     * The type of loan duration eg "months", "weeks" et al
     * @var string $loan_term_duration_type
     */
    protected $loan_term_duration_type;

    /**
     * Number of installments to repay.
     * Example: "10" repayments Every 12 Weeks
     * @var integer $duration
     */
    protected $no_periodical_repayments;

    /**
     * Frequency of repayment
     * Example: 10 repayments every "12" weeks
     * @var integer $repayment_every
     */
    protected $repayment_every;

    /**
     * The type of frequency used for repayments eg days, weeks, months, years.
     * Example: 10 repayments every 12 "weeks"
     * @var string $loan_term_frequency_type
     */
    protected $repayment_frequency_type;

    /**
     * Actual repayment frequency.
     * Calculated from the no of periodical repayments and repayment every.
     * If we have:
     *  - 1 repayment every 2 months, then frequency is 0.5
     *  - 2 repayments every 1 month, then the frequency is 2
     * @var double $repayment_frequency
     */
    public $repayment_frequency;

    /**
     * This puts a restriction so that repayments are done on specific days of the week.
     * Used to modify the installment dates to restrict them to specific week days.
     * This only works with weekly repayments.
     * @var string $repayment_week_days
     */
    public $repayment_week_days;

    /**
     * This puts a restriction so that repayments are done on specific dates of the month.
     * Used to modify the installment dates to restrict them to specific dates of the month.
     * This only works with monthly repayments.
     *
     * @var numeric $repayment_month_dates
     */
    public $repayment_month_dates;

    /**
     * This puts a restriction so that repayments are done on specific month dates of the year.
     * Used to modify the installment dates to restrict them to specific month-dates of the year.
     * This only works with yearly repayments.
     * Format is "d/m" eg "2/3" is 2nd of March every year.
     *
     * @var string $repayment_year_dates
     */
    public $repayment_year_dates;

    /**
     * Total number of installments.
     * Dependent on the repayment details above
     * @var integer $no_installments
     */
    public $no_installments;

    /**
     * Loan interest value. Used where no percentage is set but just an absolute amount for the interest
     * @var double $interest_rate
     */
    protected $absolute_interest_amount;

    /**
     * Loan interest rate (%)
     * @var double $interest_rate
     */
    protected $interest_rate_per_period;

    /**
     * The frequency of the applied interest eg daily, weekly, monthly, yearly
     * Example: e.g. 12% "Per year" on Declining Balance
     * @var string $interest_rate_frequency_type
     */
    protected $interest_rate_frequency_type;

    /**
     * Interest type. Either of 'flat' or 'reducing balance'
     * @var string $interest_type
     */
    protected $interest_type;

    /**
     * Type of loan repayment amortization
     * Can be either of "Even principal" or "Even installments"
     * @var string $amortization_type
     */
    protected $amortization_type;

    /**
     * Represents the number of repayment periods that should be interest-free
     * Eg: No interest for the first "2" repayment periods.
     * @var integer $grace_on_interest_charged
     */
    protected $grace_on_interest_charged;

    /**
     * Duration type for the period of grace on interest
     * Can be days,weeks, months, years
     * @var string $grace_on_interest_charged_type
     */
    protected $grace_on_interest_charged_type;

    /**
     * The condition on repayment duration on which the grace on interest applies
     * Eg: If you repay within "2" months, no interest for the first 2 months. (basically means zero interest)
     * @var integer $grace_on_interest_condition_duration
     */
    protected $grace_on_interest_charged_condition_duration;

    /**
     * The condition duration type eg days, weeks, months, years
     * Eg: If you repay within 2 "months", no interest for the first 2 months. (basically means zero interest)
     * @var string $grace_on_interest_condition_duration_type
     */
    protected $grace_on_interest_charged_condition_duration_type;

    /**
     * Represents the number of repayment periods that grace should apply to the interest component of a repayment period.
     * Interest is still calculated but offset to later repayment periods.
     * @var integer $grace_on_interest_repayment
     */
    protected $grace_on_interest_repayment;

    /**
     * Represents the number of repayment periods that grace should apply to the principal component of a repayment period.
     * Interest is still calculated but offset to later repayment periods.
     * @var integer $grace_on_principal_repayment
     */
    protected $grace_on_principal_repayment;

    /**
     * Holds the debug level
     * @var double $effective_interest_rate
     */
    public $effective_interest_rate;

    /**
     * Bearer token to use for all requests
     * @var double  $interest
     */
    public $interest;

    /**
     * The total repayable loan amount
     *
     * @var double $amount
     */
    public $amount;

    /**
     * This is the actual schedule the user uses to repay the loan based on their amortization frequency
     * @var array $amortization_schedule
     */
    public $amortization_schedule = [];


    /**
     * Loan constructor.
     */
    public function __construct() {
    }

    /**
     * Set principal
     *
     * @param $principal
     * @return $this
     */
    public function setPrincipal($principal) {
        $this->principal = $principal;

        return $this;
    }

    /**
     * Set interest rate
     *
     * @param $interestRate
     * @param $frequencyType
     * @param $type
     * @return $this
     */
    public function setInterestRate($interestRate, $frequencyType, $type) {

        $this->interest_rate_per_period = $interestRate;
        $this->interest_rate_frequency_type = $frequencyType;
        $this->interest_type = $type;

        return $this;
    }

    /**
     * @param $interestAmount
     * @return $this
     */
    public function setAbsoluteInterest($interestAmount): Loan
    {

        $this->absolute_interest_amount = $interestAmount;
        $this->interest_type = self::ABSOLUTE_INTEREST;

        return $this;
    }

    /**
     * Set duration details
     *
     * @param $duration
     * @param $durationType
     * @return $this
     */
    public function setLoanDuration($duration, $durationType) {

        $this->loan_term_duration = $duration;
        $this->loan_term_duration_type = $durationType;

        return $this;
    }

    /**
     * Set repayment details
     *
     * @param $noRepayments
     * @param $frequency
     * @param $frequencyType
     * @return $this
     */
    public function setRepayment($noRepayments, $frequency, $frequencyType) {

        $this->no_periodical_repayments = $noRepayments;
        $this->repayment_every = $frequency;
        $this->repayment_frequency_type = $frequencyType;

        $this->repayment_frequency = $this->no_periodical_repayments / $this->repayment_every;

        return $this;
    }

    /**
     * Set restrictions on installments to fall within specific times.
     *
     * @param $repaymentWeekDays
     * @param $repaymentMonthDates
     * @param $repaymentYearDates
     * @return $this
     */
    public function tieInstallmentsToSpecificTimes($repaymentWeekDays=null, $repaymentMonthDates=null, $repaymentYearDates=null) {

        $this->repayment_week_days = $repaymentWeekDays;
        $this->repayment_month_dates = $repaymentMonthDates;
        $this->repayment_year_dates = $repaymentYearDates;

        return $this;
    }

    /**
     * Set amortization type
     * @param $type
     * @return $this
     */
    public function setAmortization($type) {

        $this->amortization_type = $type;

        return $this;
    }

    /**
     * Set grace on interest charged
     *
     * @param $conditionDuration
     * @param $conditionDurationType
     * @param $graceDuration
     * @param $graceDurationType
     * @return $this
     */
    public function setGraceOnInterest($conditionDuration, $conditionDurationType,$graceDuration, $graceDurationType) {

        $this->grace_on_interest_charged_condition_duration = $conditionDuration;
        $this->grace_on_interest_charged_condition_duration_type = $conditionDurationType;
        $this->grace_on_interest_charged = $graceDuration;
        $this->grace_on_interest_charged_type = $graceDurationType;

        return $this;
    }

    /**
     * Set grace on interest repayment
     * Defines the first number of installments that are deferred to a later repayment
     *
     * @param $noInstallmentsGraced
     *  @return $this
     */
    public function setGraceOnInterestRepayment($noInstallmentsGraced) {
        $this->grace_on_interest_repayment = $noInstallmentsGraced;

        return $this;
    }

    /**
     * Set grace on interest repayment
     * Defines the first number of installments that are deferred to a later repayment
     *
     * @param $noInstallmentsGraced
     * @return $this
     */
    public function setGraceOnPrincipalRepayment($noInstallmentsGraced) {

        $this->grace_on_principal_repayment = $noInstallmentsGraced;

        return $this;
    }

    /**
     * Check if repayment is even principal
     * @return bool
     */
    public function isEvenPrincipalAmortization() {
        return $this->amortization_type == self::EVEN_PRINCIPAL_REPAYMENT;
    }

    /**
     * Check if repayment is even amortization
     * @return bool
     */
    public function isEvenInstallmentAmortization() {
        return $this->amortization_type == self::EVEN_INSTALLMENT_REPAYMENT;
    }

    /**
     * Check if interest is a flat rate interest
     *
     * @return bool
     */
    public function isFlatInterest() {
        return $this->interest_type == self::FLAT_INTEREST;
    }

    /**
     * Check if interest is on reducing balance
     *
     * @return bool
     */
    public function isInterestOnReducingBalance() {
        return $this->interest_type == self::INTEREST_ON_REDUCING_BALANCE;
    }

}
