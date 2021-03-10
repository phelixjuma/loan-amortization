Loan Amortization SDK by Phelix Juma
================================================

This is a PHP SDK for calculating loan amortization
Calculates the total interest, repayment amount as well as the repayment schedule

Types of Interests Included
===========================
- Flat Rate Interest
   - This is interest type where the interest rate for each period is computed as a percentage of the loan principal amount
   - The loan interest is constant over the entire repayment period
- Interest on Reducing Balance
   - In this case, instead of computing interest on the principal amount, the interest for each period is calculated as a percentage of the balance on principal repayments at at that time
   - The user thus pays less interest in later periods when their loan balance reduces as compared in the amount they'd pay at the start of the loan repayment schedle

Types of Amortization Included
==============================

- Loan amortization basically describes the repayment schedule spread over a given duration
- The following types are supported:
   - **Even Principal Repayment**: This is a repayment plan where the principal repayment remains constant over the entire repayment duration
   - **Even Installment Repayment**: This is a repayment plan where the total repayment remains constant over the entire repayment duration
- Loan amortization types depend on the interest type:
  - **Flat Rate Interest**: Amortization type doesn't really matter. The repayments will have both equal installment and principal and interest repayments for the entire repayment duration
  - **Interest on Reducing Balance**: Supports both types. Either of them can be used at a time

Grace on Repayments
====================

The package supports three types of grace on repayments
- **Grace on Principal Repayments**: When applied, the principal repayments for the set period are deferred to a later period. 
For instance, if grace on principal repayments is set to 2, then the first two installments will have principal repayments
of 0 and the principal amounts recouped through the remaining installments  
- **Grace on Interest Repayments**: When applied, the interest repayments for the set period are deferred to a later period.
For instance, if grace on interest repayments is set to 2, then *the first two installments* will have the interest repayments
of 0 and the interests will be collected in the *last two installments*.
- **Grace on Interest**: This is a conditional grace applied to interest collected. If set, the interest for the specified
period is zero-rated ie no interest is paid for that period. For instance, one can set if a loan is repaid within a year, 
then the first two months interests will be zero-rated hence the user only pays the principal amounts for those first two 
months.    


Installation
============

    composer require phelix/LoanAmortization

How To test
===========

the test is more of a visualization tool than an actual test.

    vendor/bin/phpunit test


# Documentation

