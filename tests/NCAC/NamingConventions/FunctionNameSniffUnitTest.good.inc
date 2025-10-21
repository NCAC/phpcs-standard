<?php
/**
 * Test file with VALID function names for FunctionNameSniff
 *
 * This file contains properly formatted global functions that should
 * NOT trigger any errors from the FunctionNameSniff.
 */

// Valid snake_case global functions
function calculate_total_sum()
{
    return 42;
}

function process_user_data()
{
    return true;
}

function validate_email_address()
{
    return false;
}

function get_current_timestamp()
{
    return time();
}

function format_currency_value()
{
    return '$100.00';
}

// Functions with numbers (snake_case)
function generate_md5_hash()
{
    return 'hash';
}

function convert_utf8_string()
{
    return 'converted';
}

// Class methods should be ignored by FunctionNameSniff
class ValidClass
{
    // These methods use camelCase but should NOT trigger errors
    // because FunctionNameSniff only applies to global functions
    public function getUserName()
    {
        return 'user';
    }

    private function calculateTotal()
    {
        return 100;
    }

    protected function validateInput()
    {
        return true;
    }
}

// Trait methods should also be ignored
trait ValidTrait
{
    public function processData()
    {
        return 'processed';
    }
}
