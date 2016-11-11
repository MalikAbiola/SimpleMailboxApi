<?php

/**
 * Created by Malik Abiola.
 * Date: 11/11/2016
 * Time: 01:47
 * IDE: PhpStorm
 */

use App\Validators\RequestsValidator;

class RequestsValidatorTest extends TestCase
{
    public function testValidateMailUpdateRequestPasses()
    {
        $this->assertTrue(
            RequestsValidator::validateMailUpdateRequest(
                $this->getTestRequest(
                    "PATCH",
                    [
                        'read' => true,
                        'archive' => true
                    ]
                )
            )->passes()
        );
    }

    public function testValidateMailUpdateRequestFails()
    {
        $this->assertFalse(
            RequestsValidator::validateMailUpdateRequest(
                $this->getTestRequest(
                    "PATCH",
                    [
                        'read' => 'thisiswrong',
                    ]
                )
            )->passes()
        );
    }
}