for more details on amortization, check [Loan Amortization](https://www.extension.iastate.edu/agdm/wholefarm/html/c5-93.html)


## 1. Flat Interest Rate

Loan Details:
- Loan Principal = 50,000.00
- Interest Rate = 12% per year
- Loan duration = 1 week
- Repayment Schedule = 1 repayment every 2 days

```php

<?php 

    use Phelix\LoanAmortization\ScheduleGenerator;

    $interestCalculator = new ScheduleGenerator();
    
    $interestCalculator
                ->setPrincipal(50000)
                ->setInterestRate(12, "yearly", ScheduleGenerator::FLAT_INTEREST) // note the interest type
                ->setLoanDuration(1, "weeks")
                ->setRepayment(1,2, "days")
                ->generate();
    
    print "\nTotal Interest = {$interestCalculator->interest} \n";
    print "Effective Interest Rate = {$interestCalculator->effective_interest_rate} \n";
    print "Total Repayable amount = {$interestCalculator->amount} \n";
    print "Number of Installments = {$interestCalculator->no_installments} \n";
    print "Repayment Frequency = {$interestCalculator->repayment_frequency} \n";
    
    print "Amortization Schedule: \n";
    
    print_r($this->interestCalculator->amortization_schedule);
    
    // Sample Schedule Response:
    
    Array
    (
        [0] => Array
            (
                [principal_repayment] => 14285.71,
                [interest_repayment] => 32.88,
                [total_amount_repayment] => 14318.59,
                [principal_repayment_balance] => 35714.29
            ),
    
        [1] => Array
            (
                [principal_repayment] => 14285.71,
                [interest_repayment] => 32.88,
                [total_amount_repayment] => 14318.59,
                [principal_repayment_balance] => 21428.58,
            ),
    
        [2] => Array
            (
                [principal_repayment] => 14285.71,
                [interest_repayment] => 32.88,
                [total_amount_repayment] => 14318.59,
                [principal_repayment_balance] => 7142.87,
            ),
    
        [3] => Array
            (
                [principal_repayment] => 7142.87,
                [interest_repayment] => 16.43,
                [total_amount_repayment] => 7159.3,
                [principal_repayment_balance] => 0
            )
    
    );

```


## 2. Reducing Balance - Equal Principal Repayments

Loan Details:
- Loan Principal = 50,000.00
- Interest Rate = 12% per year
- Loan duration = 1 week
- Repayment Schedule = 1 repayment every 2 days

```php

<?php 

    use Phelix\LoanAmortization\ScheduleGenerator;

    $interestCalculator = new ScheduleGenerator();
    
    $interestCalculator
                ->setPrincipal(50000)
                ->setInterestRate(12, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE) // note the interest type
                ->setLoanDuration(1, "weeks")
                ->setRepayment(1,2, "days")
                ->setAmortization(ScheduleGenerator::EVEN_PRINCIPAL_REPAYMENT) // note the amortization type
                ->generate();
    
    print "\nTotal Interest = {$interestCalculator->interest} \n";
    print "Effective Interest Rate = {$interestCalculator->effective_interest_rate} \n";
    print "Total Repayable amount = {$interestCalculator->amount} \n";
    print "Number of Installments = {$interestCalculator->no_installments} \n";
    print "Repayment Frequency = {$interestCalculator->repayment_frequency} \n";
    
    print "Amortization Schedule: \n";
    
    print_r($this->interestCalculator->amortization_schedule);
    
     // Sample Response
     Array
     (
         [0] => Array
             (
                 [principal_repayment] => 21428.57,
                 [interest_repayment] => 49.32,
                 [total_amount_repayment] => 21477.89,
                 [principal_repayment_balance] => 28571.43
             ),
     
         [1] => Array
             (
                 [principal_repayment] => 21428.57,
                 [interest_repayment] => 28.18,
                 [total_amount_repayment] => 21456.75,
                 [principal_repayment_balance] => 7142.86
             ),
     
         [2] => Array
             (
                 [principal_repayment] => 7142.86,
                 [interest_repayment] => 7.05,
                 [total_amount_repayment] => 7149.91,
                 [principal_repayment_balance] => 0
             )
     
     );


```

## 3. Reducing Balance - Equal Installment Repayments

Loan Details:
- Loan Principal = 50,000.00
- Interest Rate = 12% per year
- Loan duration = 1 week
- Repayment Schedule = 1 repayment every 2 days

```php

<?php 

    use Phelix\LoanAmortization\ScheduleGenerator;

    $interestCalculator = new ScheduleGenerator();
    
    $interestCalculator
                ->setPrincipal(50000)
                ->setInterestRate(12, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE) // note the interest type
                ->setLoanDuration(1, "weeks")
                ->setRepayment(1,2, "days")
                ->setAmortization(ScheduleGenerator::EVEN_INSTALLMENT_REPAYMENT) // note the amortization type
                ->generate();
    
    print "\nTotal Interest = {$interestCalculator->interest} \n";
    print "Effective Interest Rate = {$interestCalculator->effective_interest_rate} \n";
    print "Total Repayable amount = {$interestCalculator->amount} \n";
    print "Number of Installments = {$interestCalculator->no_installments} \n";
    print "Repayment Frequency = {$interestCalculator->repayment_frequency} \n";
    
    print "Amortization Schedule: \n";
    
    print_r($this->interestCalculator->amortization_schedule);
    
    // Sample Response
    Array
    (
        [0] => Array
            (
                [principal_repayment] => 16650.23,
                [interest_repayment] => 49.32,
                [total_amount_repayment] => 16699.55,
                [principal_repayment_balance] => 33349.77
            ),
    
        [1] => Array
            (
                [principal_repayment] => 16666.66,
                [interest_repayment] => 32.89,
                [total_amount_repayment] => 16699.55,
                [principal_repayment_balance] => 16683.11,
            ),
    
        [2] => Array
            (
                [principal_repayment] => 16683.11,
                [interest_repayment] => 16.45,
                [total_amount_repayment] => 16699.55,
                [principal_repayment_balance] => 0
            )
    
    );

```

## 4. Grace on Principal Payments

Loan Details:
- Loan Principal = 50,000.00
- Interest Rate = 12% per year
- Loan duration = 1 week
- Repayment Schedule = 1 repayment every 2 days

```php

<?php 

    use Phelix\LoanAmortization\ScheduleGenerator;

    $interestCalculator = new ScheduleGenerator();
    
    $interestCalculator
                ->setPrincipal(50000)
                ->setInterestRate(12, "yearly", ScheduleGenerator::INTEREST_ON_REDUCING_BALANCE) // note the interest type
                ->setLoanDuration(1, "weeks")
                ->setRepayment(1,2, "days")
                ->setAmortization(ScheduleGenerator::EVEN_INSTALLMENT_REPAYMENT) // note the amortization type
                ->setGraceOnPrincipalRepayment(2) // note the grace period. First two principals' payment deferred
                ->setGraceOnInterestRepayment(1) // note the grace period. First month interest payment is deferred
                ->setGraceOnInterest(5, "days", 2, "days") // not how this is set. It reads: If you repay entire loan within 5 days, then no interest will be charged for the first two days
                ->generate();
    
    print "\nTotal Interest = {$interestCalculator->interest} \n";
    print "Effective Interest Rate = {$interestCalculator->effective_interest_rate} \n";
    print "Total Repayable amount = {$interestCalculator->amount} \n";
    print "Number of Installments = {$interestCalculator->no_installments} \n";
    print "Repayment Frequency = {$interestCalculator->repayment_frequency} \n";
    
    print "Amortization Schedule: \n";
    
    print_r($this->interestCalculator->amortization_schedule);

```


Credits
=======

- Phelix Juma (jumaphelix@kuzalab.com)
