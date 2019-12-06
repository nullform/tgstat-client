<?php

namespace Nullform\TGStatClient\Params;

/**
 * Parameters for GET tools/find-user-by-phone.
 *
 * @package Nullform\TGStatClient\Params
 */
class FindUserByPhoneParams extends AbstractParams
{
    /**
     * Full phone number.
     *
     * Example: 71234567890
     *
     * @var string
     * @example 71234567890
     */
    public $phone;
}