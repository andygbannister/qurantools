<?php

/**
 * Used for testing Goggle reCAPTCHA responses in unit tests
 */

use AspectMock\Test as test;

trait ReCaptchaHelper
{
    // $properties should contain a mocked ReCaptcha\Response object
    public function mockReCaptchaReCaptcha(array $properties = [])
    {
        return test::double('\ReCaptcha\ReCaptcha', [
            '__construct' => function ($secret) {
                $this->customProperty = 'something'; // so that we can tell if the mock is in use
            },
            'verify' => $properties['response']
        ]);
    }

    public function mockReCaptchaResponse(array $properties = [])
    {
        if (!isset($properties['isSuccess']))
        {
            $properties['isSuccess'] = true;
        }

        // mock a ReCaptcha response without hitting Google
        return test::double(new ReCaptcha\Response($properties['isSuccess']), [
            'isSuccess' => $properties['isSuccess']
        ]);
    }
}
