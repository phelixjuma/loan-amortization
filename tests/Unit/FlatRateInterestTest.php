<?php

namespace Phelix\LoanAmortization\Tests\Unit;


use Phelix\LoanAmortization\ScheduleGenerator;
use PHPUnit\Framework\TestCase;

class ScheduleGeneratorTest extends TestCase {


    /**
     * @var ScheduleGenerator $interestCalculator
     */
    protected $interestCalculator;

    /**
     * Set up the test case.
     */
    public function setUp(): void {
        $this->interestCalculator = new ScheduleGenerator();
    }

    /**
     * Test flat interest rate
     */
    public function testFlatInterestRate() {

        $this
            ->interestCalculator
            ->setPrincipal(10000)
            ->setInterestRate(7, "yearly", ScheduleGenerator::FLAT_INTEREST)
            ->setLoanDuration(8, "months")
            ->setRepayment(1,2, "months")
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }

    public function testFlatInterestRateWithGraceOnInterest() {

        $this
            ->interestCalculator
            ->setPrincipal(10000)
            ->setInterestRate(7, "yearly", ScheduleGenerator::FLAT_INTEREST)
            ->setLoanDuration(8, "months")
            ->setRepayment(1,2, "months")
            ->setGraceOnInterest(1, "years", 2, "months")
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }

    public function testFlatInterestRateWithGraceOnPrincipalRepayment() {

        $this
            ->interestCalculator
            ->setPrincipal(10000)
            ->setInterestRate(7, "yearly", ScheduleGenerator::FLAT_INTEREST)
            ->setLoanDuration(8, "months")
            ->setRepayment(1,2, "months")
            ->setGraceOnPrincipalRepayment(1)
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }

    public function testEvenPrincipalOnReducingBalance() {

        $this
            ->interestCalculator
            ->setPrincipal(10000)
            ->setInterestRate(7, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(8, "months")
            ->setRepayment(1,2, "months")
            ->setAmortization(ScheduleGenerator::EVEN_PRINCIPAL_REPAYMENT)
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }

    public function testEvenPrincipalOnReducingBalanceGraceOnPrincipal() {

        $this
            ->interestCalculator
            ->setPrincipal(10000)
            ->setInterestRate(7, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(8, "months")
            ->setRepayment(1,2, "months")
            ->setAmortization(ScheduleGenerator::EVEN_PRINCIPAL_REPAYMENT)
            ->setGraceOnPrincipalRepayment(1)
            ->setGraceOnInterestRepayment(0)
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }

    public function testEvenPrincipalOnReducingBalanceGraceOnInterest() {

        $this
            ->interestCalculator
            ->setPrincipal(50000)
            ->setInterestRate(12, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(1, "weeks")
            ->setRepayment(1,3, "days")
            ->setAmortization(ScheduleGenerator::EVEN_PRINCIPAL_REPAYMENT)
            ->setGraceOnInterest(2, "weeks", 5, "days")
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }



    public function testEvenInstallmentOnReducingBalance() {

        $this
            ->interestCalculator
            ->setPrincipal(10000)
            ->setInterestRate(7, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(8, "months")
            ->setRepayment(1,2, "months")
            ->setAmortization(ScheduleGenerator::EVEN_INSTALLMENT_REPAYMENT)
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }

    public function testEvenInstallmentOnReducingBalanceGraceOnInterest() {

        $this
            ->interestCalculator
            ->setPrincipal(10000)
            ->setInterestRate(7, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(8, "months")
            ->setRepayment(1,2, "months")
            ->setAmortization(ScheduleGenerator::EVEN_INSTALLMENT_REPAYMENT)
            ->setGraceOnInterest(1, "weeks", 4, "days")
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }

    public function testEvenInstallmentOnReducingBalanceGraceOnPrincipalRepayment() {

        $this
            ->interestCalculator
            ->setPrincipal(10000)
            ->setInterestRate(7, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(8, "months")
            ->setRepayment(1,1, "months")
            ->setAmortization(ScheduleGenerator::EVEN_INSTALLMENT_REPAYMENT)
            ->setGraceOnPrincipalRepayment(1)
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }

    public function testEvenInstallmentOnReducingBalanceGraceOnInterestRepayment() {

        $this
            ->interestCalculator
            ->setPrincipal(50000)
            ->setInterestRate(12, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(1, "weeks")
            ->setRepayment(1,3, "days")
            ->setAmortization(ScheduleGenerator::EVEN_INSTALLMENT_REPAYMENT)
            ->setGraceOnInterestRepayment(1)
            ->generate();

        print "\nInterest = {$this->interestCalculator->interest} \n";
        print "Effective Interest Rate = {$this->interestCalculator->effective_interest_rate} \n";
        print "Total Repayable amount = {$this->interestCalculator->amount} \n";
        print "#Installments = {$this->interestCalculator->no_installments} \n";
        print "Repayment Frequency = {$this->interestCalculator->repayment_frequency} \n";
        print "Harmonized Duration = {$this->interestCalculator->harmonized_duration} \n";
        print "Harmonized Interest rate = {$this->interestCalculator->harmonized_interest_rate} \n";

        print "Amortization Schedule: \n";

        print_r($this->interestCalculator->amortization_schedule);
    }

}