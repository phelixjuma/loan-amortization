<?php

namespace Phelix\LoanAmortization\Tests\Unit;


use Phelix\LoanAmortization\Loan;
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
            ->setInterestRate(30, "monthly", Loan::FLAT_INTEREST)
            ->setLoanDuration(1, "months")
            ->setRepayment(1,1, "weeks")
            ->tieInstallmentsToSpecificTimes("tuesday", null, null)
            ->generate("");

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

    public function _testAbsoluteInterestRate() {

        $this
            ->interestCalculator
            ->setPrincipal(10000)
            ->setAbsoluteInterest(2000)
            ->setLoanDuration(3, "years")
            ->setRepayment(1,1, "years")
            ->tieInstallmentsToSpecificTimes(null, null, "3-31")
            ->generate("");

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

    public function _testFlatInterestRateWithGraceOnInterest() {

        $this
            ->interestCalculator
            ->setPrincipal(1000)
            ->setInterestRate(10, "monthly", Loan::FLAT_INTEREST)
            ->setLoanDuration(2, "months")
            ->setRepayment(1,2, "weeks")
            ->setGraceOnInterest(2, "months", 2, "weeks")
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

    public function _testFlatInterestRateWithGraceOnPrincipalRepayment() {

        $this
            ->interestCalculator
            ->setPrincipal(1000)
            ->setInterestRate(10, "monthly", ScheduleGenerator::FLAT_INTEREST)
            ->setLoanDuration(1, "months")
            ->setRepayment(1,1, "weeks")
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

    public function _testEvenPrincipalOnReducingBalance() {

        $this
            ->interestCalculator
            ->setPrincipal(1000)
            ->setInterestRate(10, "monthly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(1, "months")
            ->setRepayment(1,1, "weeks")
            ->setAmortization(Loan::EVEN_PRINCIPAL_REPAYMENT)
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

    public function _testEvenPrincipalOnReducingBalanceGraceOnPrincipal() {

        $this
            ->interestCalculator
            ->setPrincipal(1000)
            ->setInterestRate(10, "monthly", Loan::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(1, "months")
            ->setRepayment(1,1, "weeks")
            ->setAmortization(Loan::EVEN_PRINCIPAL_REPAYMENT)
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

    public function _testEvenPrincipalOnReducingBalanceGraceOnInterest() {

        $this
            ->interestCalculator
            ->setPrincipal(1000)
            ->setInterestRate(10, "monthly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(1, "months")
            ->setRepayment(1,1, "weeks")
            ->setAmortization(Loan::EVEN_PRINCIPAL_REPAYMENT)
            ->setGraceOnInterest(2, "months", 1, "weeks")
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

    public function _testEvenInstallmentOnReducingBalance() {

        $this
            ->interestCalculator
            ->setPrincipal(1000)
            ->setInterestRate(10, "monthly", Loan::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(1, "months")
            ->setRepayment(1,1, "weeks")
            ->setAmortization(Loan::EVEN_INSTALLMENT_REPAYMENT)
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

    public function _testEvenInstallmentOnReducingBalanceGraceOnInterest() {

        $this
            ->interestCalculator
            ->setPrincipal(1000)
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

    public function _testEvenInstallmentOnReducingBalanceGraceOnPrincipalRepayment() {

        $this
            ->interestCalculator
            ->setPrincipal(1000)
            ->setInterestRate(10, "monthly", Loan::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(9, "months")
            ->setRepayment(1,1, "months")
            ->setAmortization(Loan::EVEN_INSTALLMENT_REPAYMENT)
            ->setGraceOnPrincipalRepayment(0)
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

    public function _testEvenInstallmentOnReducingBalanceGraceOnInterestRepayment() {

        $this
            ->interestCalculator
            ->setPrincipal(1000)
            ->setInterestRate(10, "monthly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE)
            ->setLoanDuration(1, "months")
            ->setRepayment(1,1, "weeks")
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
